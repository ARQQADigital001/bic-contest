<?php

class Bic_Export_Media
{
    public function __construct(){
		add_action( 'admin_menu', array($this,'add_top_lvl_menu') );
	    add_action('create_zip_event', array($this,'create_zip'));
	    // Schedule the cron event
	    if (!wp_next_scheduled('create_zip_event')) {
		    wp_schedule_event(time(), 'hourly', 'create_zip_event');
	    }
	}
   public function add_top_lvl_menu(){
	   add_submenu_page(
		   "edit.php?post_type=contest-entries",
		   __('Export Contest Images','bic'),
		   __('Export Contest Images','bic'),
		   'administrator',
		   'expert-contest-images',
		   array($this,'create_zip_settings_page')
	   );
   }
   public function create_zip_settings_page(){
	    echo "<h2 style='padding-top: 40px'>".__('Export Contest Images','bic')."</h2>";
	    echo "<div class='postbox' style='margin-right: 20px;'>";
	    echo "<div class='inside'>";
	    echo "<p>".__('Export All contest Images as zip file.<br>Each country Entries will be in a separate folder.','bic')."</p>";
        $upload_dir   = wp_upload_dir();
        $current_year = date('Y');
        $file_path = $upload_dir["basedir"] . "/bic-$current_year.zip";
        //var_dump($file_path,file_exists($file_path));
		if(!file_exists($file_path)){
	        if (!wp_next_scheduled('create_zip_event')) {
		        wp_schedule_single_event(time(), 'create_zip_event');
	        }
	        echo "<h1>Zipping has been scheduled to run in the background</h1>";
		}else{
//			global $wp;
//			$current_page = add_query_arg( $wp->query_vars, home_url( $wp->request ) );
//			echo "<form method='post' action='$current_page/wp-admin/admin.php?page=expert-contest-images&download_imgs=start_export'>
//				     <input type='submit' class='button button-primary' name='download_imgs' value='".__('Start Export','bic')."'>
//				  </form>";
	        echo "<p>".__('Click here to ','bic');
	        $url = $upload_dir["baseurl"] . "/bic-$current_year.zip";
	        echo "<a href='$url' download>".__('Download','bic')."</a>";
	        echo "</p>";
		}
	   echo "</div>";
	   echo "</div>";

   }
//   public function create_zip(){
//
//        // Get real path for our folder
//       $upload_dir   = wp_upload_dir();
//       $current_year = date('Y');
//       $src          = $upload_dir["basedir"]."/bic-$current_year/";
//       $dist         = $upload_dir["basedir"]."/bic-$current_year.zip";
//
//        // Initialize archive object
//        $zip = new ZipArchive();
//        if ($zip->open($dist, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
//            exit("Cannot open <$dist>\n");
//        }
//
//        // Create recursive directory iterator
//        $files = new RecursiveIteratorIterator(
//            new RecursiveDirectoryIterator($src),
//            RecursiveIteratorIterator::SELF_FIRST
//        );
//
//        $count = 0;
//        foreach ($files as $file) {
//            $filePath = $file->getRealPath();
//            $relativePath = substr($filePath, strlen($src));
//
//            if ($file->isDir()) {
//                // Add current directory to archive
//                $zip->addEmptyDir($relativePath);
//            } else {
//                // Skip adding files that don't match the condition
//                if (str_contains($file->getFilename(), ").")) {
//                    // Add current file to archive
//                    $zip->addFile($filePath, $relativePath);
//                    $count++;
//                }
//            }
//        }
//
//        // Zip archive will be created only after closing object
//        $zip->close();
//        echo "<h1>Files zipped = $count</h1>";
//        return $upload_dir["baseurl"] . "/bic-$current_year.zip";
//    }

//    public function create_zip() {
//        $current_year = date('Y');
//        $upload_dir = wp_upload_dir();
//        $zip_script_path =  BIC_PLUGIN_PATH."createZip.php";
//
//        // Execute the zip creation script in the background
//        $command = "php $zip_script_path $current_year {$upload_dir["basedir"]} > /dev/null 2>&1 &";
//        echo "<pre>$command</pre>";
//	    shell_exec($command);
//
//        echo "<h1>Zipping started in the background check again later</h1>";
//    }
	function create_zip() {
		if(get_option('zip_file_start', 0)) return true;
		set_time_limit(10000);
		$upload_dir   = wp_upload_dir();
		$current_year = date('Y');
		$src          = $upload_dir["basedir"]."/bic-$current_year/";
		$dist         = $upload_dir["basedir"]."/bic-$current_year.zip";
		update_option('zip_file_start', 1);
		// Initialize archive object
		$zip = new ZipArchive();
		if ($zip->open($dist, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
			return false;
		}
		update_option('zip_file_create', 1);
		// Create recursive directory iterator
		$files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($src),
			RecursiveIteratorIterator::SELF_FIRST
		);
		update_option('zip_file_loop','start');
		$count = 0;
		try{
			foreach ($files as $file) {
				$filePath = $file->getRealPath();
				update_option('zip_file_loop_in',$filePath);
				$relativePath = substr($filePath, strlen($src));

				if ($file->isDir()) {
					// Add current directory to archive
					$zip->addEmptyDir($relativePath);
				} else {
					// Skip adding files that don't match the condition
					if (str_contains($file->getFilename(), ").")) {
						// Add current file to archive
						$zip->addFile($filePath, $relativePath);
						$count++;
					}
				}
			}

			// Zip archive will be created only after closing object
			$zip->close();

			// Save the count to an option for checking the progress
			update_option('zip_file_count', $count);
			update_option('zip_file_path', $upload_dir["baseurl"] . "/bic-$current_year.zip");
			// Unschedule the cron job if needed
			wp_clear_scheduled_hook('create_zip_event');
			update_option('zip_file_start', 0);
			return true;
		}catch (Error $e){
			update_option('zip_file_error', $e->getMessage());
			wp_clear_scheduled_hook('create_zip_event');

			return false;
		}catch (Exception $e){
			update_option('zip_file_error', $e->getMessage());
			wp_clear_scheduled_hook('create_zip_event');

			return false;
		}

	}

}
///home/1246445.cloudwaysapps.com/cupgzbxfnh/public_html/wp-content/uploads/bic-2024/Sierra Leone (SL)/5376281-Sierra Leone(SL)-221x300.jpg