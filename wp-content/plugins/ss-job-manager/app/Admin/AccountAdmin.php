<?php
namespace App\Admin;

use stdClass;
use WP_Error;

class AccountAdmin{
    public function __construct()
    {
        add_filter('authenticate', array($this, 'maybe_redirect_at_authenticate'), 101, 3);
        add_shortcode('custom_login',array($this,'render_login_form'));
		add_shortcode('custom_register', array($this, 'render_register_form'));
		add_shortcode('custom-password-lost-form', array($this, 'render_password_lost_form'));
		add_shortcode('custom-password-reset-form', array($this, 'render_password_reset_form'));
        add_action('wp_logout', array($this, 'redirect_after_logout'));
        add_action('after_setup_theme', array($this, 'remove_admin_bar'));
        add_filter( 'login_redirect', array($this, 'custom_login_redirect'), 10, 3 );
        add_action ('wp_loaded', array($this, 'my_custom_redirect'));
        add_filter('retrieve_password_message', array($this, 'replace_retrieve_password_message'), 10, 4);
		add_action('login_form_register', array($this, 'redirect_to_custom_register'));
		add_action('login_form_register', array($this, 'do_register_user'));
		// add_filter( 'wp_send_new_user_notification_to_user', array($this,'disable_send_new_user_notification_to_filter'), 10, 2 );
		add_action('login_form_lostpassword', array($this, 'redirect_to_custom_lostpassword'));
        add_action('login_form_lostpassword', array($this, 'do_password_lost'));
		add_action('login_form_rp', array($this, 'redirect_to_custom_password_reset'));
        add_action('login_form_resetpass', array($this, 'redirect_to_custom_password_reset'));
        add_action('login_form_rp', array($this, 'do_password_reset'));
        add_action('login_form_resetpass', array($this, 'do_password_reset'));
        remove_filter( 'lostpassword_url', 'wc_lostpassword_url', 10 );
        add_action( 'user_register', [$this,'user_register'], 10, 1 );
        add_shortcode('account_header_dropdown',array($this,'render_account_header_dropdown'));
    }

    function render_account_header_dropdown(){
        ob_start();
        include JOB_MANAGER_VIEW_PATH . '/accounts/account-header.php';
        return ob_get_clean();
    }
    public function user_register($user_id){
        $user = get_user_by( 'ID', $user_id);
        $roles = ( array ) $user->roles;
        $args = array(
            'post_author' => $user_id,     
            
        );
        if(in_array('candidate',$roles)){
            $args['post_type'] = 'resume';
            $args['post_title'] = $user->display_name;
            $args['post_status'] = 'not_verified';
        }
        if(in_array('school',$roles)){
            $args['post_title'] = $user->user_email;
            $args['post_type'] = 'school';
            $args['post_status'] = 'publish';
        }
        if(in_array('company',$roles)){
            $args['post_title'] = $user->user_email;
            $args['post_type'] = 'company';
            $args['post_status'] = 'not_verified';
        }
        $post_id = wp_insert_post( $args );
        if(in_array('candidate',$roles)){
            update_post_meta($post_id, '_candidate_email', $user->user_email);
        }
        if(in_array('school',$roles)){
            update_post_meta($post_id, '_school_email', $user->user_email);
        }
        if(in_array('company',$roles)){
            update_post_meta($post_id, '_company_email', $user->user_email);
        }
    }
	// function disable_send_new_user_notification_to_filter($send, $user){
	// 	return false;
	// }

	/**
     * Initiates password reset.
     */
    public function do_password_lost()
    {
        if ('POST' == $_SERVER['REQUEST_METHOD']) {
            $errors = retrieve_password();
            if (is_wp_error($errors)) {
                // Errors found
                $redirect_url = home_url('member-password-lost');
                $redirect_url = add_query_arg('errors', join(',', $errors->get_error_codes()), $redirect_url);
            } else {
                // Email sent
                $redirect_url = home_url('member-login');
                $redirect_url = add_query_arg('checkemail', 'confirm', $redirect_url);
            }

            wp_redirect($redirect_url);
            exit;
        }
    }

