(function ( $, config, webauthn ) {
	$( window ).on( 'load', function () {
		$( '#user_pass' ).removeAttr( 'disabled' );
	} );

	function renderNotice( type, message ) {
		var $notice = $( '<div />' );
		$notice.addClass( 'notice notice-' + type );
		$( '<p />' ).text( message ).appendTo( $notice );

		$notice.insertBefore( '#loginform' );
	}

	function clearNotices() {
		$( '.notice' ).remove();
	}

	$( function () {
		var $loginForm = $( '#loginform' ),
			$body = $( 'body' );

		$body.removeClass( 'no-js' );

		if ( !webauthn || !webauthn.isAvailable() ) {
			$( '.itsec-pwls-login__link-wrap--webauthn' ).remove();
		}

		$( document ).on( 'click', '.itsec-pwls-login-fallback__link-wrap--type-wp a', function ( e ) {
			e.preventDefault();
			$body.removeClass( 'itsec-pwls-login--show' ).addClass( 'itsec-pwls-login--hide' );
			$loginForm.attr( 'action', config.passwordAction );
			$( '#user_pass' ).removeAttr( 'disabled' );
		} );

		$( document ).on( 'click', '.itsec-pwls-login-fallback__link-wrap--type-ml a', function ( e ) {
			e.preventDefault();
			$body.removeClass( 'itsec-pwls-login--hide' ).addClass( 'itsec-pwls-login--show' );
			$loginForm.attr( 'action', config.magicAction );
		} );

		$( document ).on( 'click', '.itsec-pwls-login__submit-webauthn', function ( e ) {
			if ( !webauthn ) {
				return;
			}

			clearNotices();

			const $trigger = $( 'button[name="itsec_pwls_webauthn_login"]' );

			if ( $trigger.val() ) {
				return;
			}

			e.preventDefault();

			const $user = $( 'input[name="itsec_magic_link_username"]' );
			webauthn.verifyCredential( $user.val() ).then( function ( response ) {
				$user.val( response.user );
				$trigger.val( response.token );
				$trigger.prop( 'type', 'submit' );
				$trigger.click();
			} ).catch( function ( error ) {
				renderNotice( 'error', error.message || config.i18n.error );
			} );
		} );
		if ( config.flow === 'method-first' ) {
			$( '.itsec-pwls-login-fallback' ).insertAfter( '.submit' );
			$( document ).on( 'click', '.itsec-pwls-login__link', function ( e ) {
				e.preventDefault();

				$loginForm.attr( 'action', config.magicAction );
				$body.addClass( 'itsec-pwls-login-form' );

				var type = $( this ).parent().prop( 'class' ).indexOf( 'itsec-pwls-login__link-wrap--webauthn' ) !== -1 ? 'webauthn' : 'magic';
				var remove = type === 'webauthn' ? 'magic' : 'webauthn';
				$( '.itsec-pwls-login-wrap' ).html( $( '#tmpl-itsec-pwls-login-prompt-form' ).html() );
				$( '.itsec-pwls-login__submit-' + remove ).remove();
			} );

			if ( window.location.search.indexOf( 'itsec-pwls-modal=1' ) !== -1 ) {
				$( '.itsec-pwls-login__link' ).click();
			}
		} else {
			$loginForm.hide();
			$loginForm.before( $( '#tmpl-itsec-pwls-login-user-form' ).html() );

			var $userForm = $( '#itsec-pwls-login-user-form' ).on( 'submit', function ( e ) {
				e.preventDefault();

				var $btn = $( '#itsec-pwls-login-user-form__continue' ).attr( 'disabled', true );

				$.post(
					config.ajaxUrl,
					{
						action: config.ajaxAction,
						log   : $( '#itsec-pwls-login-user-form__username' ).val(),
					},
					function ( response ) {
						if ( !response.success ) {
							alert( response.data.message || config.i18n.error );

							return;
						}

						var $html = $( response.data.html );

						if ( ! webauthn || !webauthn.isAvailable() ) {
							$( '.itsec-pwls-login__submit-webauthn', $html ).remove();
						}

						$( '#user_login' ).parent().remove();

						$( 'input[name="itsec_pwls_login_user_first"]' ).remove();
						$( '#wp-submit' ).val( config.i18n.login );

						$body.removeClass( 'itsec-pwls-login--no-user' ).addClass( 'itsec-pwls-login--has-user' );
						$userForm.hide();
						$loginForm.show();
						$loginForm.prepend( $html );

						$( '.itsec-pwls-login-fallback' ).insertAfter( '.submit' );

						if ( response.data.methods.length > 0 ) {
							$body.addClass( 'itsec-pwls-login--show itsec-pwls-login--is-available' );
							$loginForm.attr( 'action', config.magicAction );
						} else {
							$( '#user_pass' ).removeAttr( 'disabled' );
						}
					} )
					.always( function () {
						$btn.attr( 'disabled', false );
					} );
			} );
		}
	} );
})( jQuery, window['ITSECMagicLogin'], window.itsec && window.itsec.webauthn && window.itsec.webauthn.utils );
