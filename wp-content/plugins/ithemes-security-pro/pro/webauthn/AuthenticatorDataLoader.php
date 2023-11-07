<?php

namespace iThemesSecurity\WebAuthn;

use iThemesSecurity\Strauss\Assert\Assert;
use iThemesSecurity\Strauss\CBOR\CBORObject;
use iThemesSecurity\Strauss\CBOR\DecoderInterface;
use iThemesSecurity\Strauss\CBOR\MapObject;
use iThemesSecurity\Strauss\CBOR\StringStream;
use iThemesSecurity\WebAuthn\DTO\AttestedCredentialData;
use iThemesSecurity\WebAuthn\DTO\AuthenticatorData;
use iThemesSecurity\WebAuthn\DTO\BinaryString;

final class AuthenticatorDataLoader {
	private const FLAG_AT = 0b01000000; // Attestation data is present.

	/** @var DecoderInterface */
	private $decoder;

	public function __construct( DecoderInterface $decoder ) { $this->decoder = $decoder; }

	public function load( BinaryString $auth_data ): AuthenticatorData {
		// See https://w3c.github.io/webauthn/#fig-attStructs for the structure.
		$auth_data_stream = new StringStream( $auth_data->get_binary() );
		$rp_id_hash       = $auth_data_stream->read( 32 );
		$flags            = $auth_data_stream->read( 1 );
		$signature_count  = $auth_data_stream->read( 4 );
		$signature_count  = unpack( 'N', $signature_count );

		$attested_credential_data = null;

		if ( 0 !== ( ord( $flags ) & self::FLAG_AT ) ) {
			$aaguid = $auth_data_stream->read( 16 );
			$aaguid = uuid_unparse( $aaguid );

			$credential_length = $auth_data_stream->read( 2 );
			$credential_length = unpack( 'n', $credential_length );
			$credential_id     = $auth_data_stream->read( $credential_length[1] );

			/** @var CBORObject|MapObject $credential_public_key */
			$credential_public_key = $this->decoder->decode( $auth_data_stream );
			Assert::that( $credential_public_key )->isInstanceOf( MapObject::class );

			$attested_credential_data = new AttestedCredentialData(
				$aaguid,
				new BinaryString( $credential_id ),
				$credential_public_key->normalize()
			);
		}

		return new AuthenticatorData(
			$auth_data->get_binary(),
			new BinaryString( $rp_id_hash ),
			$flags,
			$signature_count[1],
			$attested_credential_data
		);
	}
}
