<?php

if (!class_exists('WP_Optimiza_Control_Auto_Update')) 
{
	class WP_Optimiza_Control_Auto_Update 
	{		
		public $respository_url = "https://githubversions.optimizaclick.com/repositories/view/68282813";
		
		public $temp_name = "temp-wp-optimiza-control.zip";
		
		public $main_file = "optimiza-control.php";
				
		public $url_main_file = "optimiza-control-master/optimiza-control.php";
		
		public $url_update = "wpoptimizacontrol-update";
		
		public $url_version = "wpoptimizacontrol-version";
		
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
	}
		new WP_Optimiza_Control_Auto_Update();
}

?>