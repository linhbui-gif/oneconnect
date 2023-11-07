<?php
namespace App\Admin;

class SchoolAdmin extends GeneralAdmin{
    public function __construct()
    {
        add_action( 'init', [ $this, 'register_post_types' ], 20 );
        add_filter( 'enter_title_here', [ $this, 'enter_title_here' ], 1, 2 );
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
		add_action( 'job_manager_save_school', [ $this, 'save_school_data' ], 1, 2 );
		add_action( 'save_post', [ $this, 'save_post' ], 1, 2 );
    }

    public function enter_title_here( $text, $post ) {
		if ( $post->post_type == 'school' ) {
			return __( 'School name', 'wp-job-manager-applications' );
		}
		return $text;
	}
	function get_school_statuses() {
        return apply_filters(
            'school_statuses',
            [
                'not_verified'         => _x( 'Not Verified', 'job_application', 'wp-job-manager-applications' ),
                'verified' => _x( 'Verified', 'job_application', 'wp-job-manager-applications' ),
            ]
        );
    }
    /**
	 * register_post_types function.
	 */
	public function register_post_types() {
		if ( post_type_exists( 'school' ) ) {
			return;
		}

		$plural   = __( 'Schools', 'wp-job-manager-applications' );
		$singular = __( 'school', 'wp-job-manager-applications' );

		register_post_type(
			'school',
			apply_filters(
				'register_post_type_school',
				[
					'labels'              => [
						'name'               => $plural,
						'singular_name'      => $singular,
						'menu_name'          => $plural,
						'all_items'          => sprintf( __( 'All %s', 'wp-job-manager-applications' ), $plural ),
						'add_new'            => __( 'Add New', 'wp-job-manager-applications' ),
						'add_new_item'       => sprintf( __( 'Add %s', 'wp-job-manager-applications' ), $singular ),
						'edit'               => __( 'Edit', 'wp-job-manager-applications' ),
						'edit_item'          => sprintf( __( 'Edit %s', 'wp-job-manager-applications' ), $singular ),
						'new_item'           => sprintf( __( 'New %s', 'wp-job-manager-applications' ), $singular ),
						'view'               => sprintf( __( 'View %s', 'wp-job-manager-applications' ), $singular ),
						'view_item'          => sprintf( __( 'View %s', 'wp-job-manager-applications' ), $singular ),
						'search_items'       => sprintf( __( 'Search %s', 'wp-job-manager-applications' ), $plural ),
						'not_found'          => sprintf( __( 'No %s found', 'wp-job-manager-applications' ), $plural ),
						'not_found_in_trash' => sprintf( __( 'No %s found in trash', 'wp-job-manager-applications' ), $plural ),
						'parent'             => sprintf( __( 'Parent %s', 'wp-job-manager-applications' ), $singular ),
					],
					'description'         => __( 'This is where you can edit and view schools.', 'wp-job-manager-applications' ),
					'public'              => false,
					'show_ui'             => true,
					'capability_type'     => 'company',
					'map_meta_cap'        => true,
					'publicly_queryable'  => false,
					'exclude_from_search' => true,
					'hierarchical'        => false,
					'rewrite'             => false,
					'query_var'           => false,
					'supports'            => [ 'title', 'custom-fields', 'editor' ],
					'has_archive'         => false,
					'show_in_nav_menus'   => true,
					'delete_with_user'    => true,
					'menu_position'       => 31,
				]
			)
		);

		$school_statuses = $this->get_school_statuses();

		foreach ( $school_statuses as $name => $label ) {
			register_post_status(
				$name,
				apply_filters(
					'register_school_status',
					[
						'label'                     => $label,
						'public'                    => true,
						'exclude_from_search'       => 'archived' === $name ? true : false,
						'show_in_admin_all_list'    => 'archived' === $name ? false : true,
						'show_in_admin_status_list' => true,
						'label_count'               => _n_noop( $label . ' <span class="count">(%s)</span>', $label . ' <span class="count">(%s)</span>', 'wp-job-manager-applications' ),
					],
					$name
				)
			);
		}
	}  

	public function school_data( $post ) {
		global $post, $thepostid;

		$thepostid = $post->ID;

		echo '<div class="wp_job_manager_meta_data">';

		wp_nonce_field( 'save_meta_data', 'school_nonce' );

		do_action( 'school_data_start', $thepostid );

		foreach ( $this->school_fields() as $key => $field ) {
			$type = ! empty( $field['type'] ) ? $field['type'] : 'text';

			if ( ! isset( $field['value'] ) && metadata_exists( 'post', $thepostid, $key ) ) {
				$field['value'] = get_post_meta( $thepostid, $key, true );
			}

			if ( ! isset( $field['value'] ) && isset( $field['default'] ) ) {
				$field['value'] = $field['default'];
			} elseif ( ! isset( $field['value'] ) ) {
				$field['value'] = '';
			}

			if ( method_exists( $this, 'input_' . $type ) ) {
				call_user_func( [ $this, 'input_' . $type ], $key, $field );
			} else {
				do_action( 'school_input_' . $type, $key, $field );
			}
		}

		do_action( 'school_data_end', $thepostid );

		echo '</div>';
	}

