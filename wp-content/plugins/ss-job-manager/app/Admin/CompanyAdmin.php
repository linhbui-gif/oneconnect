<?php
namespace App\Admin;

use App\Admin\GeneralAdmin;
use App\Helpers\DatabaseHelper;
class CompanyAdmin extends GeneralAdmin{
    public function __construct()
    {
		DatabaseHelper::create_company_admin_manager();
		add_action( 'save_post', [ $this, 'save_post' ], 1, 2 );
        add_action( 'init', [ $this, 'register_post_types' ], 20 );
        add_filter( 'enter_title_here', [ $this, 'enter_title_here' ], 1, 2 );
        add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
		add_action( 'job_manager_save_company', [ $this, 'save_company_data' ], 1, 2 );
		add_action( 'save_post', array($this,'save_meta_box_data'));
        add_action( 'add_meta_boxes', array($this,'admin_manager_metabox') );
		
    }

	function admin_manager_metabox(){
        add_meta_box(
            'admin_manager_meta',
            'Admin Manager',
            array($this,'admin_manager_meta_box_callback'),
            'company'
        );
    }

	function save_meta_box_data( $post_id ) {

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( isset( $_POST['post_type'] ) && 'company' == $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return;
            }
        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }
        }
        if(isset($_POST['admin-manager'])){
            $admin = $_POST['admin-manager'];
			if(!empty($admin)){
				global $wpdb;
				$existed_company_admin_manager = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}company_admin_manager WHERE company_id = $post_id AND admin_id = $admin");
				if(empty($existed_company_admin_manager)){
					$wpdb->insert($wpdb->prefix.'company_admin_manager',array('company_id'=>$post_id,'admin_id'=>$admin));
				}
				else{
					$wpdb->update($wpdb->prefix.'company_admin_manager',array('id'=>$existed_company_admin_manager->id),array( 'company_id' => $post_id, 'admin_id'=> $admin));
				}
			}

        }
    }
	function admin_manager_meta_box_callback(){
		global $wpdb;
		global $post;
		$company_admin_manager = $wpdb->get_row("SELECT * from {$wpdb->prefix}company_admin_manager where company_id = $post->ID");
		$admins = get_users(
			array(
				'role' => 'company_manager',
			)
		);
		?>
		<div  class="postbox ">
    		<div class="inside">
				<select name="admin-manager" >
					<option value="0">All</option>
					<?php foreach($admins as $admin):?>
						<option <?php echo !empty($company_admin_manager) && $company_admin_manager->admin_id == $admin->ID ? 'selected' : ''?> value="<?php echo $admin->ID?>"><?php echo $admin->user_email?></option>
					<?php endforeach;?>
				</select>
			</div>
		</div>
		<?php

	}
    public function enter_title_here( $text, $post ) {
		if ( $post->post_type == 'company' ) {
			return __( 'Company name', 'wp-job-manager-applications' );
		}
		return $text;
	}

    function get_company_statuses() {
        return apply_filters(
            'company_statuses',
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
		if ( post_type_exists( 'company' ) ) {
			return;
		}

		$plural   = __( 'Companies', 'wp-job-manager-applications' );
		$singular = __( 'Company', 'wp-job-manager-applications' );

		register_post_type(
			'company',
			apply_filters(
				'register_post_type_company',
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
					'description'         => __( 'This is where you can edit and view companies.', 'wp-job-manager-applications' ),
					'public'              => true,
					'show_ui'             => true,
					'capability_type'     => 'company',
					'map_meta_cap'        => true,
					'publicly_queryable'  => false,
					'exclude_from_search' => true,
					'hierarchical'        => true,
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

		$company_statuses = $this->get_company_statuses();

		foreach ( $company_statuses as $name => $label ) {
			register_post_status(
				$name,
				apply_filters(
					'register_company_status',
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

    public function add_meta_boxes() {
		add_meta_box( 'company_status', __( 'Company Status', 'wp-job-manager-applications' ), [ $this, 'company_status' ], 'company', 'side', 'high' );
		add_meta_box( 'company_data', __( 'Company Data', 'wp-job-manager-applications' ), [ $this, 'company_data' ], 'company', 'normal', 'high' );
		remove_meta_box( 'submitdiv', 'company', 'side' );
	}

	/**
	 * Publish meta box
	 */
	public function company_status( $post ) {
		$statuses = $this->get_company_statuses();
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

    public function company_data( $post ) {
		global $post, $thepostid;

		$thepostid = $post->ID;

		echo '<div class="wp_job_manager_meta_data">';

		wp_nonce_field( 'save_meta_data', 'company_nonce' );

		do_action( 'comapany_data_start', $thepostid );

		foreach ( $this->company_fields() as $key => $field ) {
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
				do_action( 'company_input_' . $type, $key, $field );
			}
		}

		do_action( 'comapny_data_end', $thepostid );

		echo '</div>';
	}

    public function company_fields() {
		global $post;

		$fields = apply_filters(
			'job_manager_company_fields',
			[
                '_company_author' => [
					'label'       => __( 'Posted by', 'wp-job-manager-applications' ),
					'type'        => 'author',
					'placeholder' => '',
				],
                '_company_address' => [
					'label'       => '',
					'placeholder' => 'Address',
				],
                '_company_liscense' => [
					'label'       => '',
					'placeholder' => 'Liscense numbers',
				],
                '_company_industry' => [
					'label'       => '',
					'placeholder' => 'Industry',
				],
                '_company_website' => [
					'label'       => '',
					'placeholder' => 'Website',
				],
				'_company_email'        => [
					'label'       => '',
					'placeholder' => 'Comapny Email',
					'description' => '',
				],
				'_company_phone' => [
					'label'       => '',
					'placeholder' => 'Company Phone',
				],
                '_company_title' => [
					'label'       => '',
					'placeholder' => 'Title',
				],
                '_company_street_building' => [
					'label'       => '',
					'placeholder' => 'Street/Building',
				],
                '_company_building_number' => [
					'label'       => '',
					'placeholder' => 'Address/Building number',
				],
                '_company_additional_information' => [
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
		if ( empty( $_POST['company_nonce'] ) || ! wp_verify_nonce( $_POST['company_nonce'], 'save_meta_data' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( $post->post_type !== 'company' ) {
			return;
		}

		do_action( 'job_manager_save_company', $post_id, $post );
	}
    public function save_company_data( $post_id, $post ) {
		global $wpdb;
		foreach ( $this->company_fields() as $key => $field ) {

			if ( '_company_author' === $key ) {
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
}