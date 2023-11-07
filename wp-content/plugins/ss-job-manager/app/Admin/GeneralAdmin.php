<?php
namespace App\Admin;

class GeneralAdmin{
    public function __construct()
    {
       
    }

    /**
	 * Displays file input field.
	 *
	 * @param string  $key         Field key.
	 * @param string  $name        Input name.
	 * @param string  $placeholder Input placeholder.
	 * @param string  $value       File path.
	 * @param boolean $multiple    Flag if the field is single or part of multiple.
	 * @param string  $download    URL to download the file.
	 */
	private static function file_url_field( $key, $name, $placeholder, $value, $multiple, $download = null ) {
		$name = esc_attr( $name );
		if ( $multiple ) {
			$name = $name . '[]';
		}
		?>
		<span class="file_url">
			<input
				type="text"
				name="<?php echo esc_attr( $name ); ?>"
				<?php
				if ( ! $multiple ) {
					echo 'id="' . esc_attr( $key ) . '"';
				}
				?>
				placeholder="<?php echo esc_attr( $placeholder ); ?>"
				value="<?php echo esc_attr( $value ); ?>"
			/>
			<button class="button button-small wp_job_manager_upload_file_button" data-uploader_button_text="<?php esc_attr_e( 'Use file', 'wp-job-manager' ); ?>">
				<?php esc_html_e( 'Upload', 'wp-job-manager' ); ?>
			</button>
			<button
				class="button button-small wp_job_manager_view_file_button"
				<?php
				if ( $download ) {
					echo 'data-download-url="' . esc_url( $download ) . '"';
				}
				?>
			>
				<?php esc_html_e( 'View', 'wp-job-manager' ); ?>
			</button>
		</span>
		<?php
	}
    /**
	 * Displays label and file input field.
	 *
	 * @param string $key
	 * @param array  $field
	 */
	public static function input_file( $key, $field ) {
		global $post;

		if ( empty( $field['placeholder'] ) ) {
			$field['placeholder'] = 'https://';
		}
		if ( ! empty( $field['name'] ) ) {
			$name = $field['name'];
		} else {
			$name = $key;
		}
		?>
		<p class="form-field">
			<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( wp_strip_all_tags( $field['label'] ) ); ?>:
			<?php if ( ! empty( $field['description'] ) ) : ?>
				<span class="tips" data-tip="<?php echo esc_attr( $field['description'] ); ?>">[?]</span>
			<?php endif; ?>
			</label>
			<?php
			if ( ! empty( $field['multiple'] ) ) {
				foreach ( (array) $field['value'] as $k => $value ) {
					$download = null;
					if ( isset( $field['download'] ) && isset( $field['download'][ $k ] ) ) {
						$download = $field['download'][ $k ];
					}

					self::file_url_field( $key, $name, $field['placeholder'], $value, true, $download );
				}
			} else {
				$download = null;
				if ( isset( $field['download'] ) ) {
					$download = $field['download'];
				}

				self::file_url_field( $key, $name, $field['placeholder'], $field['value'], false, $download );
			}
			if ( ! empty( $field['multiple'] ) ) {
				?>
				<button class="button button-small wp_job_manager_add_another_file_button" data-field_name="<?php echo esc_attr( $key ); ?>" data-field_placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" data-uploader_button_text="<?php esc_attr_e( 'Use file', 'wp-job-manager' ); ?>" data-uploader_button="<?php esc_attr_e( 'Upload', 'wp-job-manager' ); ?>" data-view_button="<?php esc_attr_e( 'View', 'wp-job-manager' ); ?>"><?php esc_html_e( 'Add file', 'wp-job-manager' ); ?></button>
				<?php
			}
			?>
		</p>
		<?php
	}

	/**
	 * Displays label and text input field.
	 *
	 * @param string $key
	 * @param array  $field
	 */
	public static function input_text( $key, $field ) {
		if ( ! empty( $field['name'] ) ) {
			$name = $field['name'];
		} else {
			$name = $key;
		}
		if ( ! empty( $field['classes'] ) ) {
			$classes = implode( ' ', is_array( $field['classes'] ) ? $field['classes'] : [ $field['classes'] ] );
		} else {
			$classes = '';
		}
		?>
		<p class="form-field">
            
			<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( wp_strip_all_tags( $field['label'] ) ); ?>
			<?php if ( ! empty( $field['description'] ) ) : ?>
				<span class="tips" data-tip="<?php echo esc_attr( $field['description'] ); ?>">[?]</span>
			<?php endif; ?>
			</label>
			<input type="text" autocomplete="off" name="<?php echo esc_attr( $name ); ?>" class="<?php echo esc_attr( $classes ); ?>" id="<?php echo esc_attr( $key ); ?>" placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" value="<?php echo esc_attr( $field['value'] ); ?>" />
		</p>
		<?php
	}

