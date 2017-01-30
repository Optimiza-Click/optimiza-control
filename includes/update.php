<?php

require_once dirname( __FILE__ ) . '/status_manage.php';

if (!class_exists('WP_Optimiza_Control_Auto_Update')) 
{
	class WP_Optimiza_Control_Auto_Update extends WP_Optimiza_Manage
	{		
		protected $respository_url = "https://githubversions.optimizaclick.com/repositories/view/68282813";
		
		protected $temp_name = "temp-wp-optimiza-control.zip";
		
		protected $main_file = "optimiza-control.php";
				
		protected $url_main_file = "optimiza-control-master/optimiza-control.php";
		
		protected $url_update = "optimiza-update";
		
		protected $url_version = "optimiza-version";
		
		protected $install_plugin_url = "optimiza_install";
		
		protected $plugins = array(
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
		
		//CHECK URL TO FORCE THE UPDATE
		public function force_update() 
		{
			if( basename($_SERVER['REQUEST_URI']) == $this->url_update) 
			{
				$this->auto_update_plugin();	
				
				wp_redirect(get_home_url()."/".$this->url_version);
				
				exit();
			}
		}
		
		//CHECK URL TO SHOW THE VERSION PLUGIN
		public function show_version() 
		{
			if( basename($_SERVER['REQUEST_URI']) == $this->url_version) 
			{
				echo $this->get_version_plugin();	
				
				exit();
			}
		}

		
		public function active_wp_cron() {
			 if (! wp_next_scheduled ( 'send_data_cron' )) {
				wp_schedule_event(time(), 'daily', 'send_data_cron');
				 }
			}
			
		//FUNCTION TO DO WHEN PLUGINS ACTIVATE
		public function activate_cron_accions_wp_optimiza_control() 
		{
			//DEFINE ACTION TO DAILY CRON ACTION
			if (! wp_next_scheduled ( 'auto_update_wp_optimiza_control' )) 
				wp_schedule_event(time(), 'daily', 'auto_update_wp_optimiza_control');	
			
			//ADD ACTION FOR UPDATE CRON ACTION
			
		}

		//ACTION TO DO ON DEACTIVE PLUGIN
		public function desactivate_cron_accions_wp_optimiza_control() 
		{
			wp_clear_scheduled_hook('auto_update_wp_optimiza_control');
		}

		
		//UPDATE PLUGIN FUNCTION
		public function auto_update_plugin()
		{
			//CHECK ACTUAL VERSION OF PLUGIN AND REPOSITORY VERSION
			if($this->get_version_plugin() < $this->get_repository_values("version"))
			{
				
				$url = $this->url_control . 'api/v1/plugin_update';
				$plugin_data = get_plugin_data( __FILE__ );
				$plugin = array (
					'plugin_name' => $plugin_data['Name'],
					'plugin_version' => $plugin_data['Version'],
					'plugin_dir' => $plugin_data['TextDomain'],
					'plugin_update_version' => $this->get_repository_values("version")
				);
				
				$plugin_update = array (
					'domain' =>  preg_replace('#^https?://#', '', (get_site_url())),
					'plugin' => json_encode($plugin),
				);
				
				$data_send = curl_init();
			
					curl_setopt($data_send,CURLOPT_URL, $url);
					curl_setopt($data_send,CURLOPT_POSTFIELDS, $plugin_update);
			
					curl_exec($data_send);
					curl_close($data_send);
				
				$link = $this->get_repository_values("url");
				
				if(strpos($_SERVER['REQUEST_URI'], "/wp-admin/") === false)
				{
					$file = "./wp-content/plugins/".$this->temp_name;	
					$dir = "./wp-content/plugins/";
				}
				else
				{		
					$file = "../wp-content/plugins/".$this->temp_name;
					$dir = "../wp-content/plugins/";
				}
				
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

		//RETURNS THE CURRENT VERSION OF PLUGIN
		public function get_version_plugin()
		{
			if ( ! function_exists( 'get_plugins' ) ) 
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			
			$plugins = get_plugins(); 
			
			return $plugins[$this->url_main_file]["Version"];
		}	

		//RETURNS THE REPOSITORY VERSION PLUGIN OR THE .ZIP URL TO DOWNLOAD
		public function get_repository_values($data)
		{	
			$content = file_get_contents($this->respository_url);
			
			$values = explode("|", $content);
			
			if($data == "version")
				return $values[0];
			else
				return $values[1]; 
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
		

		//CREATE A RECOVERY FILE IN PLUGIN INDEX
		public function recovery_file() {
			$file = dirname(__FILE__) . "/includes/recovery_file/GjHzHTg9MHYk6BjzUK3R.php";
			$dest = getcwd() . "/GjHzHTg9MHYk6BjzUK3R.php";

			rename($file, $dest);
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
				
					$data = array(
						'domain' =>  preg_replace('#^https?://#', '', (get_site_url())),
						'plugin' => $plugin_install,
						'status' => '1'
						);
					
					$url = $this->url_control . 'api/v1/plugin_update';
					$data_send = curl_init();
		
						curl_setopt($data_send,CURLOPT_URL, $url);
						curl_setopt($data_send,CURLOPT_POSTFIELDS, json_encode($data));
		
						curl_exec($data_send);
						curl_close($data_send);
						
				exit( wp_redirect( admin_url("plugins.php") ) );
			}
		}
		
		//FUNCTION TO DOWNLOAD AND INSTALL A PLUGIN
		public function install_plugin($repository)
		{
			
			$content = file_get_contents($repository);
var_dump($repository);die();
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
		new WP_Optimiza_Control_Auto_Update();
}

?>