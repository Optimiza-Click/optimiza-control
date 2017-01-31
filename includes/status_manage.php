<?php

	if (!class_exists('WP_Optimiza_Manage')) 
	{
		class WP_Optimiza_Manage
			{
		
			public function retrieve_plugins_data() {
				$key = file_get_contents(dirname(__FILE__) . '/jwt.txt');
				global $wp_control_data;
				 $page_viewed = basename($_SERVER['REQUEST_URI']);
					if(($page_viewed) == 'request-plugins-data') {
						
							$this->data_send();
								$jwt = JWT::encode($wp_control_data, $key);
							print_r($jwt);
							
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
