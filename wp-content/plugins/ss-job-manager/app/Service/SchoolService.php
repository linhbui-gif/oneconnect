<?php
namespace App\Service;

class SchoolService{

    private $text_custom_fields;
    private $tax_fields;
    private $student_tax_fields;
    public function __construct()
    {
        $this->text_custom_fields = ['_school_address', '_school_website', '_school_email', 
    '_school_phone', '_school_position', '_school_street_building', '_school_building_number', '_school_additional_information', '_company_additional_information'];
        $this->tax_fields = [];
        $this->student_tax_fields = ['resume_skill', 'job_listing_type', 'workplace', 'shift', 'job_function', 'working_location', 'start_day'];
        add_action( 'wp_ajax_save_school_information', array($this,'ajax_save_school_information'));
        add_action( 'wp_ajax_nopriv_save_school_information', array($this,'ajax_save_school_information'));
        add_shortcode('school_information',array($this,'render_school_information'));
        add_shortcode('student_cv',array($this,'render_student_cv'));
        add_shortcode('student_job_preference',array($this,'render_student_job_preference'));
        add_shortcode('student_list',array($this,'render_student_list'));
        add_action( 'wp_ajax_save_student_information', array($this,'ajax_save_student_information'));
        add_action( 'wp_ajax_nopriv_save_school_information', array($this,'ajax_save_student_information'));
    }

    function ajax_save_school_information(){
        if (!is_user_logged_in()) {
            wp_send_json_error('Please login to save information', 401);
            wp_die();
        }
        $user = wp_get_current_user();
        $user_id = $user->ID;
        if ( !in_array( 'school', (array) $user->roles ) ) {
            wp_send_json_error('You are not school', 403);
            wp_die();
        }
        if(!isset($_POST['schoolData'])){
            wp_send_json_error('Invalid data', 400);
            wp_die();
        }
        $schoolData = $_POST['schoolData'];
        $post_args = array(
            'post_type' => 'school',
            'post_title' => sanitize_text_field($schoolData['schoolName']),
            'post_author' => $user_id
        );
        $school_args = array (
            'post_type' => 'school',
            'author'        =>  $user_id,
            'posts_per_page'    => 1,
            'post_status' => array('verified', 'not_verified')
        );

        $schooles = get_posts($school_args);
        if(count($schooles) > 0){
            $post_id = $schooles[0]->ID;
            if ($post = get_post($post_id)){
                $post_args['ID'] = $post_id;    
                wp_update_post($post_args);
            }
        }
        else{
            $post_args['post_status'] = 'not_verified';
            $post_id = wp_insert_post($post_args);
        }
        foreach($this->text_custom_fields as $key => $value){
            if(empty($schoolData[$value])) continue;
            update_post_meta($post_id, $value, sanitize_text_field($schoolData[$value]));
        }
        foreach($this->tax_fields as $key => $value){
            if(empty($schoolData[$value])) continue;
            $school_taxs = $schoolData[$value];
            wp_set_object_terms($post_id, $school_taxs, $value, false);
        }
        wp_send_json_success();
        wp_die();
    }

    function render_school_information(){
        wp_enqueue_script('school-js', plugins_url('/assets/js/school-information.js',JOB_MANAGER_FILE), array('jquery'), '1.0.0', true);  
        $user = wp_get_current_user();
        $user_id = $user->ID;
        $school_args = array (
            'post_type' => 'school',
            'author' =>  $user_id,
            'posts_per_page'    => 1,
            'post_status' => array('verified', 'not_verified')
        );

        $schooles = get_posts($school_args);
        $school_id = count($schooles) > 0 ? $schooles[0]->ID : -1;
        ob_start();
        include JOB_MANAGER_VIEW_PATH . '/schools/school_information.php';
        return ob_get_clean();
    }

    function render_student_cv(){
        wp_enqueue_script('student-cv-js', plugins_url('/assets/js/student-cv.js',JOB_MANAGER_FILE), array('jquery'), '1.0.0', true);  
        
        $user = wp_get_current_user();
        $user_id = $user->ID;
        if(isset($_GET['resumeId'])){
            $resume_id = $_GET['resumeId'];
            global $wpdb;
            $resume = $wpdb->get_row("SELECT * from {$wpdb->prefix}posts where post_author = $user_id and ID = $resume_id and post_type = 'resume'");
            if(!empty($resume)){
                $script_data_array = array(
                    'resumeId' => $resume_id,
                );
                wp_localize_script('student-cv-js', 'data', $script_data_array);
                ob_start();
                include JOB_MANAGER_VIEW_PATH . '/schools/edit_student_cv.php';
                return ob_get_clean();
            }
        }
        ob_start();
        include JOB_MANAGER_VIEW_PATH . '/schools/new_student_cv.php';
        return ob_get_clean();
    }

    function render_student_list(){
        // wp_enqueue_script('student-cv-js', plugins_url('/assets/js/student-cv.js',JOB_MANAGER_FILE), array('jquery'), '1.0.0', true);  
        $user = wp_get_current_user();
        $user_id = $user->ID;
        $resume_args = array (
            'post_type' => 'resume',
            'author'        =>  $user_id,
            'posts_per_page'    => 1,
            'post_status' => array('verified', 'not_verified')
        );

        $resumes = get_posts($resume_args);
        // $resume_id = count($resumes) > 0 ? $resumes[0]->ID : -1;
        ob_start();
        include JOB_MANAGER_VIEW_PATH . '/schools/student_list.php';
        return ob_get_clean();
    }

    function ajax_save_student_information(){
        if (!is_user_logged_in()) {
            wp_send_json_error('Please login to save information', 401);
            wp_die();
        }
        $user = wp_get_current_user();
        $user_id = $user->ID;
        //check resume role
        if ( !in_array( 'school', (array) $user->roles ) ) {
            wp_send_json_error('You are not school', 403);
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

        if(isset($_POST['resumeId'])){
            $resume_id = $_POST['resumeId'];
            $resume = get_post($resume_id);
            if(!empty($resume)){
                $post_id = $resume_id;
                if ($post = get_post($post_id)){
                    $post_args['ID'] = $post_id;    
                    wp_update_post($post_args);
                }
            }
        }

        if(!isset($post_id)){
            $post_args['post_status'] = 'not_verified';
            $post_id = wp_insert_post($post_args);
        }
        
        $post = get_post($post_id);
        do_action( 'resume_manager_save_resume', $post_id, $post );
        update_post_meta($post_id, '_is_student', 1);
        foreach($this->student_tax_fields as $key => $value){
            if(!isset($_POST[$value])) continue;
            $resume_taxs = $_POST[$value];
            wp_set_object_terms($post_id, $resume_taxs, $value, false);
        }
        wp_send_json_success();
        wp_die();
    }

}