	/**
	 * Just displays information.
	 *
	 * @since 1.27.0
	 *
	 * @param string $key
	 * @param array  $field
	 */
	public static function input_info( $key, $field ) {
		self::input_hidden( $key, $field );
	}

	/**
	 * Displays information and/or hidden input.
	 *
	 * @since 1.27.0
	 *
	 * @param string $key
	 * @param array  $field
	 */
	public static function input_hidden( $key, $field ) {
		if ( ! empty( $field['name'] ) ) {
			$name = $field['name'];
		} else {
			$name = $key;
		}
		if ( ! empty( $field['classes'] ) ) {
			$classes = implode( ' ', is_array( $field['classes'] ) ? $field['classes'] : [ $field['classes'] ] );
		} else {
			$classes = '';
		}
		if ( 'hidden' === $field['type'] ) {
			if ( empty( $field['label'] ) ) {
				echo '<input type="hidden" name="' . esc_attr( $name ) . '" class="' . esc_attr( $classes ) . '" id="' . esc_attr( $key ) . '" value="' . esc_attr( $field['value'] ) . '" />';
				return;
			}
		}
		?>
		<p class="form-field">
			<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( wp_strip_all_tags( $field['label'] ) ); ?>:
			<?php if ( ! empty( $field['description'] ) ) : ?>
				<span class="tips" data-tip="<?php echo esc_attr( $field['description'] ); ?>">[?]</span>
			<?php endif; ?>
			</label>
			<?php if ( ! empty( $field['information'] ) ) : ?>
				<span class="information"><?php echo wp_kses( $field['information'], [ 'a' => [ 'href' => [] ] ] ); ?></span>
			<?php endif; ?>
			<?php echo '<input type="hidden" name="' . esc_attr( $name ) . '" class="' . esc_attr( $classes ) . '" id="' . esc_attr( $key ) . '" value="' . esc_attr( $field['value'] ) . '" />'; ?>
		</p>
		<?php
	}