	/**
     * Redirects the user to the custom "Forgot your password?" page instead of
     * wp-login.php?action=lostpassword.
     */
    public function redirect_to_custom_lostpassword()
    {
        if ('GET' == $_SERVER['REQUEST_METHOD']) {
            if (is_user_logged_in()) {
                $this->redirect_logged_in_user();
                exit;
            }

            wp_redirect(home_url('member-password-lost'));
            exit;
        }
    }
	/**
	 * Handles the registration of a new user.
	 *
	 * Used through the action hook "login_form_register" activated on wp-login.php
	 * when accessed through the registration action.
	 */
	public function do_register_user()
	{
		if ('POST' == $_SERVER['REQUEST_METHOD']) {
			$redirect_url = home_url('member-register');
            $email      = sanitize_email($_POST['email']);
            $first_name = sanitize_text_field($_POST['first_name']);
			$last_name =  sanitize_text_field($_POST['surname']);
            $pass1 = sanitize_text_field($_POST['pass1']);
            $pass2 = sanitize_text_field($_POST['pass2']);
            $role = sanitize_text_field($_POST['role']);
            $allow_roles = ['school', 'company', 'candidate'];
            if(!in_array($role, $allow_roles)){
                $role = 'Subscriber';
            }
            $result = $this->register_user($email, $first_name, $last_name, $pass1, $pass2, $role);

            if (is_wp_error($result)) {
                // Parse errors into a string and append as parameter to redirect
                $errors       = join(',', $result->get_error_codes());
                $redirect_url = add_query_arg('register-errors', $errors, $redirect_url);
            } else {
                // Success, redirect to login page.
                $redirect_url = home_url('member-login');
                $redirect_url = add_query_arg('registered', $email, $redirect_url);
            }

			wp_redirect($redirect_url);
			exit;
		}
	}
	/**
	 * Validates and then completes the new user signup process if all went well.
	 *
	 * @param string $email         The new user's email address
	 * @param string $first_name    The new user's first name
	 * @param string $last_name     The new user's last name
	 *
	 * @return int|WP_Error         The id of the user that was created, or error if failed.
	 */
	private function register_user($email, $first_name, $last_name, $pass1, $pass2, $role = 'Subscriber')
	{
		$errors = new WP_Error();

		// Email address is used as both username and email. It is also the only
		// parameter we need to validate
		if (!is_email($email)) {
			$errors->add('email', $this->get_error_message('email'));
			return $errors;
		}

        if(empty($first_name)){
            $errors->add('first_name', $this->get_error_message('empty_name'));
			return $errors;
        }

		if(empty($last_name)){
            $errors->add('last_name', $this->get_error_message('empty_name'));
			return $errors;
        }

        if(empty($pass1)){
            $errors->add('password', $this->get_error_message('empty_password'));
			return $errors;
        }

        if(empty($pass2) || $pass2 != $pass1){
            $errors->add('password', $this->get_error_message('password_reset_mismatch'));
			return $errors;
        }

		if (username_exists($email) || email_exists($email)) {
			$errors->add('email_exists', $this->get_error_message('email_exists'));
			return $errors;
		}

		// Generate the password so that the subscriber will have to check email...
		//$password = wp_generate_password(12, false);

		$user_data = array(
			'user_login'    => $email,
			'user_email'    => $email,
			'user_pass'     => $pass1,
			'first_name'    => $first_name,
			'last_name'      => $last_name,
            'role'          => $role
		);

		$user_id = wp_insert_user($user_data);
        if(!is_wp_error($user_id)){
            update_user_meta($user_id, sanitize_key( 'baba_user_locked' ),'yes');
        }
		//wp_new_user_notification($user_id, $password);

		return $user_id;
	}

    /**
	 * Redirects the user to the custom registration page instead
	 * of wp-login.php?action=register.
	 */
	public function redirect_to_custom_register()
	{
		if ('GET' == $_SERVER['REQUEST_METHOD']) {
			if (is_user_logged_in()) {
				$this->redirect_logged_in_user();
			} else {
				wp_redirect(home_url('member-register'));
			}
			exit;
		}
	}

