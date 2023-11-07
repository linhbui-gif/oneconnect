<?php require( __DIR__ . '/preamble.php' ); ?>

<div class="itsec-pwls-login__fields">
	<?php if ( ! empty( $username ) ): ?>
		<input type="hidden" name="itsec_magic_link_username" value="<?php echo esc_attr( $username ); ?>">
	<?php else: ?>
		<label for="itsec_magic_link_username"><?php echo $user_lookup_fields_label; ?><br/>
			<input type="text" name="itsec_magic_link_username" id="itsec_magic_link_username" class="input" size="20" autocapitalize="off"/>
		</label>
	<?php endif; ?>
	<?php if ( in_array( 'magic', $methods, true ) ): ?>
		<?php if ( empty( $use_prompt_link ) ) : ?>
			<button class="itsec-pwls-login__submit itsec-pwls-login__submit-magic" name="itsec_pwls_magic_login" type="submit" value="1">
				<?php esc_html_e( 'Email Magic Link', 'it-l10n-ithemes-security-pro' ); ?>
			</button>
		<?php else: ?>
			<?php require( __DIR__ . '/prompt-link.php' ); ?>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ( in_array( 'webauthn', $methods, true ) && empty( $use_prompt_link ) ): ?>
		<button class="itsec-pwls-login__submit itsec-pwls-login__submit-webauthn fade-if-no-js" name="itsec_pwls_webauthn_login" type="button" value="">
			<?php esc_html_e( 'Use Your Passkey', 'it-l10n-ithemes-security-pro' ); ?>
		</button>
		<?php require __DIR__ . '/webauthn-noscript.php'; ?>
	<?php endif; ?>
</div>
