<?php

namespace iThemesSecurity\Import_Export\Sources;

use iThemesSecurity\Contracts\Import_Export_Source;
use iThemesSecurity\Import_Export\Export\Export;
use iThemesSecurity\Import_Export\Import\Import_Context;
use iThemesSecurity\Import_Export\Import\Transformation;
use iThemesSecurity\Lib\Result;

final class Settings implements Import_Export_Source {
	public function get_export_slug(): string {
		return 'settings';
	}

	public function get_export_title(): string {
		return __( 'Settings', 'it-l10n-ithemes-security-pro' );
	}

	public function get_export_description(): string {
		return __( 'The configured settings for each iThemes Security feature.', 'it-l10n-ithemes-security-pro' );
	}

	public function get_export_options_schema(): array {
		$modules = array_map( static function ( $module ) {
			if ( ! \ITSEC_Modules::get_settings_obj( $module ) ) {
				return null;
			}

			if ( ( $config = \ITSEC_Modules::get_config( $module ) ) && $config->get_extends() ) {
				return null;
			}

			return [
				$module,
				\ITSEC_Modules::get_labels( $module )['title'] ?? $module,
			];
		}, \ITSEC_Modules::get_available_modules() );
		$modules = array_values( array_filter( $modules ) );

		return [
			'title'       => __( 'Features', 'it-l10n-ithemes-security-pro' ),
			'description' => __( 'Select the iThemes Security features to export settings for.', 'it-l10n-ithemes-security-pro' ),
			'type'        => 'array',
			'minItems'    => 1,
			'uniqueItems' => true,
			'items'       => [
				'type'      => 'string',
				'enum'      => wp_list_pluck( $modules, 0 ),
				'enumNames' => wp_list_pluck( $modules, 1 )
			],
			'default'     => wp_list_pluck( $modules, 0 ),
			'uiSchema'    => [
				'ui:widget' => 'checkboxes',
			],
		];
	}

	public function get_export_schema(): array {
		return [
			'type'                 => 'object',
			'additionalProperties' => [
				'type' => 'object',
			],
		];
	}

	public function get_transformations(): array {
		return [
			new class implements Transformation {
				public function transform( Export $export, Import_Context $context ): Export {
					$data = $export->get_data( 'settings' );

					foreach ( \ITSEC_Modules::get_available_modules() as $module ) {
						if ( ! $settings = \ITSEC_Modules::get_settings_obj( $module ) ) {
							continue;
						}

						if ( ! $schema = $settings->get_settings_schema() ) {
							continue;
						}

						foreach ( $schema['properties'] as $setting => $config ) {
							if ( ! in_array( $config['format'] ?? '', [ 'file-path', 'directory' ], true ) ) {
								continue;
							}

							$data[ $module ][ $setting ] = preg_replace(
								'/^' . preg_quote( $export->get_abspath(), '/' ) . '/',
								ABSPATH,
								$data[ $module ][ $setting ]
							);
						}
					}

					return $export->with_data( 'settings', $data );
				}

				public function get_user_paths(): array {
					return [];
				}

				public function get_role_paths(): array {
					return [];
				}
			}
		];
	}

	public function export( $options ): Result {
		$all_settings = [];

		foreach ( \ITSEC_Modules::get_available_modules() as $module ) {
			$check    = $module;
			$config   = \ITSEC_Modules::get_config( $module );
			$excluded = [];

			if ( $config ) {
				$check    = $config->get_extends() ?: $check;
				$excluded = $config->get_export_excluded_settings();
			}

			if ( ! in_array( $check, $options, true ) || ! $settings_obj = \ITSEC_Modules::get_settings_obj( $module ) ) {
				continue;
			}

			$settings = $settings_obj->get_all();

			foreach ( $excluded as $path ) {
				$settings = \ITSEC_Lib::array_remove( $settings, $path );
			}

			$all_settings[ $module ] = $settings;
		}

		return Result::success( $all_settings );
	}

	public function import( Export $from, Import_Context $context ): Result {
		if ( ! $all_settings = $from->get_data( $this->get_export_slug() ) ) {
			return Result::success();
		}

		$result = Result::success();

		foreach ( $all_settings as $module => $settings ) {
			if ( ! $obj = \ITSEC_Modules::get_settings_obj( $module ) ) {
				continue;
			}

			// The list of settings to import may not include all setting keys.
			$defaults = $obj->get_defaults();
			$saved    = $obj->set_all( array_merge( $defaults, $settings ) );
			$saved    = \ITSEC_Lib::updated_settings_to_wp_error( $saved );

			if ( is_wp_error( $saved ) ) {
				$result->add_warning_message( ...\ITSEC_Lib::get_error_strings( $saved ) );
			}
		}

		return $result;
	}
}
