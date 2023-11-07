<?php
namespace App\Service;
use App\Helpers\CommonHelper;
use stdClass;

class CompanyService{

    private $text_custom_fields;
    private $tax_fields;
    public function __construct()
    {
        $this->text_custom_fields = ['_company_address','_company_liscense', '_company_industry', '_company_website', 
    '_company_email', '_company_phone', '_company_title', '_company_street_building', '_company_building_number', '_company_additional_information'];
        $this->tax_fields = [];
        add_action( 'wp_ajax_save_company_information', array($this,'ajax_save_company_information'));
        add_action( 'wp_ajax_nopriv_save_company_information', array($this,'ajax_save_company_information'));
        add_action( 'wp_ajax_filter_candidate', array($this,'ajax_filter_candidate'));
        add_action( 'wp_ajax_nopriv_filter_candidate', array($this,'ajax_filter_candidate'));
        add_action( 'wp_ajax_save_application_status', array($this,'ajax_save_application_status'));
        add_action( 'wp_ajax_nopriv_save_application_status', array($this,'ajax_save_application_status'));
        add_action( 'wp_ajax_filter_interview', array($this,'ajax_filter_interview'));
        add_action( 'wp_ajax_nopriv_filter_interview', array($this,'ajax_filter_interview'));
        add_action( 'wp_ajax_update_interview', array($this,'ajax_update_interview'));
        add_action( 'wp_ajax_nopriv_update_interview', array($this,'ajax_update_interview'));
        add_shortcode('company_information',array($this,'render_company_information'));
        add_shortcode('company_dashboard',array($this,'render_company_dashboard'));
        add_shortcode('company_invoice',array($this,'render_company_payment_invoices'));
        add_action( 'wp_ajax_get_invoice_detail', array($this,'ajax_get_invoice_detail'));
        add_action( 'wp_ajax_nopriv_get_invoice_detail', array($this,'ajax_get_invoice_detail'));
    }

    function ajax_save_company_information(){
        if (!is_user_logged_in()) {
            wp_send_json_error('Please login to save information', 401);
            wp_die();
        }
        $user = wp_get_current_user();
        $user_id = $user->ID;
        if ( !in_array( 'company', (array) $user->roles ) ) {
            wp_send_json_error('You are not compny',403);
            wp_die();
        }
        if(!isset($_POST['companyData'])){
            wp_send_json_error('Invalid data',400);
            wp_die();
        }
        $companyData = $_POST['companyData'];
        $post_args = array(
            'post_type' => 'company',
            'post_title' => sanitize_text_field($companyData['companyName']),
            'post_author' => $user_id
        );
        $company_args = array (
            'post_type' => 'company',
            'author'        =>  $user_id,
            'posts_per_page'    => 1,
            'post_status' => array('verified', 'not_verified')
        );

        $companies = get_posts($company_args);
        if(count($companies) > 0){
            $post_id = $companies[0]->ID;
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
            if(empty($companyData[$value])) continue;
            update_post_meta($post_id, $value, sanitize_text_field($companyData[$value]));
        }
        foreach($this->tax_fields as $key => $value){
            if(empty($companyData[$value])) continue;
            $company_taxs = $companyData[$value];
            wp_set_object_terms($post_id, $company_taxs, $value, false);
        }
        wp_send_json_success();
        wp_die();
    }

    function render_company_information(){
        wp_enqueue_script('company-js', plugins_url('/assets/js/company-information.js',JOB_MANAGER_FILE), array('jquery'), '1.0.0', true);  
        $user = wp_get_current_user();
        $user_id = $user->ID;
        $company_args = array (
            'post_type' => 'company',
            'author' =>  $user_id,
            'posts_per_page'    => 1,
            'post_status' => array('verified', 'not_verified')
        );

        $companies = get_posts($company_args);
        $company_id = count($companies) > 0 ? $companies[0]->ID : -1;
        ob_start();
        include JOB_MANAGER_VIEW_PATH . '/companies/company_information.php';
        return ob_get_clean();
    }
    function ajax_save_application_status(){
        if (!is_user_logged_in()) {
            wp_send_json_error('Please login to update candidate status', 401);
            wp_die();
        }
        $user = wp_get_current_user();
        $user_id = $user->ID;
        if ( !in_array( 'company', (array) $user->roles ) ) {
            wp_send_json_error('You are not compny',403);
            wp_die();
        }
        if(!isset($_POST['applicationId']) || !isset($_POST['status'])){
            wp_send_json_error('Invalid data',400);
            wp_die();
        }
        $application = get_post($_POST['applicationId']);
        if(empty($application)){
            wp_send_json_error('Invalid data',400);
            wp_die();
        }
        $update_arg = array(
            'ID'           => $application->ID,
            'post_status'   => $_POST['status']
        );
      
      // Update the post into the database
        wp_update_post( $update_arg );
        wp_send_json_success();
        wp_die();
    }

