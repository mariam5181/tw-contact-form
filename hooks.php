<?php
/**
 * Activate the plugin.
 */
function tw_contact_form_activate() { 
    global $wpdb;
 
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
 
    $charset_collate = $wpdb->get_charset_collate();
    $table 			 = $wpdb->prefix . "tw_contact_form";

    maybe_create_table(
    	$wpdb->prefix . $table,
    	"CREATE TABLE {$table} (
			`ID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`first_name` varchar(250) NOT NULL,
			`last_name` varchar(250) NOT NULL,
			`email` varchar(250) NOT NULL,
			`date` date,
			`colors` varchar(250),
			`attachment_id` int(11),
			`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
		) {$charset_collate};"
    );
}
register_activation_hook( TW_MAIN_FILE, 'tw_contact_form_activate' );

/**
 * Create contact form shortcode.
 *
 * @param array $atts Shortcode attributes.
 *
 * @return array
 */
function tw_contact_form_shortcode( $atts = array() ) {
	$args = array();

	$args['title'] = isset( $atts['title'] ) ? trim( $atts['title'] ) : '';

	ob_start();

	tw_get_template_part( 'form', $args );

	return ob_get_clean();
}
add_shortcode( 'tw_contact_form', 'tw_contact_form_shortcode' );

/**
 * Enqueque plugin styles and scripts.
 */
function tw_enqueue_scripts() {
	wp_register_style( 'jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css' );
    wp_enqueue_style( 'jquery-ui' );
    wp_enqueue_style( 'tw-style', plugin_dir_url( TW_MAIN_FILE ) . 'assets/css/style.css', array(), '1.0.0' );

	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_register_script( 'tw-script', plugin_dir_url( TW_MAIN_FILE ) . 'assets/js/script.js', array( 'jquery', 'jquery-ui-datepicker' ), '1.0.0', true );
	wp_localize_script( 'tw-script', 'vars', array( 'ajaxUrl' => admin_url( 'admin-ajax.php' ) ) );
	wp_enqueue_script( 'tw-script' );
}
add_action( 'wp_enqueue_scripts', 'tw_enqueue_scripts' );

/**
 * Submit form.
 */
function tw_submit_form() {
	global $wpdb;

	$error_message = '';
	$attach_id     = 0;

	$first_name = isset( $_POST['first_name'] ) ? $_POST['first_name'] : false;
	$last_name  = isset( $_POST['last_name'] ) ? $_POST['last_name'] : false;
	$email      = isset( $_POST['email'] ) ? $_POST['email'] : false;
	$colors     = isset( $_POST['colors'] ) ? $_POST['colors'] : array();
	$date       = isset( $_POST['date'] ) ? $_POST['date'] : false;
	$file_data  = isset( $_FILES['file_data'] ) ? $_FILES['file_data'] : array();

	if ( ! $first_name ) {
		$error_message .= __( 'First name can\'t be empty. ', 'tw' );
	}

	if ( ! $last_name ) {
		$error_message .= __( 'Last name can\'t be empty. ', 'tw' );
	}

	if ( ! $email ) {
		$error_message .= __( 'Email can\'t be empty. ', 'tw' );
	} elseif ( ! is_email( $email ) ) {
		$error_message .= __( 'Please provide a valid email address. ', 'tw' );
	}

	if ( ! empty( $file_data ) ) {
		$upload = wp_upload_bits( $file_data['name'], null, file_get_contents( $file_data['tmp_name'] ) );

	    $wp_filetype = wp_check_filetype( basename( $upload['file'] ), null );

		if ( ! wp_match_mime_types( 'image', $wp_filetype['type'] ) ) {
			$error_message .= __( 'The uploaded file is not a valid image. Please try again. ', 'tw' );
		}

		$max_uploads_size = wp_max_upload_size();
		if ( ! empty( $file_data['size'] ) && $file_data['size'] > $max_uploads_size ) {
			$error_message .= __( 'Memory exceeded. Please try another smaller file. ', 'tw' );
		}

		$wp_upload_dir = wp_upload_dir();
		if ( ! is_writeable( $wp_upload_dir['path'] ) ) {
			$error_message .= __( 'Unable to create directory, please check permissions. ', 'tw' );
		}
	}

	if ( '' !== $error_message ) {
		wp_send_json_error( $error_message, 400 );
		wp_die();
	}

	if ( ! empty( $file_data ) ) {
	    $attachment = array(
	        'guid' => $wp_upload_dir['baseurl'] . _wp_relative_upload_path( $upload['file'] ),
	        'post_mime_type' => $wp_filetype['type'],
	        'post_title' => preg_replace('/\.[^.]+$/', '', basename( $upload['file'] )),
	        'post_content' => '',
	        'post_status' => 'inherit'
	    );
	    
	    $attach_id = wp_insert_attachment( $attachment, $upload['file'] );

	    require_once(ABSPATH . 'wp-admin/includes/image.php');

	    $attach_data = wp_generate_attachment_metadata( $attach_id, $upload['file'] );

	    wp_update_attachment_metadata( $attach_id, $attach_data );
	}

	$time     = current_time( 'mysql' );
	$table    = $wpdb->prefix . 'tw_contact_form';

	$inserted = $wpdb->insert(
		$table,
		array(
			'first_name'    => $first_name,
			'last_name'     => $last_name,
			'email'         => $email,
			'colors'        => ! empty( $colors ) ? wp_json_encode( $colors ) : '',
			'date'          => $date,
			'attachment_id' => $attach_id
		),
		array( '%s', '%s', '%s', '%s', '%s', '%d' )
	);

	if ( is_wp_error( $inserted ) ) {
		wp_send_json_error( __( 'Error submitting form.', 'tw' ), 400 );
		wp_die();
	}

    wp_send_json_success( __( 'Thank you, your data has been sent!.', 'tw' ), 201 );
    wp_die();
}
add_action('wp_ajax_submit_form','tw_submit_form');
add_action('wp_ajax_nopriv_submit_form', 'tw_submit_form');

/**
 * Add admin page for plugin.
 */
function tw_admin_menu() {
	add_menu_page(
		__( '10web form', 'tw' ),
		__( '10web form', 'tw' ),
		'manage_options',
		'tw-contact-form',
		'tw_admin_page_contents',
		'dashicons-schedule',
		3
	);
}
add_action( 'admin_menu', 'tw_admin_menu' );
