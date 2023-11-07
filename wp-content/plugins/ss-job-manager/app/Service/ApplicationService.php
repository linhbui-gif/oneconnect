<?php
namespace App\Service;

class ApplicationService{
    public function __construct()
    {
        add_action( 'wp_ajax_save_application_information', array($this,'ajax_save_application_information'));
        add_action( 'wp_ajax_nopriv_save_application_information', array($this,'ajax_save_application_information'));
    }

    function ajax_save_application_information(){
        if (!is_user_logged_in()) {
            wp_send_json_error('Please login to apply job', 401);
            wp_die();
        }
        $user = wp_get_current_user();
        $user_id = $user->ID;
        //check resume role
        if ( !in_array( 'candidate', (array) $user->roles ) ) {
            wp_send_json_error('You are not candidate', 403);
            wp_die();
        }

        // check existed resume information
        $resume_args = array (
            'post_type' => 'resume',
            'author'        =>  $user_id,
            'posts_per_page'    => 1,
            'post_status' => array('verified', 'not_verified')
        );

        $resumes = get_posts($resume_args);
        if(count($resumes) == 0 || $resumes[0]->post_status == 'not_verified'){
            wp_send_json_error("Your resume hasn't been verfied yet", 403);
            wp_die();
        }
        
        if(!isset($_POST['jobId'])){
            wp_send_json_error('Missing Job information', 400);
            wp_die();
        }

        // check duplicate 
        $application_args = array (
            'post_type' => 'job_application',
            '_resume_id'        =>  $resumes[0]->ID,
            'post_parent'   => $_POST['jobId'],
            'posts_per_page'    => 1,
            'post_status' => 'any'
        );
        $existing_applications = get_posts($application_args);
        if(count($existing_applications) > 0){
            wp_send_json_error("You applied this job", 400);
            wp_die();
        }

        $create_application_args = array(
            'post_type' => 'job_application',
            'post_title' => $resumes[0]->post_title,
            'post_parent' => $_POST['jobId'],
            'post_status' => 'not_verified',
            'post_author' => $user_id,           
        );       
        $post_id = wp_insert_post($create_application_args);
        update_post_meta($post_id, '_resume_id', $resumes[0]->ID);
        update_post_meta($post_id, '_candidate_email',get_post_meta($resumes[0]->ID, '_candidate_email', true));
        $headers = array('Content-Type: text/html; charset=UTF-8');
        //$to = get_option('admin_email');
        $to = 'info@parkwayconsulting.ca';
        $subject = "[ParwayHR] New Application";
        $candidate_email = $user->user_email;
        $job_name = get_post($_POST['jobId'])->post_title;
        $candidate_url = get_edit_post_link( $resumes[0]->ID );
        $jobId = (int) $_POST['jobId'];
        $job_url = get_edit_post_link($jobId);
        ob_start();
        include JOB_MANAGER_VIEW_PATH . '/emails/email-new-application.php';
        $message = ob_get_contents();
        ob_end_clean();
        $mail = wp_mail($to, $subject, $message, $headers);
        wp_send_json_success();
        wp_die();
    }
}