    function ajax_filter_candidate(){
        if (!is_user_logged_in()) {
            wp_send_json_error('Please login to filter candidate', 401);
            wp_die();
        }
        $user = wp_get_current_user();
        $user_id = $user->ID;
        if ( !in_array( 'company', (array) $user->roles ) ) {
            wp_send_json_error('You are not compny',403);
            wp_die();
        }
        global $wpdb;
        $sql = '';
        $select = "SELECT * from {$wpdb->prefix}posts";
        if(!isset($_POST['jobId'])){
            wp_send_json_error('Invalid data',400);
            wp_die();
        }
        $jobId = $_POST['jobId'];
        $sql .= " WHERE post_parent = $jobId and post_type = 'job_application'";
        if(isset($_POST['status'])){
            $status = $_POST['status'];
            $sql .= " AND post_status = '$status'";
        }
        $all_job_candidates = $wpdb->get_results($select.$sql);
        $response = new stdClass;
        ob_start();
        foreach($all_job_candidates as $candidate){
            $resume_id = get_post_meta($candidate->ID, '_resume_id', true);
            include JOB_MANAGER_VIEW_PATH . '/companies/candidate_card.php';
        }
        $response->html = ob_get_clean();
        wp_send_json_success($response);
        wp_die();
    }

    function ajax_filter_interview(){
        if (!is_user_logged_in()) {
            wp_send_json_error('Please login to filter candidate', 401);
            wp_die();
        }
        $user = wp_get_current_user();
        $user_id = $user->ID;
        if ( !in_array( 'company', (array) $user->roles ) ) {
            wp_send_json_error('You are not compny',403);
            wp_die();
        }
        global $wpdb;
        $sql = '';
        $select = "SELECT * from {$wpdb->prefix}posts";
        if(!isset($_POST['jobId'])){
            wp_send_json_error('Invalid data',400);
            wp_die();
        }
        $jobId = $_POST['jobId'];
        $sql .= " WHERE post_parent = $jobId and post_type = 'job_application' and post_status = 'interviewed'";
        if(isset($_POST['keyword'])){
            $keyword = $_POST['keyword'];
            $sql .= " AND post_title LIKE '%$keyword%'"; 
        }
        $interview_job_candidates = $wpdb->get_results($select.$sql);
        $response = new stdClass;
        ob_start();
        foreach($interview_job_candidates as $candidate){
            $resume_id = get_post_meta($candidate->ID, '_resume_id', true);
            ?>
            <tr class="interview-card" data-candidate="<?php echo $candidate->ID?>">
                <td>
                    <img decoding="async" src="<?php echo !empty(get_user_meta($candidate->post_author, '_avatar', true)) ? site_url().get_user_meta($user_id, '_avatar', true) : '/wp-content/uploads/empty_avatar.jpg';?>" width="40" height="40">
                </td>
                <td><?php echo get_post( $resume_id )->post_title; ?></td>
                <td class="candidate-interview-time"><?php echo get_post_meta($candidate->ID, 'interview_time', true)?></td>
                <td class="candidate-interview-date"><?php echo get_post_meta($candidate->ID, 'interview_date', true)?></td>
                <td>Interviewed</td>
                <!-- <td>Note from HR</td> -->
                <td>
                    <a class="interview-btn button primary" style="border-radius:10px">
                        <span>Interview</span>
                    </a>
                </td>
            </tr>
            <?php
        }
        $response->html = ob_get_clean();
        wp_send_json_success($response);
        wp_die();
    }

