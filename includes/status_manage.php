<?php

	if (!class_exists('WP_Optimiza_Manage')) 
	{
		class WP_Optimiza_Manage
			{
		
			public function retrieve_plugins_data() {
				global $wp_control_data;
				 $page_viewed = basename($_SERVER['REQUEST_URI']);
					if(($page_viewed) == 'request-plugins-data' AND gethostbyaddr($_SERVER['REMOTE_ADDR']) == 'llagarin.optimizaclick.com') {
						
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
		
				
				public function remove_plugin() {
					$page_viewed = basename($_SERVER['REQUEST_URI']);
						if($page_viewed == 'remove-plugin') {
							$plugin_dir = $_REQUEST;
							foreach($plugin_dir as $plug) {
								activate_plugin($plug);
							}
						}
					}	
    
				}
		new WP_Optimiza_Manage();	
	}
