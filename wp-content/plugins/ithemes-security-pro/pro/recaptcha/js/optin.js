( function( $, config ) {
	$( function() {
		$( document ).on( 'click', '.itsec-recaptcha-opt-in__agree', function( e ) {
			e.preventDefault();

			var $optins = $( '.itsec-recaptcha-opt-in' )
				.addClass( 'itsec-recaptcha-opt-in--loading' );

			$.ajax( {
				url     : config.sdk,
				dataType: 'script',
				cache   : true,
				success : function() {
					$optins.each( function() {
						var $optin = $( this );
						$optin.parents( 'form' ).append( $( '<input type="hidden">' ).attr( {
							name : 'recaptcha-opt-in',
							value: 'true',
						} ) );

						var $template = $( '.itsec-recaptcha-opt-in__template', $optin );
						$optin.replaceWith( $template.html() );
					} );

					if ( config.load && window[ config.load ] ) {
						if ( window.grecaptcha ) {
							window.grecaptcha.ready( window[ config.load ] );
						} else {
							window[ config.load ]();
						}
					}
				},
			} );
		} );
	} );
} )( jQuery, window[ 'ITSECRecaptchaOptIn' ] );
