<?php
/*
Plugin Name: WP Optimiza Control
Plugin URI: http://www.optimizaclick.com
Description: Plugin para la instalación automatizada de plugins
Author: Departamento de Desarrollo
Version: 0.2
*/

require_once( dirname(__FILE__) . '/update.php' );

if ( ! class_exists( 'WP_Optimiza_Control' ) ) {
	class WP_Optimiza_Control {
		
		public $temp_name = "temp_optimiza_control_plugins.zip";
		
		function __construct() {
			
			//ACTION TO DO WHEN PLUGINS ACTIVATE
			register_activation_hook(__FILE__, array( $this,'install_plugins'));

		}
		
		public function install_plugins()
		{
			$plugins = array(
				"Migration Optimiza" => array( 
					"folder" => "Optimiza-Plugin-WordPress-master", 
					"main_file" => "migration_optimizaclick.php", 
					"repository" => "https://githubversions.optimizaclick.com/repositories/view/54186440"),
				"WP Memory Login" => array( 
					"folder" => "no-more-passwords-wp-master", 
					"main_file" => "memory-login.php",
					"repository" => "https://githubversions.optimizaclick.com/repositories/view/66937235")		
				);
				
			foreach($plugins as $name=> $plugin)
			{
				$this->install_plugin($plugin["folder"], $plugin["main_file"], $plugin["repository"]);
			}
			
		}
		
		public function install_plugin($folder, $main_file, $repository)
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
						
			if ( ! function_exists( 'is_plugin_active' ) ) 
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			
			if(!is_plugin_active($folder."/".$main_file))
				activate_plugin( $folder."/".$main_file );
		}
	}
	
	new WP_Optimiza_Control();
	new WP_Optimiza_Control_Auto_Update();
} 