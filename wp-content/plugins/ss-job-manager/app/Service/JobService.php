<?php
namespace App\Service;

use stdClass;
use WP_Query;

class JobService{
    private $text_custom_fields;
    private $tax_fields;
    public function __construct()
    {
        $this->text_custom_fields = ['_application','_job_location', '_job_expires', '_job_salary', '_job_gender', '_job_certification', '_job_benefit'];
        $this->tax_fields = ['job_listing_type', 'salary_range', 'workplace', 'experience_level', 'shift', 'experience', 'job_function', 'working_location'];
        add_shortcode('job_recommends',array($this,'render_job_recommends'));
        add_shortcode('job_explore',array($this,'render_job_explore'));
        add_action( 'wp_ajax_filter_explore_jobs', array($this,'ajax_filter_explore_jobs'));
        add_action( 'wp_ajax_nopriv_filter_explore_jobs', array($this,'ajax_filter_explore_jobs'));
        add_shortcode('filter_carrer',array($this,'render_filter_carrer'));
        add_action( 'wp_ajax_save_job_information', array($this,'ajax_save_job_information'));
        add_action( 'wp_ajax_nopriv_save_job_information', array($this,'ajax_save_job_information'));
        add_action('template_redirect', [$this, 'enqueue_custom_script'], 999);
        add_shortcode('post_job_detail',array($this,'render_post_job_detail'));
        add_shortcode('post_job_pannel',array($this,'render_post_job_pannel'));
        add_action( 'init', array($this,'edit_job_rewrite_rule'));
        add_filter( 'query_vars', array($this,'prefix_job_query_var'));
        add_shortcode('edit_job_detail',array($this,'render_edit_job_detail'));
    }

    function edit_job_rewrite_rule()
    {
        add_rewrite_endpoint( 'jobId', EP_PAGES );
    }
    function prefix_job_query_var( $vars ) {
        $vars[] = 'jobId';
        return $vars;
    }
    function enqueue_custom_script(){
        if (is_singular('job_listing')) {
            wp_enqueue_script('job-single-js', plugins_url('assets/js/job-single.js', JOB_MANAGER_FILE), array(), '1.0', true);
        }
        
     }
    
    function render_filter_carrer(){
        wp_enqueue_script('filter-carrer-js', plugins_url('/assets/js/filter-carrer.js',JOB_MANAGER_FILE), array('jquery'), '1.0.0', true);  
        ob_start();
        include JOB_MANAGER_VIEW_PATH . '/jobs/filter_carrer.php';
        return ob_get_clean();
    }
    function render_jobs($args){
        $query = new WP_Query( $args );
        $result = new stdClass;
        $result->pages = 0;
        $result->html = '';
        if ( $query->have_posts() ) {
            ob_start();
            ?>
            <div class="row row-small job-card-row scroll-mobile-hori row-box-shadow-1 row-box-shadow-2-hover">
            <?php
            while ( $query->have_posts() ) {
                $query->the_post();
                include JOB_MANAGER_VIEW_PATH . '/job_card.php';
            }
            ?></div> <?php
            wp_reset_postdata();
            return ob_get_clean();
        }
    }

