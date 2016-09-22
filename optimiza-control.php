<?php
/*
Plugin Name: WP Optimiza Control
Plugin URI: http://www.optimizaclick.com
Description: Plugin para la instalación automatizada de plugins
Author: Departamento de Desarrollo
Version: 0.3.1
*/

require_once( dirname(__FILE__) . '/update.php' );

if ( ! class_exists( 'WP_Optimiza_Control' ) ) {
	class WP_Optimiza_Control {
		
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
					"folder" => "amp", 
					"main_file" => "amp.php",
					"repository" => "https://githubversions.optimizaclick.com/repositories/view/68916383")				
				);
		
		function __construct() {
			
			//ACTION TO DO WHEN PLUGINS ACTIVATE
			register_activation_hook(__FILE__, array( $this,'install_plugins'));
			
			add_action( 'init', array( $this, 'activate_plugin' ));
			
			//ACTION TO DO AFTER PLUGIN ACTIVATION
			add_action( 'activated_plugin', array( $this, 'activation_plugin_redirect') );

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
	new WP_Optimiza_Control_Auto_Update();
} 