	/**
	 * Displays label and textarea input field.
	 *
	 * @param string $key
	 * @param array  $field
	 */
	public static function input_textarea( $key, $field ) {
		if ( ! empty( $field['name'] ) ) {
			$name = $field['name'];
		} else {
			$name = $key;
		}
		?>
		<p class="form-field">
			<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( wp_strip_all_tags( $field['label'] ) ); ?>:
			<?php if ( ! empty( $field['description'] ) ) : ?>
				<span class="tips" data-tip="<?php echo esc_attr( $field['description'] ); ?>">[?]</span>
			<?php endif; ?>
			</label>
			<textarea name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $key ); ?>" placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"><?php echo esc_html( $field['value'] ); ?></textarea>
		</p>
		<?php
	}

	/**
	 * Displays label and select input field.
	 *
	 * @param string $key
	 * @param array  $field
	 */
	public static function input_select( $key, $field ) {
		if ( ! empty( $field['name'] ) ) {
			$name = $field['name'];
		} else {
			$name = $key;
		}
		$selected_value = null;
		if ( isset( $field['value'] ) ) {
			$selected_value = esc_attr( $field['value'] );
		}
		?>
		<p class="form-field">
			<label for="<?php echo esc_attr( $key ); ?>">
				<?php echo esc_html( wp_strip_all_tags( $field['label'] ) ); ?>:
				<?php if ( ! empty( $field['description'] ) ) : ?>
					<span class="tips" data-tip="<?php echo esc_attr( $field['description'] ); ?>">[?]</span>
				<?php endif; ?>
			</label>
			<select name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $key ); ?>" autocomplete="off">
				<?php foreach ( $field['options'] as $key => $value ) : ?>
					<option
						value="<?php echo esc_attr( $key ); ?>"
						<?php
						if ( null !== $selected_value ) {
							selected( $selected_value, trim( $key ) );
						}
						?>
					><?php echo esc_html( $value ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php
	}

	/**
	 * Displays label and multi-select input field.
	 *
	 * @param string $key
	 * @param array  $field
	 */
	public static function input_multiselect( $key, $field ) {
		if ( ! empty( $field['name'] ) ) {
			$name = $field['name'];
		} else {
			$name = $key;
		}
		?>
		<p class="form-field">
			<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( wp_strip_all_tags( $field['label'] ) ); ?>:
			<?php if ( ! empty( $field['description'] ) ) : ?>
				<span class="tips" data-tip="<?php echo esc_attr( $field['description'] ); ?>">[?]</span>
			<?php endif; ?>
			</label>
			<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="">
			<select multiple="multiple" name="<?php echo esc_attr( $name ); ?>[]" id="<?php echo esc_attr( $key ); ?>">
				<?php foreach ( $field['options'] as $key => $value ) : ?>
				<option value="<?php echo esc_attr( $key ); ?>"
					<?php
					if ( ! empty( $field['value'] ) && is_array( $field['value'] ) ) {
						// phpcs:ignore WordPress.PHP.StrictInArray
						selected( in_array( $key, $field['value'] ), true );
					}
					?>
				><?php echo esc_html( $value ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php
	}

	/**
	 * Displays label and checkbox input field.
	 *
	 * @param string $key
	 * @param array  $field
	 */
	public static function input_checkbox( $key, $field ) {
		if ( ! empty( $field['name'] ) ) {
			$name = $field['name'];
		} else {
			$name = $key;
		}
		?>
		<p class="form-field form-field-checkbox">
			<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( wp_strip_all_tags( $field['label'] ) ); ?></label>
			<input type="checkbox" class="checkbox" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $key ); ?>" value="1" <?php isset( $field['value'] ) ? checked( $field['value'], 1 ) : ''; ?> />
			<?php if ( ! empty( $field['description'] ) ) : ?>
				<span class="description"><?php echo wp_kses_post( $field['description'] ); ?></span>
			<?php endif; ?>
		</p>
		<?php
	}

	/**
	 * Displays label and author select field.
	 *
	 * @param string $key
	 * @param array  $field
	 */
	public static function input_author( $key, $field ) {
		global $thepostid, $post;

		if ( ! $post || $thepostid !== $post->ID ) {
			$the_post  = get_post( $thepostid );
			$author_id = $the_post->post_author;
		} else {
			$author_id = $post->post_author;
		}

		$posted_by = get_user_by( 'id', $author_id );
		$name      = ! empty( $field['name'] ) ? $field['name'] : $key;
		?>
		<p class="form-field form-field-author">
			<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( wp_strip_all_tags( $field['label'] ) ); ?>:</label>
			<span class="current-author">
				<?php
				if ( $posted_by ) {
					$user_string = sprintf(
						// translators: Used in user select. %1$s is the user's display name; #%2$s is the user ID; %3$s is the user email.
						esc_html__( '%1$s (#%2$s – %3$s)', 'wp-job-manager' ),
						htmlentities( $posted_by->display_name ),
						absint( $posted_by->ID ),
						$posted_by->user_email
					);
					echo '<a href="' . esc_url( admin_url( 'user-edit.php?user_id=' . absint( $author_id ) ) ) . '">#' . absint( $author_id ) . ' &ndash; ' . esc_html( $posted_by->user_login ) . '</a>';
				} else {
					$user_string = __( 'Guest User', 'wp-job-manager' );
					echo esc_html( $user_string );
				}
				?>
				<a href="#" class="change-author button button-small"><?php esc_html_e( 'Change', 'wp-job-manager' ); ?></a>
			</span>
			<span class="hidden change-author">
				<select class="wpjm-user-search" id="job_manager_user_search" name="<?php echo esc_attr( $name ); ?>" data-placeholder="<?php esc_attr_e( 'Guest', 'wp-job-manager' ); ?>" data-allow_clear="true">
					<option value="<?php echo esc_attr( $author_id ); ?>" selected="selected"><?php echo esc_html( htmlspecialchars( $user_string ) ); ?></option>
				</select>
			</span>
		</p>
		<?php
	}

	/**
	 * Displays label and radio input field.
	 *
	 * @param string $key
	 * @param array  $field
	 */
	public static function input_radio( $key, $field ) {
		if ( ! empty( $field['name'] ) ) {
			$name = $field['name'];
		} else {
			$name = $key;
		}
		?>
		<p class="form-field form-field-checkbox">
			<label><?php echo esc_html( wp_strip_all_tags( $field['label'] ) ); ?></label>
			<?php foreach ( $field['options'] as $option_key => $value ) : ?>
				<label><input type="radio" class="radio" name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key ); ?>" value="<?php echo esc_attr( $option_key ); ?>" <?php checked( $field['value'], $option_key ); ?> /> <?php echo esc_html( $value ); ?></label>
			<?php endforeach; ?>
			<?php if ( ! empty( $field['description'] ) ) : ?>
				<span class="description"><?php echo wp_kses_post( $field['description'] ); ?></span>
			<?php endif; ?>
		</p>
		<?php
	}
}