	/**
	 * Redirects the user to the correct page depending on whether he / she
	 * is an admin or not.
	 *
	 * @param string $redirect_to   An optional redirect_to URL for admin users
	 */
	private function redirect_logged_in_user($redirect_to = null)
	{
		// $user = wp_get_current_user();
        // if ($redirect_to) {
        //     wp_safe_redirect($redirect_to);
        // } else {
        //     wp_redirect(home_url());
        // }
		wp_redirect(home_url());
	}

    function add_rewrite_tickets_endpoint(){
		add_rewrite_endpoint('tickets', EP_PAGES );
	}

	/**
	 * Redirect the user after authentication if there were any errors.
	 *
	 * @param Wp_User|Wp_Error  $user       The signed in user, or the errors that have occurred during login.
	 * @param string            $username   The user name used to log in.
	 * @param string            $password   The password used to log in.
	 *
	 * @return Wp_User|Wp_Error The logged in user, or error information if there were errors.
	 */
	function maybe_redirect_at_authenticate($user, $username, $password)
	{
		// Check if the earlier authenticate filter (most likely,
		// the default WordPress authentication) functions have found errors
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			if (is_wp_error($user)) {
				$error_codes = join(',', $user->get_error_codes());

				$login_url = home_url('member-login');
				$login_url = add_query_arg('login', $error_codes, $login_url);

				wp_redirect($login_url);
				exit;
			}
		}
		return $user;
	}

	/**
     * Resets the user's password if the password reset form was submitted.
     */
    public function do_password_reset()
    {


        if ('POST' == $_SERVER['REQUEST_METHOD']) {
            $rp_key   = sanitize_text_field($_REQUEST['rp_key']);
            $rp_login = sanitize_text_field($_REQUEST['rp_login']);

            $user = check_password_reset_key($rp_key, $rp_login);

            if (!$user || is_wp_error($user)) {
                if ($user && $user->get_error_code() === 'expired_key') {
                    wp_redirect(home_url('member-login?login=expiredkey'));
                } else {
                    wp_redirect(home_url('member-login?login=invalidkey'));
                }
                exit;
            }

            if (isset($_POST['pass1'])) {
                if ($_POST['pass1'] != $_POST['pass2']) {
                    // Passwords don't match
                    $redirect_url = home_url('cambio-contrasena');

                    $redirect_url = add_query_arg('key', $rp_key, $redirect_url);
                    $redirect_url = add_query_arg('login', $rp_login, $redirect_url);
                    $redirect_url = add_query_arg('error', 'password_reset_mismatch', $redirect_url);

                    wp_redirect($redirect_url);
                    exit;
                }

                if (empty($_POST['pass1'])) {
                    // Password is empty
                    $redirect_url = home_url('cambio-contrasena');

                    $redirect_url = add_query_arg('key', $rp_key, $redirect_url);
                    $redirect_url = add_query_arg('login', $rp_login, $redirect_url);
                    $redirect_url = add_query_arg('error', 'password_reset_empty', $redirect_url);

                    wp_redirect($redirect_url);
                    exit;
                }

                // Parameter checks OK, reset password
                reset_password($user, sanitize_text_field($_POST['pass1']));
                wp_redirect(home_url('member-login?password=changed'));
            } else {
                echo "Invalid request.";
            }

            exit;
        }
    }
	/**
     * Redirects to the custom password reset page, or the login page
     * if there are errors.
     */
    public function redirect_to_custom_password_reset()
    {
        if ('GET' == $_SERVER['REQUEST_METHOD']) {
            // Verify key / login combo
            $user = check_password_reset_key($_REQUEST['key'], $_REQUEST['login']);
            if (!$user || is_wp_error($user)) {
                if ($user && $user->get_error_code() === 'expired_key') {
                    wp_redirect(home_url('member-login?login=expiredkey'));
                } else {
                    wp_redirect(home_url('member-login?login=invalidkey'));
                }
                exit;
            }

            $redirect_url = home_url('cambio-contrasena');
            $redirect_url = add_query_arg('login', esc_attr($_REQUEST['login']), $redirect_url);
            $redirect_url = add_query_arg('key', esc_attr($_REQUEST['key']), $redirect_url);

            wp_redirect($redirect_url);
            exit;
        }
    }

	/**
	 * A shortcode for rendering the new user registration form.
	 *
	 * @param  array   $attributes  Shortcode attributes.
	 * @param  string  $content     The text content for shortcode. Not used.
	 *
	 * @return string  The shortcode output
	 */
	public function render_register_form($attributes, $content = null)
	{
		// Parse shortcode attributes
		$default_attributes = array('show_title' => false);
		$attributes         = shortcode_atts($default_attributes, $attributes);

		if (is_user_logged_in()) {
			return __('You are already signed in.', 'personalize-login');
        }
        else {
			include(JOB_MANAGER_VIEW_PATH.'/accounts/custom-register.php');    
		}
	}

	/**
     * A shortcode for rendering the form used to initiate the password reset.
     *
     * @param  array   $attributes  Shortcode attributes.
     * @param  string  $content     The text content for shortcode. Not used.
     *
     * @return string  The shortcode output
     */
    public function render_password_lost_form($attributes, $content = null)
    {
        // Parse shortcode attributes
        $default_attributes = array('show_title' => false);
        $attributes = shortcode_atts($default_attributes, $attributes);

        // Retrieve possible errors from request parameters
        $attributes['errors'] = array();
        if (isset($_REQUEST['errors'])) {
            $error_codes = explode(',', $_REQUEST['errors']);

            foreach ($error_codes as $error_code) {
                $attributes['errors'][] = $this->get_error_message($error_code);
            }
        }

        if (is_user_logged_in()) {
            return __('You are already signed in.', 'personalize-login');
        } else {
            include(JOB_MANAGER_VIEW_PATH.'/accounts/custom-password-lost.php');    
        }
    }
