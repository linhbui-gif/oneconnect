<?php

namespace iThemesSecurity\WebAuthn;

use iThemesSecurity\Strauss\CBOR\Decoder;
use iThemesSecurity\Strauss\CBOR\DecoderInterface;
use iThemesSecurity\Strauss\Cose\Algorithm\Manager as CoseManager;
use iThemesSecurity\Strauss\Cose\Algorithm\Signature\ECDSA\ES256;
use iThemesSecurity\Strauss\Cose\Algorithm\Signature\RSA\RS256;
use iThemesSecurity\Strauss\Pimple\Container;

return static function ( Container $c ) {
	$c['module.webauthn.files'] = [
		'active.php' => Module::class,
	];

	$c[ Module::class ] = static function ( Container $c ) {
		return new Module(
			$c[ Login::class ]
		);
	};

	$c[ Login::class ] = static function () {
		return new Login();
	};

	// -- Services -- //

	$c[ PublicKeyCredentialRpEntity_Factory::class ] = static function () {
		return new PublicKeyCredentialRpEntity_Factory_Default();
	};

	$c[ PublicKeyCredentialUserEntity_Factory::class ] = static function () {
		return new PublicKeyCredentialUserEntity_Factory_Database( $GLOBALS['wpdb'] );
	};

	$c[ PublicKeyCredential_Record_Repository::class ] = static function () {
		return new PublicKeyCredential_Record_Repository_Database( $GLOBALS['wpdb'] );
	};

	$c[ PublicKeyCredentialCreationOptions_Factory::class ] = static function ( Container $c ) {
		return new PublicKeyCredentialCreationOptions_Factory_Default(
			$c[ PublicKeyCredentialUserEntity_Factory::class ],
			$c[ PublicKeyCredentialRpEntity_Factory::class ],
			$c[ PublicKeyCredential_Record_Repository::class ],
			$c[ CoseManager::class ]
		);
	};

	$c[ PublicKeyCredentialRequestOptions_Factory::class ] = static function ( Container $c ) {
		return new PublicKeyCredentialRequestOptions_Factory_Default(
			$c[ PublicKeyCredentialUserEntity_Factory::class ],
			$c[ PublicKeyCredentialRpEntity_Factory::class ],
			$c[ PublicKeyCredential_Record_Repository::class ]
		);
	};

	$c[ Session_Storage::class ] = static function () {
		return new Session_Storage_Opaque_Tokens();
	};

	$c[ DecoderInterface::class ] = static function () {
		return Decoder::create();
	};

	$c[ AuthenticatorDataLoader::class ] = static function ( Container $c ) {
		return new AuthenticatorDataLoader(
			$c[ DecoderInterface::class ]
		);
	};

	$c[ AttestationObjectLoader::class ] = static function ( Container $c ) {
		return new AttestationObjectLoader(
			$c[ DecoderInterface::class ],
			$c[ AuthenticatorDataLoader::class ]
		);
	};

	$c[ RegistrationCeremony::class ] = static function ( Container $c ) {
		return new RegistrationCeremony(
			$c[ PublicKeyCredentialRpEntity_Factory::class ],
			$c[ AttestationObjectLoader::class ],
			$c[ PublicKeyCredential_Record_Repository::class ]
		);
	};

	$c[ AuthenticationCeremony::class ] = static function ( Container $c ) {
		return new AuthenticationCeremony(
			$c[ PublicKeyCredentialRpEntity_Factory::class ],
			$c[ AuthenticatorDataLoader::class ],
			$c[ PublicKeyCredential_Record_Repository::class ],
			$c[ CoseManager::class ]
		);
	};

	$c[ Verified_Credential_Tokens::class ] = static function ( Container $c ) {
		return new Verified_Credential_Opaque_Tokens(
			$c[ PublicKeyCredentialUserEntity_Factory::class ]
		);
	};

	$c[ CoseManager::class ] = static function () {
		$manager = new CoseManager();
		$manager->add( new ES256() );
		$manager->add( new RS256() );

		return $manager;
	};

	// -- REST -- //
	\ITSEC_Lib::extend_if_able( $c, 'rest.controllers', function ( $controllers, Container $c ) {
		$controllers[] = $c[ REST\RegisterCredential::class ];
		$controllers[] = $c[ REST\VerifyCredential::class ];
		$controllers[] = $c[ REST\Credentials::class ];

		return $controllers;
	} );

	$c[ REST\RegisterCredential::class ] = static function ( Container $c ) {
		return new REST\RegisterCredential(
			$c[ RegistrationCeremony::class ],
			$c[ PublicKeyCredentialCreationOptions_Factory::class ],
			$c[ Session_Storage::class ]
		);
	};

	$c[ REST\VerifyCredential::class ] = static function ( Container $c ) {
		return new REST\VerifyCredential(
			$c[ AuthenticationCeremony::class ],
			$c[ PublicKeyCredentialRequestOptions_Factory::class ],
			$c[ PublicKeyCredentialUserEntity_Factory::class ],
			$c[ Session_Storage::class ],
			$c[ Verified_Credential_Tokens::class ]
		);
	};

	$c[ REST\Credentials::class ] = static function ( Container $c ) {
		return new REST\Credentials(
			$c[ PublicKeyCredential_Record_Repository::class ],
			$c[ PublicKeyCredentialUserEntity_Factory::class ]
		);
	};
};
