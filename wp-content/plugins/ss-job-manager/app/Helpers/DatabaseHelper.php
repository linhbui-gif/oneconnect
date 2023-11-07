<?php
namespace App\Helpers;

class DatabaseHelper{
    public static function create_company_admin_manager(){
        global $wpdb;
        $table = $wpdb->prefix.'company_admin_manager';
        $user_table =  $wpdb->prefix.'users';
        $post_table = $wpdb->prefix.'posts';
        $sql = "CREATE TABLE IF NOT EXISTS $table (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `company_id` bigint(20) UNSIGNED NOT NULL,
            `admin_id` bigint(20) UNSIGNED NOT NULL,
            PRIMARY KEY(`id`),
            FOREIGN KEY (`admin_id`) REFERENCES $user_table(ID) ON DELETE CASCADE,
            FOREIGN KEY (`company_id`) REFERENCES $post_table(ID) ON DELETE CASCADE
          ) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ENGINE=InnoDB;"; 
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        maybe_create_table($table, $sql);
    }
}