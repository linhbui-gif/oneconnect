<?php

namespace iThemesSecurity\WebAuthn;

use iThemesSecurity\Strauss\Assert\Assert;
use iThemesSecurity\Strauss\CBOR\CBORObject;
use iThemesSecurity\Strauss\CBOR\DecoderInterface;
use iThemesSecurity\Strauss\CBOR\Normalizable;
use iThemesSecurity\Strauss\CBOR\StringStream;
use iThemesSecurity\WebAuthn\DTO\AttestationObject;
use iThemesSecurity\WebAuthn\DTO\AttestationStatement;
use iThemesSecurity\WebAuthn\DTO\BinaryString;

final class AttestationObjectLoader {

	/** @var DecoderInterface */
	protected $decoder;

	/** @var AuthenticatorDataLoader */
	protected $auth_data_loader;

	public function __construct(
		DecoderInterface $decoder,
		AuthenticatorDataLoader $auth_data_loader
	) {
		$this->decoder          = $decoder;
		$this->auth_data_loader = $auth_data_loader;
	}

	public function load( BinaryString $data ): AttestationObject {
		$stream = new StringStream( $data->get_binary() );

		/** @var CBORObject&Normalizable $parsed */
		$parsed = $this->decoder->decode( $stream );

		Assert::that( $parsed )->isInstanceOf(
			Normalizable::class,
			'parsed class "%s" was expected to be instanceof of "%s" but is not.'
		);

		$decoded_attestation = $parsed->normalize();

		Assert::that( $decoded_attestation, 'Attestation hydration does not contain "%s".' )
		      ->isArray()
		      ->keyExists( 'authData' )
		      ->keyExists( 'fmt' )
		      ->keyExists( 'attStmt' );

		// Right now, we are not requesting any attestation from the Authenticator.
		// This can be updated in the future to handle provided data.
		$attestation_statement = new AttestationStatement(
			$decoded_attestation['fmt'],
			$decoded_attestation['attStmt'],
			AttestationStatement::TYPE_NONE
		);

		$authenticator_data = $this->auth_data_loader->load(
			new BinaryString( $decoded_attestation['authData'] )
		);

		return new AttestationObject(
			$data->get_binary(),
			$attestation_statement,
			$authenticator_data
		);
	}
}
