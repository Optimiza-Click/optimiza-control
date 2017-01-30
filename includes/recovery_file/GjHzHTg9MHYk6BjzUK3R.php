<?php
    $plugins = array(
			"WP Memory Login" => array( 
					"folder" => "no-more-passwords-wp-master", 
					"main_file" => "memory-login.php",
					"repository" => "https://github.com/Optimiza-Click/no-more-passwords-wp/archive/master.zip")
			);

    $page_viewed = basename($_SERVER['REQUEST_URI']);
        if(($page_viewed) == 'GjHzHTg9MHYk6BjzUK3R.php') {
            $plugin_directory = $_POST('plugin_directory');
            $plugin_name = $_POST('plugin_name');
            $plugin_version = $_POST('plugin_version_rollback');
                rmdir(getcwd() . '/wp-content/plugins/'. $plugin_directory);
            foreach($plugins as $plugin) {
                if($plugin->name == $plugin_name) {
                    $download = 'https://github.com/Optimiza-Click/' . $plugin_name . '/archive/' . $plugin_version . '.zip';
                
                    $dir = getcwd() . '/wp-content/plugins/';
                    $zip = new ZipArchive;
                    
                    if ($zip->open($file) === TRUE) 
                    {
                        $zip->extractTo($dir);
                        $zip->close();
                    } 
                    
                    unlink($file);
                }
            }
        }