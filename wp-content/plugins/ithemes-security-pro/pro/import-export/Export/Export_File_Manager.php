<?php

namespace iThemesSecurity\Import_Export\Export;

use iThemesSecurity\Lib\Result;

final class Export_File_Manager {

	/**
	 * Creates an export file.
	 *
	 * Attempts
	 *
	 * @param Export $export
	 *
	 * @return string|\WP_Error
	 */
	public function create_file( Export $export ) {
		$title = sanitize_title( $export->get_title() );

		if ( ! $title ) {
			$title = $export->get_exported_at()->format( 'y-m-d' );
		}

		$title = 'itsec-export-' . $title;

		$dir = \ITSEC_Lib_Directory::create_temp_directory();

		if ( is_wp_error( $dir ) ) {
			return $dir;
		}

		$json_file = $dir . $title . '.json';
		$zip_file  = $dir . $title . '.zip';

		if ( ! class_exists( 'PclZip' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-pclzip.php';
		}

		$zip     = new \PclZip( $zip_file );
		$created = $zip->create( $json_file, PCLZIP_OPT_REMOVE_PATH, $dir );

		if ( 0 === $created ) {
			return $json_file;
		}

		return $zip_file;
	}

	/**
	 * Reads export data from a zip or json file.
	 *
	 * @param string $file Full file path.
	 *
	 * @return Result<Export>
	 */
	public function read_file( string $file ): Result {
		if ( $this->is_zip_file( $file ) ) {
			$json = $this->extract_json_from_zip( $file );
		} else {
			$json = \ITSEC_Lib_File::read( $file );
		}

		if ( is_wp_error( $json ) ) {
			return Result::error( $json );
		}

		if ( ! $json ) {
			return Result::error( new \WP_Error(
				'itsec.import-export.export-file-empty',
				__( 'The supplied export JSON file is empty.', 'it-l10n-ithemes-security-pro' ),
				[ 'status' => \WP_Http::BAD_REQUEST ]
			) );
		}

		$data = json_decode( $json, true );

		if ( ! $data ) {
			return Result::error( new \WP_Error(
				'itsec.import-export.export-invalid-json',
				__( 'The supplied export file contains invalid JSON.', 'it-l10n-ithemes-security-pro' ),
				[ 'status' => \WP_Http::BAD_REQUEST ]
			) );
		}

		return Result::success( Export::from_data( $data ) );
	}

	/**
	 * Checks if the file at the given path is a zip file.
	 *
	 * @param string $file
	 *
	 * @return bool
	 */
	private function is_zip_file( string $file ): bool {
		if ( \ITSEC_Lib::str_ends_with( $file, '.zip' ) ) {
			return true;
		}

		$finfo = finfo_open( FILEINFO_MIME_TYPE );
		$mime  = finfo_file( $finfo, $file );

		return in_array( $mime, [
			'application/zip',
			'application/octet-stream',
		], true );
	}

	/**
	 * Extracts the raw JSON from a zip file.
	 *
	 * @param string $file Path to a zip file.
	 *
	 * @return string|\WP_Error
	 */
	private function extract_json_from_zip( string $file ) {
		$is_available = $this->is_filesystem_available();

		if ( is_wp_error( $is_available ) ) {
			return $is_available;
		}

		if ( true !== WP_Filesystem() ) {
			return new \WP_Error( 'fs_unavailable', __( 'Could not initialize filesystem.', 'it-l10n-ithemes-security-pro' ) );
		}

		$temp_dir = \ITSEC_Lib_Directory::create_temp_directory();

		if ( is_wp_error( $temp_dir ) ) {
			return $temp_dir;
		}

		if ( ! function_exists( 'unzip_file' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		$unzipped = unzip_file( $file, $temp_dir );

		if ( is_wp_error( $unzipped ) ) {
			return $unzipped;
		}

		$files = \ITSEC_Lib_Directory::read( $temp_dir );

		if ( is_wp_error( $files ) ) {
			return $files;
		}

		$files = array_filter( $files, static function ( $file ) {
			return \ITSEC_Lib::str_ends_with( $file, '.json' );
		} );

		if ( ! $files ) {
			return new \WP_Error(
				'itsec.import-export.export-file-missing-files',
				__( 'The supplied zip file did not contain a JSON file with valid iThemes Security settings.', 'it-l10n-ithemes-security-pro' ),
				[ 'status' => \WP_Http::BAD_REQUEST ]
			);
		}

		if ( count( $files ) > 1 ) {
			return new \WP_Error(
				'itsec.import-export.export-file-multiple-files',
				__( 'The supplied zip file contained more than one JSON file.', 'it-l10n-ithemes-security-pro' ),
				[ 'status' => \WP_Http::BAD_REQUEST ]
			);
		}

		$contents = \ITSEC_Lib_File::read( \ITSEC_Lib::first( $files ) );

		\ITSEC_Lib_Directory::remove( $temp_dir );

		return $contents;
	}

	/**
	 * Determines if the filesystem is available.
	 *
	 * Only the 'Direct' filesystem transport, and SSH/FTP when credentials are stored are supported at present.
	 *
	 * @return true|\WP_Error True if filesystem is available, WP_Error otherwise.
	 */
	private function is_filesystem_available() {
		if ( ! function_exists( 'get_filesystem_method' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		$filesystem_method = get_filesystem_method();

		if ( 'direct' === $filesystem_method ) {
			return true;
		}

		ob_start();
		$filesystem_credentials_are_stored = request_filesystem_credentials( self_admin_url() );
		ob_end_clean();

		if ( $filesystem_credentials_are_stored ) {
			return true;
		}

		return new \WP_Error( 'fs_unavailable', __( 'The filesystem is currently unavailable.', 'it-l10n-ithemes-security-pro' ) );
	}
}
