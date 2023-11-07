<?php

namespace iThemesSecurity\Import_Export\Export;

use iThemesSecurity\Contracts\Import_Export_Source;
use iThemesSecurity\Import_Export\Export\Repository\Repository;
use iThemesSecurity\Lib\Result;

final class Exporter {

	/** @var Repository */
	private $repository;

	/** @var Export_File_Manager */
	private $file_manager;

	/** @var array */
	private $options_schema;

	/** @var Import_Export_Source[] */
	private $sources;

	public function __construct(
		Repository $repository,
		Export_File_Manager $file_manager,
		array $options_schema,
		array $sources
	) {
		$this->repository     = $repository;
		$this->file_manager   = $file_manager;
		$this->options_schema = $options_schema;
		$this->sources        = $sources;
	}

	/**
	 * Creates a new export.
	 *
	 * @param Export_Context $context
	 * @param \WP_User|null  $exported_by
	 * @param string         $title
	 *
	 * @return Result<Export>
	 */
	public function export( Export_Context $context, \WP_User $exported_by = null, string $title = '' ): Result {
		$valid = $context->validate_options_against( $this->options_schema );

		if ( is_wp_error( $valid ) ) {
			return Result::error( $valid );
		}

		$export = new Export( $this->repository->next_id() );
		$export->attach_metadata(
			$title,
			\ITSEC_Core::get_plugin_build(),
			\ITSEC_Core::get_plugin_version(),
			\ITSEC_Core::get_current_time_gmt( true ),
			$exported_by,
			network_home_url(),
			ABSPATH
		);

		$results = [];

		foreach ( $this->sources as $source ) {
			if ( ! $context->is_source_included( $source->get_export_slug() ) ) {
				continue;
			}

			$result    = $source->export( $context->get_options( $source->get_export_slug() ) );
			$results[] = $result;

			if ( $result->is_success() && $result->get_data() ) {
				$export->set_data( $source->get_export_slug(), $result->get_data() );
			}
		}

		return Result::combine_with_success_data( $export, ...$results );
	}

	/**
	 * Sends the export file to the given email address.
	 *
	 * @param Export $export
	 * @param string $email
	 *
	 * @return bool
	 */
	public function notify( Export $export, string $email ): bool {
		$attachment = $this->file_manager->create_file( $export );

		if ( is_wp_error( $attachment ) ) {
			return false;
		}

		$exported_at = $export->get_exported_at();

		$date = \ITSEC_Lib::date_format_i18n_and_local_timezone( $exported_at, get_option( 'date_format' ) );
		$time = \ITSEC_Lib::date_format_i18n_and_local_timezone( $exported_at, get_option( 'time_format' ) );

		$nc   = \ITSEC_Core::get_notification_center();
		$mail = $nc->mail();

		$subject = $mail->prepend_site_url_to_subject( $nc->get_subject( 'import-export' ) );
		$subject = apply_filters( 'itsec_backup_email_subject', $subject );

		$mail->set_subject( $subject, false );
		$mail->set_recipients( [ $email ] );
		$mail->add_attachment( $attachment );

		$mail->add_header(
			esc_html__( 'Settings Export', 'it-l10n-ithemes-security-pro' ),
			sprintf(
				/* translators: 1. opening bold tag, 2. date, 3. time, 4. closing bold tag. */
				esc_html__( 'Settings Export created on %1$s %2$s at %3$s %4$s', 'it-l10n-ithemes-security-pro' ),
				'<b>',
				$date,
				$time,
				'</b>'
			)
		);

		$message = \ITSEC_Lib::replace_tags( $nc->get_message( 'import-export' ), [
			'date'       => $date,
			'time'       => $time,
			'site_url'   => $mail->get_display_url(),
			'site_title' => get_bloginfo( 'name', 'display' ),
		] );

		$mail->add_info_box( $message, 'attachment' );
		$mail->add_footer();

		$send = $nc->send( 'import-export', $mail );

		\ITSEC_Lib_Directory::remove( dirname( $attachment ) );

		return $send;
	}
}
