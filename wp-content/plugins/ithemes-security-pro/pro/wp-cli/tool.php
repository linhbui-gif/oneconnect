<?php

namespace iThemesSecurity\WP_CLI;

use iThemesSecurity\Contracts\Runnable;
use iThemesSecurity\Lib\Tools\Tools_Runner;
use iThemesSecurity\Lib\Tools\Tools_Registry;
use function WP_CLI\Utils\get_flag_value;

final class Tool implements Runnable {

	/** @var Tools_Registry */
	private $registry;

	/** @var Tools_Runner */
	private $runner;

	/**
	 * Tool constructor.
	 *
	 * @param Tools_Registry $registry
	 * @param Tools_Runner   $runner
	 */
	public function __construct( Tools_Registry $registry, Tools_Runner $runner ) {
		$this->registry = $registry;
		$this->runner   = $runner;
	}

	public function run() {
		foreach ( $this->registry->get_tools() as $tool ) {
			if ( ! $tool->is_available() ) {
				continue;
			}

			$synopsis = [
				[
					'name'        => 'format',
					'type'        => 'assoc',
					'description' => 'Render output in particular format.',
					'default'     => 'json',
					'options'     => [ 'json', 'yaml' ],
					'optional'    => true,
				],
				[
					'name'        => 'porcelain',
					'type'        => 'flag',
					'description' => 'Only print the tool\'s output.',
					'default'     => false,
					'optional'    => true,
				],
			];

			if ( $tool->get_form() ) {
				$synopsis = array_merge( \ITSEC_Lib::convert_schema_to_cli_synopsis( $tool->get_form() ), $synopsis );
			}

			\WP_CLI::add_command(
				"itsec tool {$tool->get_slug()}",
				function ( $args, $assoc_args ) use ( $tool ) {
					$porcelain = get_flag_value( $assoc_args, 'porcelain' );
					$form      = $assoc_args;
					unset( $form['format'], $form['porcelain'] );

					$result = $this->runner->run_tool( $tool, $form );

					if ( ! $porcelain ) {
						foreach ( $result->get_info_messages() as $message ) {
							\WP_CLI::log( $message );
						}

						foreach ( $result->get_warning_messages() as $message ) {
							\WP_CLI::warning( $message );
						}

						foreach ( $result->get_success_messages() as $message ) {
							\WP_CLI::success( $message );
						}
					}

					if ( $result->is_success() ) {
						if ( $data = $result->get_data() ) {
							if ( is_string( $data ) ) {
								\WP_CLI::log( $data );
							} else {
								\WP_CLI::print_value( $data, $assoc_args );
							}
						} elseif ( ! $porcelain ) {
							\WP_CLI::success( 'Tool completed.' );
						}
					} else {
						\WP_CLI::error( $result->get_error() );
					}
				},
				[
					'synopsis'  => $synopsis,
					'shortdesc' => $tool->get_title(),
					'longdesc'  => $tool->get_description(),
				]
			);
		}
	}
}

( new Tool(
	\ITSEC_Modules::get_container()->get( Tools_Registry::class ),
	\ITSEC_Modules::get_container()->get( Tools_Runner::class )
) )->run();
