<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

if ( ! class_exists( 'TW_List_Table' ) ) :
    class TW_List_Table extends WP_List_Table {

        /**
         * Get a list of columns.
         *
         * @return array
         */
        public function get_columns() {
            return array(
            	'attachment_id' => wp_strip_all_tags( __( 'Picture' ) ),
                'first_name'    => wp_strip_all_tags( __( 'First name' ) ),
                'last_name'     => wp_strip_all_tags( __( 'Last name' ) ),
                'email'         => wp_strip_all_tags( __( 'Email' ) ),
                'colors'        => wp_strip_all_tags( __( 'Colors' ) ),
                'date'          => wp_strip_all_tags( __( 'Date' ) ),
            );
        }

        /**
         * Prepares the list of items for displaying.
         */
        public function prepare_items() {
            $columns  = $this->get_columns();
            $hidden   = array();
            $sortable = $this->get_sortable_columns();
            $primary  = 'email';

            $this->_column_headers = array( $columns, $hidden, $sortable, $primary );
        }

		/**
         * Prepares the list of items for displaying.
         *
         * @return array
         */
        public function get_sortable_columns() {
		    $sortable_columns = array(
		        'first_name' => array('first_name', true),
		        'last_name'  => array('last_name', true),
		        'date'       => array('date', true)
		    );

		    return $sortable_columns;
		}

        /**
         * Generates content for a single row of the table.
         * 
         * @param object $item The current item.
         * @param string $column_name The current column name.
         */
        protected function column_default( $item, $column_name ) {
            switch ( $column_name ) {
                case 'first_name':
                    return esc_html( $item['first_name'] );
                case 'last_name':
                    return esc_html( $item['last_name'] );
                case 'email':
                    return esc_html( $item['email'] );
                case 'colors':
                    return esc_html( json_decode( $item['colors'] ) );
                case 'date':
                    return esc_html( $item['date'] );
                case 'attachment_id':
                	$attachment = wp_get_attachment_image_src( $item['attachment_id'], 'thumbnail' );
                	
                	if ( $attachment ) {
                		return '<img src="' . esc_html( $attachment[0] ) . '" width="50" height="50" />';
                	}

                	return '';
                return 'Unknown';
            }
        }
    }
endif;