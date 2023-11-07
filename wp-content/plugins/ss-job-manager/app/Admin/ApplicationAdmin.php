<?php
namespace App\Admin;

class ApplicationAdmin{
    public function __construct()
    {
        add_filter('job_application_statuses', array($this, 'job_manager_applications_job_application_statuses'), 101, 1);  
        add_filter('job_manager_applications_job_application_fields', array($this, 'job_manager_applications_job_application_fields'), 101, 1);  
        add_action( 'init', [ $this, 'register_application_stataus' ], 0 );     
        add_action( 'manage_job_application_posts_custom_column', [ $this, 'custom_columns' ], 10);
        add_filter( 'manage_edit-job_application_columns', [ $this, 'columns' ] , 20);
    }
    public function columns( $columns ) {
		if ( ! is_array( $columns ) ) {
			$columns = [];
		}
		unset( $columns['online_resume'], $columns['attachment'], $columns['job_application_actions'] );
		$columns['resume']  = __( 'Resume', 'wp-job-manager-applications' );
        $columns['job_application_actions'] = __( 'Actions', 'wp-job-manager-applications' );
		return $columns;
	}
    public function custom_columns( $column ) {
		global $post;
        switch ( $column ){
            case 'resume':
				if ( ( $resume_id = get_job_application_resume_id( $post->ID ) ) && $share_link = get_edit_post_link( $resume_id ) ) {
					echo '<a href="' . esc_attr( $share_link ) . '" target="_blank" class="job-application-resume">' . get_the_title( $resume_id ) . '</a>';
				} else {
					echo '<span class="na">&ndash;</span>';
				}
				break;
        }
    }

    function job_manager_applications_job_application_statuses($statuses){
        $statuses['not_verified'] = _x( 'Not Verified', 'job_application', 'wp-job-manager-applications' );
        $statuses['reviewing'] = _x( 'Reviewing', 'job_application', 'wp-job-manager-applications' );
        return $statuses;
    }
    function job_manager_applications_job_application_fields($fields){
        $fields['_resume_id']['label'] =  __( 'Resume Id', 'wp-job-manager-applications' );
        unset($fields['_attachment']);
        unset($fields['_rating']);
        return $fields;
    }

    function register_application_stataus(){
        register_post_status(
            'not_verified',
            [
                'label'                     => _x( 'Not Verified', 'post status', 'wp-job-manager' ),
                'public'                    => true,
                'protected'                 => true,
                'exclude_from_search'       => true,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                // translators: Placeholder %s is the number of expired posts of this type.
                'label_count'               => _n_noop( 'Not Verified <span class="count">(%s)</span>', 'Not Verified <span class="count">(%s)</span>', 'wp-job-manager' ),
            ]
        );
        register_post_status(
            'reviewing',
            [
                'label'                     => _x( 'Reviewing', 'post status', 'wp-job-manager' ),
                'public'                    => true,
                'protected'                 => true,
                'exclude_from_search'       => true,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                // translators: Placeholder %s is the number of expired posts of this type.
                'label_count'               => _n_noop( 'Reviewing <span class="count">(%s)</span>', 'Reviewing <span class="count">(%s)</span>', 'wp-job-manager' ),
            ]
        );
    }

}