/**
     * A shortcode for rendering the form used to reset a user's password.
     *
     * @param  array   $attributes  Shortcode attributes.
     * @param  string  $content     The text content for shortcode. Not used.
     *
     * @return string  The shortcode output
     */
    public function render_password_reset_form($attributes, $content = null)
    {
        // Parse shortcode attributes
        $default_attributes = array('show_title' => false);
        $attributes = shortcode_atts($default_attributes, $attributes);

        if (is_user_logged_in()) {
            return __('You are already signed in.', 'personalize-login');
        } else {
            if (isset($_REQUEST['login']) && isset($_REQUEST['key'])) {
                $attributes['login'] = sanitize_text_field($_REQUEST['login']);
                $attributes['key']   = sanitize_text_field($_REQUEST['key']);

                // Error messages
                $errors = array();
                if (isset($_REQUEST['error'])) {
                    $error_codes = explode(',', $_REQUEST['error']);

                    foreach ($error_codes as $code) {
                        $errors[] = $this->get_error_message($code);
                    }
                }
                $attributes['errors'] = $errors;

                include(JOB_MANAGER_VIEW_PATH.'/accounts/custom-password-reset.php');
            } else {
                return __('Invalid password reset link.', 'personalize-login');
            }
        }
    }

	public function render_login_form($attributes, $content = null)
	{
		// Parse shortcode attributes
		$default_attributes = array('show_title' => false);
		$attributes         = shortcode_atts($default_attributes, $attributes);
		$show_title         = $attributes['show_title'];

		if (is_user_logged_in()) {
			return __('You are already signed in.', 'likdo');
		}

		// Pass the redirect parameter to the WordPress login functionality: by default,
		// don't specify a redirect, but if a valid redirect URL has been passed as
		// request parameter, use it.
		$attributes['redirect'] = '';
		if (isset($_REQUEST['redirect_to'])) {
			$attributes['redirect'] = wp_validate_redirect($_REQUEST['redirect_to'], $attributes['redirect']);
		}

		$errors = array();
		if (isset($_REQUEST['login'])) {
			$error_codes = explode(',', $_REQUEST['login']);

			foreach ($error_codes as $code) {
				$errors[] = $this->get_error_message($code);
			}
		}
		$attributes['errors'] = $errors;

		// Check if user just logged out
		$attributes['logged_out'] = isset($_REQUEST['logged_out']) && $_REQUEST['logged_out'] == true;

		// Check if user just updated password
		$attributes['password_updated'] = isset($_REQUEST['password']) && $_REQUEST['password'] == 'changed';

		// Check if the user just requested a new password
		$attributes['lost_password_sent'] = isset($_REQUEST['checkemail']) && $_REQUEST['checkemail'] == 'confirm';

		// Render the login form using an external template
        include JOB_MANAGER_VIEW_PATH . '/accounts/custom-login.php';
	} // end render_login_form

	/**
	 * Finds and returns a matching error message for the given error code.
	 *
	 * @param string $error_code    The error code to look up.
	 *
	 * @return string               An error message.
	 */
	private function get_error_message($error_code)
	{
		switch ($error_code) {
			case 'empty_username':
				return __('You do have an email address, right?', 'likdo');

			case 'empty_email':
				return __('You do have an email address, right?', 'likdo');

			case 'empty_password':
				return __('You need to enter a password', 'likdo');

			case 'invalid_username':
				return __(
					"We don't have any users with that email address. Maybe you used a different one when signing up?",
					'likdo'
				);

			case 'incorrect_password':
				$err = __(
					"The password you entered wasn't quite right",
					'likdo'
				);
				return sprintf($err, site_url('wp-login.php?action=lostpassword'));
				

				// Reset password
			case 'expiredkey':
			case 'invalidkey':
				return __('The password reset link you used is not valid anymore.', 'likdo');

			case 'password_reset_mismatch':
				return __("The two passwords you entered don't match.", 'likdo');

			case 'password_reset_empty':
				return __("Sorry, we don't accept empty passwords.", 'likdo');
            case 'locked':
                return __("Your account has not been activated yet", 'likdo');

			default:
				break;

				// Lost password
			case 'empty_username':
				return __('You need to enter your email address to continue.', 'likdo');

			case 'invalid_email':
			case 'invalidcombo':
				return __('There are no users registered with this email address.', 'likdo');
		}

		return __('An unknown error occurred. Please try again later.', 'likdo');
	}

    /**
     * Returns the message body for the password reset mail.
     * Called through the retrieve_password_message filter.
     *
     * @param string  $message    Default mail message.
     * @param string  $key        The activation key.
     * @param string  $user_login The username for the user.
     * @param WP_User $user_data  WP_User object.
     *
     * @return string   The mail message to send.
     */
    public function replace_retrieve_password_message($message, $key, $user_login, $user_data)
    {
        // Create new message
        $msg  = __('Hello!', 'likdo') . "\r\n\r\n";
        $msg .= sprintf(__('You asked us to reset your password for your account using the email address %s.', 'likdo'), $user_login) . "\r\n\r\n";
        $msg .= __("If this was a mistake, or you didn't ask for a password reset, just ignore this email and nothing will happen.", 'likdo') . "\r\n\r\n";
        $msg .= __('To reset your password, visit the following address:', 'likdo') . "\r\n\r\n";
        $msg .= site_url("member-password-reset?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . "\r\n\r\n";
        $msg .= __('Thanks!', 'likdo') . "\r\n";

        return $msg;
    }

	/**
	 * Redirect to custom login page after the user has been logged out.
	 */
	public function redirect_after_logout()
	{
		$redirect_url = home_url('member-login?logged_out=true');
		wp_safe_redirect($redirect_url);
		exit;
	}

	function remove_admin_bar() {
		if (!current_user_can('administrator') && !current_user_can('editor')) {
		  show_admin_bar(false);
		}
	}

	function my_custom_redirect() {
        $request_uri = $_SERVER['REQUEST_URI'];
        if (str_contains($request_uri,'/member-login')) {
            if(is_user_logged_in()){
                //wp_redirect(home_url());
               // exit;
            }    
        }
        // if(str_contains($request_uri,'/my-tickets')){
        //     if(!is_user_logged_in()){
        //         wp_redirect(home_url('/member-login'));
        //         exit;
        //     }   
        // }
    } 

	function custom_login_redirect($redirect_to, $request, $user){
		$redirect_to = home_url();
		return $redirect_to;
	}
}