    public function school_fields() {

		$fields = apply_filters(
			'job_manager_school_fields',
			[
                '_school_author' => [
					'label'       => __( 'Posted by', 'wp-job-manager-applications' ),
					'type'        => 'author',
					'placeholder' => '',
				],
                '_school_address' => [
					'label'       => '',
					'placeholder' => 'Address',
				],
                '_school_website' => [
					'label'       => '',
					'placeholder' => 'Website',
				],
				'_school_email'        => [
					'label'       => '',
					'placeholder' => 'School Email',
					'description' => '',
				],
				'_school_phone' => [
					'label'       => '',
					'placeholder' => 'School Phone',
				],
                '_school_position' => [
					'label'       => '',
					'placeholder' => 'Position',
				],
                '_school_street_building' => [
					'label'       => '',
					'placeholder' => 'Street/Building',
				],
                '_school_building_number' => [
					'label'       => '',
					'placeholder' => 'Address/Building number',
				],
                '_school_additional_information' => [
					'label'       => '',
					'placeholder' => 'Additional address information',
				],
			]
		);

		return $fields;
	}

    public function save_post( $post_id, $post ) {
		if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( is_int( wp_is_post_revision( $post ) ) ) {
			return;
		}
		if ( is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}
		if ( empty( $_POST['school_nonce'] ) || ! wp_verify_nonce( $_POST['school_nonce'], 'save_meta_data' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( $post->post_type !== 'school' ) {
			return;
		}

		do_action( 'job_manager_save_school', $post_id, $post );
	}
    public function save_school_data( $post_id, $post ) {
		global $wpdb;
		foreach ( $this->school_fields() as $key => $field ) {

			if ( '_school_author' === $key ) {
				$wpdb->update( $wpdb->posts, [ 'post_author' => $_POST[ $key ] > 0 ? absint( $_POST[ $key ] ) : 0 ], [ 'ID' => $post_id ] );
			} elseif ( 'post_parent' === $key ) {
				// WP Handles this field
			} else {
				$type = ! empty( $field['type'] ) ? $field['type'] : '';

				switch ( $type ) {
					case 'textarea':
						update_post_meta( $post_id, $key, wp_kses_post( stripslashes( $_POST[ $key ] ) ) );
						break;
					case 'checkbox':
						if ( isset( $_POST[ $key ] ) ) {
							update_post_meta( $post_id, $key, 1 );
						} else {
							update_post_meta( $post_id, $key, 0 );
						}
						break;
					default:
						if ( is_array( $_POST[ $key ] ) ) {
							update_post_meta( $post_id, $key, array_filter( array_map( 'sanitize_text_field', $_POST[ $key ] ) ) );
						} else {
							update_post_meta( $post_id, $key, sanitize_text_field( $_POST[ $key ] ) );
						}
						break;
				}
			}
		}
	}

	public function add_meta_boxes() {
		add_meta_box( 'school_status', __( 'School Status', 'wp-job-manager-applications' ), [ $this, 'school_status' ], 'school', 'side', 'high' );
		add_meta_box( 'school_data', __( 'School Data', 'wp-job-manager-applications' ), [ $this, 'school_data' ], 'school', 'normal', 'high' );
		remove_meta_box( 'submitdiv', 'school', 'side' );
	}

	public function school_status( $post ) {
		$statuses = $this->get_school_statuses();
		?>
		<div class="submitbox" id="submitpost">
			<div id="minor-publishing">
				<div id="misc-publishing-actions">
					<div class="misc-pub-section misc-pub-post-status">
						<div id="post-status-select">
							<select name='post_status' id='post_status'>
								<?php
								foreach ( $statuses as $key => $label ) {
									$selected = selected( $post->post_status, $key, false );
									echo "<option{$selected} value='" . esc_attr( $key ) . "'>" . esc_html( $label ) . '</option>';
								}
								?>
							</select>
						</div>
					</div>
				</div>
			</div>
			<div id="major-publishing-actions">
				<div id="delete-action">
					<a class="submitdelete deletion" href="<?php echo get_delete_post_link( $post->ID ); ?>"><?php _e( 'Move to Trash', 'wp-job-manager-applications' ); ?></a>
				</div>
				<div id="publishing-action">
					<span class="spinner"></span>
					<input name="save" class="button button-primary" type="submit" value="<?php _e( 'Save', 'wp-job-manager-applications' ); ?>">
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<?php
	}
}