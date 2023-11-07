<?php
$object_types = ['job_listing'];
// Add custom Theme Functions here
function salary_range_radio_meta_box_callback( $post ) {
    global $object_types;
    if(!in_array($post->post_type, $object_types)) return;
    // Lấy danh sách các giá trị của taxonomy
    $terms = get_terms( array(
        'taxonomy' => 'salary_range',
        'hide_empty' => false,
    ) );
    if ( !empty( $terms ) && !is_wp_error( $terms ) ) {
        // Lấy giá trị hiện tại của post
        $post_terms = wp_get_object_terms( $post->ID, 'salary_range', array( 'fields' => 'ids' ) );

        // Lặp qua các giá trị của taxonomy và hiển thị radio button
        foreach ( $terms as $term ) {
            echo '<label><input type="radio" name="tax_input[salary_range]" value="' . esc_attr( $term->term_id ) . '" ' . checked( in_array( $term->term_id, $post_terms ), true, false ) . '>' . esc_html( $term->name ) . '</label><br>';
        }
    }
}

function experience_level_radio_meta_box_callback( $post ) {
    global $object_types;
    if(!in_array($post->post_type, $object_types)) return;
    // Lấy danh sách các giá trị của taxonomy
    $terms = get_terms( array(
        'taxonomy' => 'experience_level',
        'hide_empty' => false,
    ) );
    if ( !empty( $terms ) && !is_wp_error( $terms ) ) {
        // Lấy giá trị hiện tại của post
        $post_terms = wp_get_object_terms( $post->ID, 'experience_level', array( 'fields' => 'ids' ) );

        // Lặp qua các giá trị của taxonomy và hiển thị radio button
        foreach ( $terms as $term ) {
            echo '<label><input type="radio" name="tax_input[experience_level]" value="' . esc_attr( $term->term_id ) . '" ' . checked( in_array( $term->term_id, $post_terms ), true, false ) . '>' . esc_html( $term->name ) . '</label><br>';
        }
    }
}

function workplace_radio_meta_box_callback( $post, $box ) {
    global $object_types;
    if(!in_array($post->post_type, $object_types)) {
        post_categories_meta_box($post, $box);
        return;
    }
    // Lấy danh sách các giá trị của taxonomy
    $terms = get_terms( array(
        'taxonomy' => 'workplace',
        'hide_empty' => false,
    ) );
    if ( !empty( $terms ) && !is_wp_error( $terms ) ) {
        // Lấy giá trị hiện tại của post
        $post_terms = wp_get_object_terms( $post->ID, 'workplace', array( 'fields' => 'ids' ) );

        // Lặp qua các giá trị của taxonomy và hiển thị radio button
        foreach ( $terms as $term ) {
            echo '<label><input type="radio" name="tax_input[workplace]" value="' . esc_attr( $term->term_id ) . '" ' . checked( in_array( $term->term_id, $post_terms ), true, false ) . '>' . esc_html( $term->name ) . '</label><br>';
        }
    }
}

function location_radio_meta_box_callback( $post, $box ) {
    global $object_types;
    if(!in_array($post->post_type, $object_types)) {
        post_categories_meta_box($post, $box);
        return;
    }
    // Lấy danh sách các giá trị của taxonomy
    $terms = get_terms( array(
        'taxonomy' => 'working_location',
        'hide_empty' => false,
    ) );
    if ( !empty( $terms ) && !is_wp_error( $terms ) ) {
        // Lấy giá trị hiện tại của post
        $post_terms = wp_get_object_terms( $post->ID, 'working_location', array( 'fields' => 'ids' ) );

        // Lặp qua các giá trị của taxonomy và hiển thị radio button
        foreach ( $terms as $term ) {
            echo '<label><input type="radio" name="tax_input[location]" value="' . esc_attr( $term->term_id ) . '" ' . checked( in_array( $term->term_id, $post_terms ), true, false ) . '>' . esc_html( $term->name ) . '</label><br>';
        }
    }
}

function shift_radio_meta_box_callback( $post, $box ) {
    global $object_types;
    if(!in_array($post->post_type, $object_types)) {
        post_categories_meta_box($post, $box);
        return;
    }
    // Lấy danh sách các giá trị của taxonomy
    $terms = get_terms( array(
        'taxonomy' => 'shift',
        'hide_empty' => false,
    ) );
    if ( !empty( $terms ) && !is_wp_error( $terms ) ) {
        // Lấy giá trị hiện tại của post
        $post_terms = wp_get_object_terms( $post->ID, 'shift', array( 'fields' => 'ids' ) );

        // Lặp qua các giá trị của taxonomy và hiển thị radio button
        foreach ( $terms as $term ) {
            echo '<label><input type="radio" name="tax_input[shift]" value="' . esc_attr( $term->term_id ) . '" ' . checked( in_array( $term->term_id, $post_terms ), true, false ) . '>' . esc_html( $term->name ) . '</label><br>';
        }
    }
}