    function ajax_get_invoice_detail(){
        if (!is_user_logged_in()) {
            wp_send_json_error('Please login to filter candidate', 401);
            wp_die();
        }
        $user = wp_get_current_user();
        $user_id = $user->ID;
        if ( !in_array( 'company', (array) $user->roles ) ) {
            wp_send_json_error('You are not compny',403);
            wp_die();
        }
        if(!isset($_POST['jobId'])){
            wp_send_json_error('Invalid data',400);
            wp_die();
        }
        $jobId = $_POST['jobId'];
        $bill_to = get_field('bill_to', $jobId);
        $description = get_field('description', $jobId);
        $payment_due = get_field('payment_due', $jobId);
        ob_start();
        ?>
        <div class="row row-small view-popup-invoice" id="row-1374072916">
            <div id="col-682578279" class="col medium-4 small-12 large-4">
                <div class="col-inner">
                <p>
                    <strong>Bill to: <br>
                    </strong><?php echo $bill_to?>
                </p>
                </div>
            </div>
            <div id="col-1873868486" class="col medium-4 small-12 large-4">
                <div class="col-inner">
                <p>
                    <strong>Date:</strong> <?php echo $payment_due?> <br>
                    <strong>Description:</strong> <?php echo $description?>
                </p>
                </div>
            </div>
            <div id="col-139629033" class="col small-12 large-12">
                <div class="col-inner">
                <table class="payment-invoice-table">
                    <thead>
                    <tr>
                        <th>No.</th>
                        <th>Position</th>
                        <th>Full Name</th>
                        <th>Rate offer ($/hr)</th>
                        <th>Fee charge</th>
                        <th>Note</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php
                        if( have_rows('invoice_detail', $jobId) ):
                            $index = 1;
                            while( have_rows('invoice_detail', $jobId) ) : the_row();
                                $full_name = get_sub_field('full_name');
                                $rate_offer = get_sub_field('rate_offer');
                                $fee_charge = get_sub_field('fee_charge');
                                $note = get_sub_field('note');
                                ?>
                                <tr>
                                    <td><?php echo $index?></td>
                                    <td><?php echo get_the_title($jobId)?></td>
                                    <td><?php echo $full_name?></td>
                                    <td><?php echo $rate_offer?></td>
                                    <td><?php echo $fee_charge?></td>
                                    <td><?php echo $note?></td>
                                </tr>
                                <?php
                                $index++;
                            endwhile; 
                        endif;
                        ?>
                    </tbody>
                </table>
                </div>
            </div>
            </div>
        <?php
        $html = ob_get_clean();
        wp_send_json_success($html);
        wp_die();
    }
    function render_company_payment_invoices()
    {
        global $wpdb;
        wp_enqueue_script('company-js', plugins_url('/assets/js/company-invoice.js',JOB_MANAGER_FILE), array('jquery'), '1.0.0', true);  
        $user = wp_get_current_user();
        $user_id = $user->ID;
        $company_args = array (
            'post_type' => 'company',
            'author' =>  $user_id,
            'posts_per_page'    => 1,
            'post_status' => array('verified', 'not_verified')
        );

        $companies = get_posts($company_args);
        $company_id = count($companies) > 0 ? $companies[0]->ID : -1;
        $invoice_jobs = $wpdb->get_results("SELECT * from {$wpdb->prefix}posts p inner join {$wpdb->prefix}postmeta pm on p.ID = pm.post_id Where p.post_author = $user_id and p.post_type = 'job_listing' and pm.meta_key = 'invoice_total' and pm.meta_value > 0");

        ob_start();
        include JOB_MANAGER_VIEW_PATH . '/companies/payment_invoice.php';
    
        return ob_get_clean();
    }

