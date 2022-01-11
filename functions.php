<?php
/**
 * Includ template part.
 *
 * @param string $template_part_name Template part name.
 *
 * @param array  $args Variables for template part.
 *
 * @return string 
 */
function tw_get_template_part( $template_part_name, $args = array() ) {
	if ( ! empty( $args ) && is_array( $args ) ) {
		extract( $args );
	}

	$template_part = plugin_dir_path( __FILE__ ) . 'template-parts/' .  $template_part_name . '.php';

	if ( ! file_exists( $template_part ) ) {
		_doing_it_wrong( __FUNCTION__, sprintf( __( '%s doesn\'t exist.', 'tw' ), '<code>' . $template_part . '</code>' ), '1.0' );

		return;
	}

	include $template_part;
}

/**
 * Add plugin admin page content.
 */
function tw_admin_page_contents() { ?>
	<div class="wrap">
		<h2>
			<?php esc_html_e( 'Contact form submittions.', 'tw' ); ?>
		</h2>
		<form method="get">
			<input type="hidden" name="page" value="tw-contact-form">
			<?php
			global $wpdb;

			$table = $wpdb->prefix . "tw_contact_form";
			$query = "SELECT * FROM {$table}";

			if ( isset( $_GET['s'] ) ) {
				$query .= " WHERE `email` LIKE '%" . $_GET['s'] . "%'";				
			}

			if ( isset( $_GET['orderby'] ) ) {
				$query .= " ORDER BY " . $_GET['orderby'] . " " . $_GET['order'];				
			}

			$submissions = $wpdb->get_results( $query, ARRAY_A );

			$table = new TW_List_Table();
			$table->items = $submissions;

			$table->search_box( 'Search by email', 'email' );
			$table->prepare_items();
			$table->display();
			?>
		</form>
	</div>
<?php }
