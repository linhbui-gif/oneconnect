<div class="itsec-pwls-login__fields">
	<?php if ( in_array( 'magic', $methods, true ) ): ?>
		<p class="itsec-pwls-login__link-wrap itsec-pwls-login__link-wrap--magic">
			<a class="itsec-pwls-login__link" href="<?php echo esc_url( add_query_arg( ITSEC_Passwordless_Login::METHOD, 'magic', $prompt_link ) ); ?>">
				<?php esc_html_e( 'Email Magic Link', 'it-l10n-ithemes-security-pro' ) ?>
			</a>
		</p>
	<?php endif; ?>

	<?php if ( in_array( 'webauthn', $methods, true ) ): ?>
		<p class="itsec-pwls-login__link-wrap itsec-pwls-login__link-wrap--webauthn fade-if-no-js">
			<a class="itsec-pwls-login__link" href="<?php echo esc_url( add_query_arg( ITSEC_Passwordless_Login::METHOD, 'webauthn', $prompt_link ) ); ?>">
				<?php esc_html_e( 'Use Your Passkey', 'it-l10n-ithemes-security-pro' ) ?>
			</a>
		</p>
		<?php require __DIR__ . '/webauthn-noscript.php'; ?>
	<?php endif; ?>
</div>