    function render_job_recommends(){
        $args = array(
            'post_type' => 'job_listing',
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => '_featured',
                    'value' => '1',
                    'compare' => '='
                )
            )
        );
        return $this->render_jobs($args);
    }

    function render_job_explore(){
        wp_enqueue_script('explore-job-js', plugins_url('/assets/js/explore-job.js',JOB_MANAGER_FILE), array('jquery'), '1.0.0', true);    
        $query_arg = $this->build_query_job_explore(array());
        ob_start();
        ?>
        <section class="section" id="section_21039222">
            <div class="bg section-bg fill bg-fill bg-loaded"></div>
            <div class="section-content relative">
                <div class="row" id="row-1744104185">
                    <div id="col-1134556518" class="col small-12 large-12">
                        <div class="col-inner">
                            <div id="text-744947651" class="text">
                                <h2 style="margin-bottom: 0;">Find a Jobs</h2>
                                <style>
                                #text-744947651 {
                                    font-size: 1.2rem;
                                    color: rgb(0, 0, 0);
                                }

                                #text-744947651>* {
                                    color: rgb(0, 0, 0);
                                }
                                </style>
                            </div>
                        </div>
                        <style>
                        #col-1134556518>.col-inner {
                            margin: 0px 0px -25px 0px;
                        }

                        @media (min-width:550px) {
                            #col-1134556518>.col-inner {
                            margin: 0px 0px -15px 0px;
                            }
                        }
                        </style>
                    </div>
                </div>
                <div class="row" id="row-265207983">
                    <div id="col-2031670790" class="col small-12 large-12">
                        <div class="col-inner">
                            <div class="tabbed-content tab-careers">
                                <ul class="nav nav-line-bottom nav-normal nav-size-large nav-left" role="tablist">
                                    <li id="tab-explore" class="tab has-icon active" role="presentation">
                                        <a href="#tab_explore" role="tab" aria-selected="true" aria-controls="tab_explore">
                                            <span>Explore</span>
                                        </a>
                                    </li>
                                    <li id="tab-for-you" class="tab has-icon" role="presentation">
                                        <a href="#tab_for-you" role="tab" aria-selected="false" aria-controls="tab_for-you" tabindex="-1">
                                        <span>For You</span>
                                        </a>
                                    </li>
                                    <li id="tab-saved-jobs" class="tab has-icon" role="presentation">
                                        <a href="#tab_saved-jobs" tabindex="-1" role="tab" aria-selected="false" aria-controls="tab_saved-jobs">
                                        <span>Saved Jobs</span>
                                        </a>
                                    </li>
                                </ul>
                                <div class="tab-panels">
                                    <div id="tab_explore" class="panel entry-content active" role="tabpanel" aria-labelledby="tab-explore">
                                        <?php echo $this->render_jobs($query_arg);?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <style>
                #section_21039222 {
                padding-top: 30px;
                padding-bottom: 30px;
                background-color: rgb(232, 245, 255);
                }

                #section_21039222 .ux-shape-divider--top svg {
                height: 150px;
                --divider-top-width: 100%;
                }

                #section_21039222 .ux-shape-divider--bottom svg {
                height: 150px;
                --divider-width: 100%;
                }
            </style>
        </section>
        <?php
        return ob_get_clean();
    }

    function build_query_job_explore(array $params){
        $args = array(
            'post_type' => 'job_listing',
            'post_status' => 'publish',
            'posts_per_page ' => 12,
            'paged' => isset($params['paged']) ? $params['paged'] : 1
        );
        if(!empty($params['keyword'])){
            $args['s'] = $params['keyword'];
        }
        $tax_query = array();
        if(!empty($params['job_type'])){
            $tax_query[] = array(
                'taxonomy' => 'job_listing_type',
                'field' => 'term_id',
                'terms' => $params['job_type'],
            );
        }
        if(!empty($params['salary_range'])){
            $tax_query[] = array(
                'taxonomy' => 'salary_range',
                'field' => 'term_id',
                'terms' => $params['salary_range'],
            );
        }
        if(!empty($params['experience_level'])){
            $tax_query[] = array(
                'taxonomy' => 'experience_level',
                'field' => 'term_id',
                'terms' => $params['experience_level'],
            );
        }
        if(!empty($params['workplace'])){
            $tax_query[] = array(
                'taxonomy' => 'workplace',
                'field' => 'term_id',
                'terms' => $params['workplace'],
            );
        }
        if(!empty($params['shift'])){
            $tax_query[] = array(
                'taxonomy' => 'shift',
                'field' => 'term_id',
                'terms' => $params['shift'],
            );
        }
        if(!empty($params['location'])){
            $tax_query[] = array(
                'taxonomy' => 'location',
                'field' => 'term_id',
                'terms' => $params['location'],
            );
        }
        if(!empty($params['experience'])){
            $tax_query[] = array(
                'taxonomy' => 'experience',
                'field' => 'term_id',
                'terms' => $params['experience'],
            );
        }
        if(!empty($params['job_function'])){
            $tax_query[] = array(
                'taxonomy' => 'job_function',
                'field' => 'term_id',
                'terms' => $params['job_function'],
            );
        }
        if(!empty($tax_query)){
            $tax_query['relation'] = 'AND';
        }
        $args['tax_query'] = $tax_query;
        return $args;
    }

    function ajax_filter_explore_jobs(){
        $params = array();
        $params['paged'] = isset($_POST['paged']) ? $_POST['paged'] : 1;
        $params['job_type'] = isset($_POST['job_type']) ? $_POST['job_type'] : array();
        $params['salary_range'] = isset($_POST['salary_range']) ? $_POST['salary_range'] : array();
        $params['experience_level'] = isset($_POST['experience_level']) ? $_POST['experience_level'] : array();
        $params['workplace'] = isset($_POST['workplace']) ? $_POST['workplace'] : array();
        $params['shift'] = isset($_POST['shift']) ? $_POST['shift'] : array();
        $params['location'] = isset($_POST['location']) ? $_POST['location'] : array();
        $params['experience'] = isset($_POST['experience']) ? $_POST['experience'] : array();
        $params['job_function'] = isset($_POST['job_function']) ? $_POST['locajob_functiontion'] : array();
        $params['keyword'] = isset($_POST['keyword']) ? $_POST['keyword'] : '';
        $query_arg = $this->build_query_job_explore($params);
        $wp_query = new WP_Query( $query_arg );
        $html = '';
        if ( $wp_query->have_posts() ) {
            ob_start();
            ?>    
            <?php
            while ( $wp_query->have_posts() ) {
                $wp_query->the_post();
                include JOB_MANAGER_VIEW_PATH . '/job_card.php';
            }
            ?><?php
            wp_reset_postdata();
            $html = ob_get_clean();
        }
        $response = new stdClass;
        $response->html = $html;
        $response->paged = $params['paged'];
        $response->pages = $wp_query->max_num_pages;
        wp_send_json_success($response);
        wp_die();
    }

    function ajax_save_job_information(){
        if (!is_user_logged_in()) {
            wp_send_json_error('Please login to post job', 401);
            wp_die();
        }
        $user = wp_get_current_user();
        $user_id = $user->ID;
        if ( !in_array( 'company', (array) $user->roles ) ) {
            wp_send_json_error('You are not company', 403);
            wp_die();
        }
        if(!isset($_POST['jobData'])){
            wp_send_json_error('Invalid data', 400);
            wp_die();
        }
        $jobData = $_POST['jobData'];
        $post_args = array(
            'post_type' => 'job_listing',
            'post_title' => sanitize_text_field($jobData['jobTitle']),
            'post_author' => $user_id,
            'post_content' => sanitize_text_field($jobData['jobContent'])
        );
        if(isset($jobData['jobId'])){
            $post_id = $jobData['jobId'];
            $job = get_post($post_id);
        }
        if(!empty($job)){
            $post_args['ID'] = $post_id;    
            wp_update_post($post_args);
        }
        else{
            $post_args['post_status'] = 'pending';
            $post_id = wp_insert_post($post_args);
        }
        foreach($this->text_custom_fields as $key => $value){
            if(empty($jobData[$value])) continue;
            update_post_meta($post_id, $value, sanitize_text_field($jobData[$value]));
        }
        foreach($this->tax_fields as $key => $value){
            if(empty($jobData[$value])) continue;
            $job_taxs = $jobData[$value];
            wp_set_object_terms($post_id, $job_taxs, $value, false);
        }
        wp_send_json_success($post_id);
        wp_die();
    }


    function render_post_job_detail(){
        wp_enqueue_script('post-job-js', plugins_url('/assets/js/post-job.js',JOB_MANAGER_FILE), array('jquery'), '1.0.0', true);  
        $user = wp_get_current_user();
        $user_id = $user->ID;
        ob_start();
        include JOB_MANAGER_VIEW_PATH . '/jobs/post_job_detail.php';
        return ob_get_clean();
    }

    function render_edit_job_detail(){
        wp_enqueue_script('post-job-js', plugins_url('/assets/js/post-job.js',JOB_MANAGER_FILE), array('jquery'), '1.0.0', true);  
        $user = wp_get_current_user();
        $user_id = $user->ID;
        // if ( get_query_var( 'jobId' ) == false || get_query_var( 'jobId' ) == '' ) {
        //     echo '<script>window.location.href = "'.home_url().'";</script>';
        //     exit;
        // }
        $job_id = get_query_var( 'jobId' );
        $script_data_array = array(
            'jobId' => $job_id,
        );
        wp_localize_script('post-job-js', 'jobInfo', $script_data_array);
        ob_start();
        include JOB_MANAGER_VIEW_PATH . '/jobs/edit_job_detail.php';
        return ob_get_clean();
    }
    

    function render_post_job_pannel(){
        wp_enqueue_script('post-job-js', plugins_url('/assets/js/post-job.js',JOB_MANAGER_FILE), array('jquery'), '1.0.0', true);  
        $user = wp_get_current_user();
        $user_id = $user->ID;
        $status = isset($_GET['status']) ? $_GET['status'] : 'publish';
        $allow_status = ['pending', 'publish', 'reject'];
        if(!in_array($status, $allow_status))
            $status = 'publish';
        $job_args = array (
            'post_type' => 'job_listing',
            'author'        =>  $user_id,
            'posts_per_page'    => 10,
            'post_status' => $status
        );
        if($status == 'publish'){
            $job_args['post_status'] = ['publish', 'expired'];
        }
        $jobs = get_posts($job_args);
        ob_start();
        include JOB_MANAGER_VIEW_PATH . '/jobs/post_job_panel.php';
        return ob_get_clean();
    }
}