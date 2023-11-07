<?php

namespace iThemesSecurity\Modules\Password_Expiration;

use iThemesSecurity\Lib\Config_Password_Requirement;
use iThemesSecurity\Module_Config;
use iThemesSecurity\User_Groups;
use ITSEC_Core;
use ITSEC_Lib_Password_Requirements;

final class Age_Requirement extends Config_Password_Requirement {

	/** @var User_Groups\Matcher */
	private $matcher;

	public function __construct( User_Groups\Matcher $matcher, Module_Config $config, string $code ) {
		parent::__construct( $config, $code );
		$this->matcher = $matcher;
	}

	public function is_password_change_required( \WP_User $user, array $settings ): bool {
		$target = User_Groups\Match_Target::for_user( $user );

		if ( ! $this->matcher->matches( $target, $settings['group'] ) ) {
			return false;
		}

		$days   = isset( $settings['expire_max'] ) ? absint( $settings['expire_max'] ) : 120;
		$period = $days * DAY_IN_SECONDS;

		$oldest_allowed = ITSEC_Core::get_current_time_gmt() - $period;

		return ITSEC_Lib_Password_Requirements::password_last_changed( $user ) < $oldest_allowed;
	}

	public function evaluate( string $password, $user ) {
		return new \WP_Error( 'not_implemented', __( 'This password requirement does not evaluate passwords.', 'it-l10n-ithemes-security-pro' ) );
	}

	public function validate( $evaluation, $user, array $settings, array $args ) {
		return true;
	}

	public function get_reason_message( $evaluation, array $settings ): string {
		$period = absint( $settings['expire_max'] ?? 120 );

		return sprintf( esc_html__( 'Your password has expired. You must create a new password every %d days.', 'it-l10n-ithemes-security-pro' ), $period );
	}

	public function is_always_enabled(): bool {
		return false;
	}

	public function should_evaluate_if_not_enabled(): bool {
		return false;
	}

	public function render( \ITSEC_Form $form ) {
		?>
		<tr>
			<th scope="row">
				<label for="itsec-password-requirements-requirement_settings-age-group">
					<?php esc_html_e( 'User Group', 'it-l10n-ithemes-security-pro' ); ?>
				</label>
			</th>
			<td>
				<?php $form->add_user_groups( 'group', 'password-requirements', 'requirement_settings.age.group' ); ?>
				<br/>
				<label for="itsec-password-requirements-requirement_settings-age-group"><?php esc_html_e( 'Require users in the selected groups to change their password periodically.', 'it-l10n-ithemes-security-pro' ); ?></label>
				<p class="description"><?php esc_html_e( 'We suggest enabling this setting for all users, but it may lead to users forgetting their passwords.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-password-requirements-requirement_settings-age-expire_max"><?php esc_html_e( 'Maximum Password Age', 'it-l10n-ithemes-security-pro' ); ?></label></th>
			<td>
				<?php $form->add_text( 'expire_max', array( 'class' => 'small-text code' ) ); ?>
				<label for="itsec-password-requirements-requirement_settings-age-expire_max"><?php esc_html_e( 'Days', 'it-l10n-ithemes-security-pro' ); ?></label>
				<p class="description"><?php esc_html_e( 'The maximum number of days a password may be kept before it is expired.', 'it-l10n-ithemes-security-pro' ); ?></p>
			</td>
		</tr>
		<?php
	}
}
