<?php
/*
* Plugin Name: Job Manager functionality
* Version: 1.0.0
* Description: Job Manager functionality Developed By ServiSense
* Author: ServiSense
* Author URI: https://servisense.vn/
* Plugin URI: https://servisense.vn/
* Text Domain: ss-job-manager
* Domain Path: /languages

*/
require_once __DIR__ . '/vendor/autoload.php';
define('JOB_MANAGER_FILE',__FILE__);
define('JOB_MANAGER_PATH', realpath(plugin_dir_path(JOB_MANAGER_FILE)).'/');
define('JOB_MANAGER_APP_PATH', realpath(JOB_MANAGER_PATH.'app/').'/');
define('JOB_MANAGER_VIEW_PATH', realpath(JOB_MANAGER_PATH . 'views/' ) . '/' );
require_once __DIR__ .'/main.php';
// add_action('plugins_loaded', array('Noodle_House_Main_Class', 'init'));
