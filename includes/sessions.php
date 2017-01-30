<?php

if ( ! class_exists( 'Remove_Sessions' ) ) {
	class Remove_Sessions {
        function __construct() {
            register_activation_hook(__FILE__, array( $this,'wp_cron'));
            add_action('data_cron', array( $this,'remove'));
        }

// function to start cron job to delete wc_sessions, only if woocommerce exists :)        
        public function wp_cron() {
			if ( class_exists( 'WooCommerce' ) ) {
				if (! wp_next_scheduled ( 'data_cron' )) {
					wp_schedule_event(time(), 'daily', 'data_cron');
				 }
			}
        }
                 
        public function remove() {
            global $wpdb;
                        $wpdb->query( $wpdb->prepare("DELETE FROM wp_options WHERE option_name LIKE '_wc_session_%' OR option_name LIKE '_wc_session_expires_%" ));
                    }         
         }
    new Remove_Sessions();
}
?>