    function render_company_dashboard()
    {
        global $wpdb;
        wp_enqueue_script('company-js', plugins_url('/assets/js/company-dashboard.js',JOB_MANAGER_FILE), array('jquery'), '1.0.0', true);  
        $user = wp_get_current_user();
        $user_id = $user->ID;
        $company_args = array (
            'post_type' => 'company',
            'author' =>  $user_id,
            'posts_per_page'    => 1,
            'post_status' => array('verified', 'not_verified')
        );

        $companies = get_posts($company_args);
        $company_id = count($companies) > 0 ? $companies[0]->ID : -1;
        // $active_job_count = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}posts Where post_author = $user_id and post_status = 'publish' and post_type = 'job_listing'");
        // $pending_job_count = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}posts Where post_author = $user_id and post_status = 'pending' and post_type = 'job_listing'");
        // $expired_job_count = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}posts Where post_author = $user_id and post_status = 'expired' and post_type = 'job_listing'");
        // $reject_job_count = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}posts Where post_author = $user_id and post_status = 'reject' and post_type = 'job_listing'");
        $active_job_count = 0;
        $pending_job_count = 0;
        $expired_job_count = 0;
        $reject_job_count = 0;
        $all_jobs = $wpdb->get_results("SELECT * from {$wpdb->prefix}posts Where post_author = $user_id and post_type = 'job_listing'");
        $job_ids = [];
        foreach($all_jobs as $job){
            array_push($job_ids, $job->ID);
            switch($job->post_status){
                case 'publish':
                    $active_job_count ++;
                    break;
                case 'pending':
                    $pending_job_count ++;
                    break;
                case 'expired':
                    $expired_job_count ++;
                    break;
                case 'reject':
                    $reject_job_count++;
                    break;
                default:
                    break;
            }
        }
        if(count($job_ids)> 0){
            $job_id_list = CommonHelper::get_sanitized_id_list($job_ids);
            if($_GET['job']){
                $jobId = $_GET['job'];
            }
            else{
                $jobId = $job_ids[0];
            }
            $all_job_candidates = $wpdb->get_results("SELECT * from {$wpdb->prefix}posts Where post_parent = $jobId and post_type = 'job_application'");
            $interview_job_candidates = $wpdb->get_results("SELECT * from {$wpdb->prefix}posts Where post_parent = $jobId and post_type = 'job_application' and post_status = 'interviewed'");
        }
        
        $new_candidate_count = count($job_ids) == 0 ? 0 : (int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}posts p where p.post_status = 'new' and p.post_type = 'job_application' and p.post_parent in ({$job_id_list})");
        $interview_candidate_count = count($job_ids) == 0 ? 0 : (int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}posts p where p.post_status = 'interviewed' and p.post_type = 'job_application' and p.post_parent in ({$job_id_list})");
        $hired_candidate_count = count($job_ids) == 0 ? 0 : (int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}posts p where p.post_status = 'hired' and p.post_type = 'job_application' and p.post_parent in ({$job_id_list})");
        $reject_candidate_count = count($job_ids) == 0 ? 0 : (int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}posts p where p.post_status = 'rejected' and p.post_type = 'job_application' and p.post_parent in ({$job_id_list})");
        $reviewing_candidate_count = count($job_ids) == 0 ? 0 : (int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}posts p where p.post_status = 'reviewing' and p.post_type = 'job_application' and p.post_parent in ({$job_id_list})");

        ob_start();
        include JOB_MANAGER_VIEW_PATH . '/companies/dashboard.php';
    
        return ob_get_clean();
    }

    function ajax_update_interview(){
        if (!is_user_logged_in()) {
            wp_send_json_error('Please login to filter candidate', 401);
            wp_die();
        }
        $user = wp_get_current_user();
        $user_id = $user->ID;
        if ( !in_array( 'company', (array) $user->roles ) ) {
            wp_send_json_error('You are not compny',403);
            wp_die();
        }
        if(!isset($_POST['candidateId']) || !isset($_POST['interviewDate']) || !isset($_POST['interviewTime'])){
            wp_send_json_error('Invalid data',400);
            wp_die();
        }
        update_post_meta($_POST['candidateId'], 'interview_date', $_POST['interviewDate']);
        update_post_meta($_POST['candidateId'], 'interview_time', $_POST['interviewTime']);
        wp_send_json_success();
        wp_die();
    }
}