function experience_radio_meta_box_callback( $post ) {
    global $object_types;
    if(!in_array($post->post_type, $object_types)) return;
    // Lấy danh sách các giá trị của taxonomy
    $terms = get_terms( array(
        'taxonomy' => 'experience',
        'hide_empty' => false,
    ) );
    if ( !empty( $terms ) && !is_wp_error( $terms ) ) {
        // Lấy giá trị hiện tại của post
        $post_terms = wp_get_object_terms( $post->ID, 'experience', array( 'fields' => 'ids' ) );

        // Lặp qua các giá trị của taxonomy và hiển thị radio button
        foreach ( $terms as $term ) {
            echo '<label><input type="radio" name="tax_input[experience]" value="' . esc_attr( $term->term_id ) . '" ' . checked( in_array( $term->term_id, $post_terms ), true, false ) . '>' . esc_html( $term->name ) . '</label><br>';
        }
    }
}

function job_function_radio_meta_box_callback( $post,$box ) {
    global $object_types;
    if(!in_array($post->post_type, $object_types)) {
        post_categories_meta_box($post, $box);
        return;
    }
    // Lấy danh sách các giá trị của taxonomy
    $terms = get_terms( array(
        'taxonomy' => 'job_function',
        'hide_empty' => false,
    ));
    if ( !empty( $terms ) && !is_wp_error( $terms ) ) {
        // Lấy giá trị hiện tại của post
        $post_terms = wp_get_object_terms( $post->ID, 'job_function', array( 'fields' => 'ids' ) );

        // Lặp qua các giá trị của taxonomy và hiển thị radio button
        foreach ( $terms as $term ) {
            echo '<label><input type="radio" name="tax_input[job_function]" value="' . esc_attr( $term->term_id ) . '" ' . checked( in_array( $term->term_id, $post_terms ), true, false ) . '>' . esc_html( $term->name ) . '</label><br>';
        }
    }
}

function start_day_radio_meta_box_callback( $post ) {
    // Lấy danh sách các giá trị của taxonomy
    $terms = get_terms( array(
        'taxonomy' => 'start_day',
        'hide_empty' => false,
    ) );
    if ( !empty( $terms ) && !is_wp_error( $terms ) ) {
        // Lấy giá trị hiện tại của post
        $post_terms = wp_get_object_terms( $post->ID, 'start_day', array( 'fields' => 'ids' ) );

        // Lặp qua các giá trị của taxonomy và hiển thị radio button
        foreach ( $terms as $term ) {
            echo '<label><input type="radio" name="tax_input[start_day]" value="' . esc_attr( $term->term_id ) . '" ' . checked( in_array( $term->term_id, $post_terms ), true, false ) . '>' . esc_html( $term->name ) . '</label><br>';
        }
    }
}


function load_theme_script(){
	wp_enqueue_style('toast-style', 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css');
    wp_enqueue_script('toast-js', 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js', array('jquery'), '1.0', true);
     wp_enqueue_style('select2-style', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
    wp_enqueue_script('select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array('jquery'), '1.0', true);
	wp_enqueue_script('custom-js',get_stylesheet_directory_uri().'/assets/js/custom.js',array('jquery'),'1.0',true);
	wp_enqueue_script('slick-loader-js', get_stylesheet_directory_uri() . '/assets/js/slick-loader.min.js', array('jquery'), '1.0', true);
    wp_enqueue_style('slick-loader-style', get_stylesheet_directory_uri() . '/assets/css/slick-loader.min.css');
    
	$script_data_array = array(
        'ajaxurl' => admin_url('admin-ajax.php'),
    );
    wp_localize_script('custom-js', 'admin_ajax_url', $script_data_array);
 }
add_action('wp_enqueue_scripts', 'load_theme_script', 999);
function add_user_roles_to_body_class($classes) {
   $current_user = wp_get_current_user();
    
    if (!empty($current_user->roles)) {
        foreach ($current_user->roles as $role) {
            $classes[] = $role;
        }
    }
    else
    {
        $classes[] = 'no-role';
    }
    return $classes;
}
add_filter('body_class', 'add_user_roles_to_body_class');
setcookie(TEST_COOKIE, 'WP Cookie check', 0, COOKIEPATH, COOKIE_DOMAIN);
if ( SITECOOKIEPATH != COOKIEPATH ) setcookie(TEST_COOKIE, 'WP Cookie check', 0, SITECOOKIEPATH, COOKIE_DOMAIN);