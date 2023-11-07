<?php
namespace App\Service;

class FileManageService{


    public function __construct()
    {
        add_action( 'wp_ajax_upload_file', array($this,'ajax_upload_file'));
        add_action( 'wp_ajax_nopriv_upload_file', array($this,'ajax_upload_file'));
        add_action( 'wp_ajax_avatar_upload', array($this,'ajax_avatar_upload'));
        add_action( 'wp_ajax_nopriv_avatar_upload', array($this,'ajax_avatar_upload'));
    }

    function ajax_upload_file(){
        $file = $_FILES['file'];
        // removing white space
        $fileName = preg_replace('/\s+/', '-', $_FILES["file"]["name"]);

        // removing special character but keep . character because . seprate to extantion of file
        $fileName = preg_replace('/[^A-Za-z0-9.\-]/', '', $fileName);

        // rename file using time
        $fileName = time().'-'.$fileName;
        $upload_dir = wp_upload_dir();

        $uploaded_file = move_uploaded_file($file['tmp_name'], $upload_dir['path'] . '/' . $fileName);

        if ($uploaded_file) {
            $base_url = $upload_dir['url'];
            $upload_path = str_replace(site_url(), '', $base_url);
            $file_url = $upload_path . '/' . $fileName;
            wp_send_json_success($file_url);
        } else {
            wp_send_json_error();
        }
        wp_die();
    }

    function ajax_avatar_upload(){
        if (!is_user_logged_in()) {
            wp_send_json_error('Unauthorized', 401);
            wp_die();
        }
        $file = $_FILES['file'];
        // removing white space
        $fileName = preg_replace('/\s+/', '-', $_FILES["file"]["name"]);

        // removing special character but keep . character because . seprate to extantion of file
        $fileName = preg_replace('/[^A-Za-z0-9.\-]/', '', $fileName);

        // rename file using time
        $fileName = time().'-'.$fileName;
        $upload_dir = wp_upload_dir();

        $uploaded_file = move_uploaded_file($file['tmp_name'], $upload_dir['path'] . '/' . $fileName);

        if ($uploaded_file) {
            $base_url = $upload_dir['url'];
            $upload_path = str_replace(site_url(), '', $base_url);
            $file_url = $upload_path . '/' . $fileName;
            $user_id = wp_get_current_user()->ID;
            update_user_meta($user_id, '_avatar', $file_url);
            wp_send_json_success($file_url);
        } else {
            wp_send_json_error();
        }
        wp_die();
    }
    
}