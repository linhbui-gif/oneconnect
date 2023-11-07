<?php
namespace App\Admin;
use App\Helpers\CommonHelper;
class JobAdmin{
    public function __construct()
    {
        add_filter('job_manager_job_listing_data_fields', array($this, 'job_manager_job_listing_data_fields'), 101, 1);
        add_filter('resume_manager_resume_fields', array($this, 'resume_manager_resume_fields'), 101, 1);
        add_filter('register_taxonomy_job_listing_type_object_type', array($this, 'register_taxonomy_job_listing_type_object_type'), 101, 1);
        add_filter('job_listing_post_statuses', array($this, 'job_listing_post_statuses'), 101, 1);
        add_action( 'init', [ $this, 'register_job_stataus' ], 0 );
        add_filter( 'posts_where', array($this,'custom_job_filter_where'));
    }
    function custom_job_filter_where($where){
        global $wpdb, $pagenow, $typenow;
        if ( 'job_listing' === $typenow && $pagenow === 'edit.php' ) {
            $user_id = get_current_user_id();
            $prepared_statement  = $wpdb->prepare("SELECT p.post_author FROM {$wpdb->prefix}company_admin_manager cm inner join {$wpdb->prefix}posts p on cm.company_id = p.ID WHERE cm.admin_id = $user_id");
            $company_users = $wpdb->get_col( $prepared_statement );
            if(count($company_users) == 0) return $where;
            $where .= " AND post_author IN (" . implode(',', $company_users) . ")";
        }
        return $where;
    }

    function register_job_stataus(){
        register_post_status(
            'reject',
            [
                'label'                     => _x( 'Reject', 'post status', 'wp-job-manager' ),
                'public'                    => true,
                'protected'                 => true,
                'exclude_from_search'       => true,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                // translators: Placeholder %s is the number of expired posts of this type.
                'label_count'               => _n_noop( 'Rejected <span class="count">(%s)</span>', 'Rejected <span class="count">(%s)</span>', 'wp-job-manager' ),
            ]
        );
    }
    
    function register_taxonomy_job_listing_type_object_type($types){
        array_push($types, 'resume');
        return $types;
    }

    function job_listing_post_statuses($statuses){
        unset($statuses['draft']);
        unset($statuses['preview']);
        unset($statuses['pending_payment']);
        $statuses['reject'] = _x( 'Reject', 'post status', 'wp-job-manager' );
        return $statuses;
    }

    function job_manager_job_listing_data_fields($fields){
        $fields['_job_location']['label'] =  __( 'Address', 'wp-job-manager' );
        $fields['_job_gender']   = [
            'label'         => __( 'Gender', 'wp-job-manager-resumes' ),
            'type'   =>  'select',
            'options'   => [
                1 => 'Male',
                2 => 'Female'
            ],
            'priority'      => 2,
            'data_type'     => 'integer',
            'show_in_admin' => true,
            'show_in_rest'  => true
        ];
        $fields['_job_certification']   = [
            'label'         => __( 'Certificate', 'wp-job-manager-resumes' ),
            'type'   =>  'text',
            'priority'      => 2,
            'data_type'     => 'string',
            'show_in_admin' => true,
            'show_in_rest'  => true
        ];
        $fields['_job_benefit']   = [
            'label'         => __( 'Other benefit', 'wp-job-manager-resumes' ),
            'type'   =>  'text',
            'priority'      => 5,
            'data_type'     => 'textarea',
            'show_in_admin' => true,
            'show_in_rest'  => true
        ];
        unset($fields['_company_twitter']);
        unset($fields['_company_video']);
        unset($fields['_company_name']);
        unset($fields['_company_website']);
        unset($fields['_company_tagline']);
        return $fields;
    }

    function resume_manager_resume_fields($fields){
        // unset($fields['_candidate_photo']);
        unset($fields['_candidate_title']);
        unset($fields['_candidate_video']);
        unset($fields['_resume_expires']);
        unset($fields['_featured']);
        $fields['_candidate_birthday'] = [
            'label'              => __( 'Date of Birth', 'wp-job-manager-resumes' ),
            'placeholder'        => __( 'yyyy-mm-dd', 'wp-job-manager-resumes' ),
            'priority'           => 8,
            'data_type'          => 'string',
            'show_in_admin'      => true,
            'show_in_rest'       => true,
            'sanitize_callback'  => [ 'WP_Job_Manager_Post_Types', 'sanitize_meta_field_date' ],
        ];
        $fields['_candidate_phone'] = [
            'label'         => __( 'Contact Phone', 'wp-job-manager-resumes' ),
            'placeholder'   => __( '001613xxxx', 'wp-job-manager-resumes' ),
            'priority'      => 2,
            'data_type'     => 'string',
            'show_in_admin' => true,
            'show_in_rest'  => true,
        ];

        $fields['_candidate_gender']   = [
            'label'         => __( 'Gender', 'wp-job-manager-resumes' ),
            'type'   =>  'select',
            'options'   => [
                1 => 'Male',
                2 => 'Female'
            ],
            'value' => 1,
            'priority'      => 2,
            'data_type'     => 'integer',
            'show_in_admin' => true,
            'show_in_rest'  => true,
            'sanitize_callback'  => [ 'WP_Job_Manager_Post_Types', 'sanitize_meta_field_date' ],
        ];

        $fields['_is_student'] = [
            'label'              => __( 'Is Student?', 'wp-job-manager-resumes' ),
            'type'               => 'checkbox',
            'priority'           => 7,
            'data_type'          => 'integer',
            'show_in_admin'      => true,
            'show_in_rest'       => true
        ];

        return $fields;
    }
}