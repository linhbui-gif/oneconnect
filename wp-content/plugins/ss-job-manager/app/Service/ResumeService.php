<?php
namespace App\Service;

class ResumeService{

    private $tax_fields;
    public function __construct()
    {
        $this->tax_fields = ['resume_skill', 'job_listing_type', 'workplace', 'shift', 'job_function', 'working_location', 'start_day'];
        add_action( 'wp_ajax_save_resume_information', array($this,'ajax_save_resume_information'));
        add_action( 'wp_ajax_nopriv_save_resume_information', array($this,'ajax_save_resume_information'));
        add_action( 'wp_ajax_save_resume_job_preference', array($this,'ajax_save_resume_job_preference'));
        add_action( 'wp_ajax_nopriv_save_resume_job_preference', array($this,'ajax_save_resume_job_preference'));
        add_shortcode('candidate_cv',array($this,'render_candidate_cv'));
        add_shortcode('candidate_job_preference',array($this,'render_candidate_job_preference'));
    }

    function ajax_save_resume_information(){
        if (!is_user_logged_in()) {
            wp_send_json_error('Please login to save information', 401);
            wp_die();
        }
        $user = wp_get_current_user();
        $user_id = $user->ID;
        //check resume role
        if ( !in_array( 'candidate', (array) $user->roles ) ) {
            wp_send_json_error('You are not candidate', 403);
            wp_die();
        }
        $post_args = array(
            'post_type' => 'resume',
            'post_author' => $user_id
        );

        if(isset($_POST['candidateName'])){
            $post_args['post_title'] = sanitize_text_field($_POST['candidateName']);
        }
        if(isset($_POST['candidateAbout'])){
            $post_args['post_content'] = sanitize_text_field($_POST['candidateAbout']);
        }

        $resume_args = array (
            'post_type' => 'resume',
            'author'        =>  $user_id,
            'posts_per_page'    => 1,
            'post_status' => array('verified', 'not_verified')
        );

        $resumes = get_posts($resume_args);
        if(count($resumes) > 0){
            $post_id = $resumes[0]->ID;
            if ($post = get_post($post_id)){
                $post_args['ID'] = $post_id;    
                wp_update_post($post_args);
            }
        }
        else{
            $post_args['post_status'] = 'not_verified';
            $post_id = wp_insert_post($post_args);
        }
        $post = get_post($post_id);
        do_action( 'resume_manager_save_resume', $post_id, $post );
        foreach($this->tax_fields as $key => $value){
            if(!isset($_POST[$value])) continue;
            $resume_taxs = $_POST[$value];
            wp_set_object_terms($post_id, $resume_taxs, $value, false);
        }
        wp_send_json_success();
        wp_die();
    }

    function ajax_save_resume_job_preference(){
        if (!is_user_logged_in()) {
            wp_send_json_error('Please login to save information', 401);
            wp_die();
        }
        $user = wp_get_current_user();
        $user_id = $user->ID;
        //check resume role
        if ( !in_array( 'candidate', (array) $user->roles ) ) {
            wp_send_json_error('You are not candidate', 403);
            wp_die();
        }
        $post_args = array(
            'post_type' => 'resume',
            'post_author' => $user_id
        );

        $resume_args = array (
            'post_type' => 'resume',
            'author'        =>  $user_id,
            'posts_per_page'    => 1,
            'post_status' => array('verified', 'not_verified')
        );

        $resumes = get_posts($resume_args);
        if(count($resumes) > 0){
            $post_id = $resumes[0]->ID;
            if ($post = get_post($post_id)){
                $post_args['ID'] = $post_id;    
                wp_update_post($post_args);
            }
        }
        else{
            $post_args['post_status'] = 'not_verified';
            $post_id = wp_insert_post($post_args);
        }
        $post = get_post($post_id);
        foreach($this->tax_fields as $key => $value){
            if(!isset($_POST[$value])) continue;
            $company_taxs = $_POST[$value];
            wp_set_object_terms($post_id, $company_taxs, $value, false);
        }
        wp_send_json_success();
        wp_die();
    }
    
    function render_candidate_cv(){
        wp_enqueue_script('candidate-cv-js', plugins_url('/assets/js/candidate-cv.js',JOB_MANAGER_FILE), array('jquery'), '1.0.0', true);  
        $user = wp_get_current_user();
        $user_id = $user->ID;
        $resume_args = array (
            'post_type' => 'resume',
            'author' =>  $user_id,
            'posts_per_page'    => 1,
            'post_status' => array('verified', 'not_verified')
        );

        $resumes = get_posts($resume_args);
        $resume_id = count($resumes) > 0 ? $resumes[0]->ID : -1;
        
        ob_start();
        include JOB_MANAGER_VIEW_PATH . '/candidates/candidate_cv.php';
        return ob_get_clean();
    }

    function render_candidate_job_preference(){
        wp_enqueue_script('candidate-cv-js', plugins_url('/assets/js/candidate-cv.js',JOB_MANAGER_FILE), array('jquery'), '1.0.0', true);  
        $user = wp_get_current_user();
        $user_id = $user->ID;
        $resume_args = array (
            'post_type' => 'resume',
            'author'        =>  $user_id,
            'posts_per_page'    => 1,
            'post_status' => array('verified', 'not_verified')
        );

        $resumes = get_posts($resume_args);
        $resume_id = count($resumes) > 0 ? $resumes[0]->ID : -1;
        ob_start();
        include JOB_MANAGER_VIEW_PATH . '/candidates/candidate_job_preference.php';
        return ob_get_clean();
    }
}