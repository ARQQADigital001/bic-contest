<?php

class Bic_Search_Media
{
    public function __construct(){
		add_action( 'admin_menu', array($this,'add_top_lvl_menu') );
   }
   public function add_top_lvl_menu(){
	   add_submenu_page(
		   "edit.php?post_type=contest-entries",
		   __('Search In Contest Images','bic'),
		   __('Search In Contest Images','bic'),
		   'administrator',
		   'search-contest-images',
		   array($this,'search_contest')
	   );

   }
   public function search_contest(){
		$search = $_REQUEST['filename']?:"";
	    echo "<h2 style='padding-top: 40px'>".__('Search Contest Images','bic')."</h2>";
	    echo "<div class='postbox' style='margin-right: 20px;'>";
	    echo "<div class='inside'>";
	    global $wp;
	    $current_page = add_query_arg( $wp->query_vars, home_url( $wp->request ) );
	    echo "<form method='post' action='$current_page/wp-admin/edit.php?post_type=contest-entries&page=search-contest-images'>";
	    echo "<label for='filename' style='margin:10px'>".__('File Name','bic')."</label>";
	    echo "<input type='text'    style='margin:10px' name='filename' value='$search' placeholder='".__('Search','bic')."'>";
	    echo "<input type='submit'  style='margin:10px' class='button button-primary' name='search_imgs' value='".__('Search','bic')."'>";
	    echo "</form>";
	    echo "</div>";
	    echo "</div>";
		if($search){
			global $wpdb;
			$query   = $wpdb->prepare( "
							SELECT apm.post_id    as 'attachement_id', 
								   pp.ID          as 'entry_id',
								   pp.post_title  as 'entry_title',
								   pp.post_author as 'entry_author'  
							FROM {$wpdb->prefix}postmeta       as apm  
							inner join {$wpdb->prefix}postmeta as ppm on (apm.post_id = ppm.meta_value and ppm.meta_key='_thumbnail_id')
							inner join {$wpdb->prefix}posts    as pp  on (pp.ID = ppm.post_id)
							WHERE   apm.meta_key = '_wp_attached_file'
							   and  apm.meta_value like %s", "%$search%");
			$results = $wpdb->get_results( $query );
			if(!empty($results)){
				foreach ($results as $result){
					echo "<div class='postbox' style='margin-right: 20px;'>";
					echo "<div class='inside' style='display: flex;'>";
					$attachment  = wp_get_attachment_url( get_post_thumbnail_id( $result->entry_id ));
					$user        = get_user_by("ID",$result->entry_author);
					$country     = get_user_meta($result->entry_author,"country",true);
					$nationality = get_user_meta($result->entry_author,"nationality",true);
					$phone       = get_user_meta($result->entry_author,"phone",true);
					$state       = Bic_common::acfcs_get_state_data($country["stateCode"]);
					$post_url    = get_edit_post_link($result->entry_id);
					$user_url    = get_edit_user_link($result->entry_author);
					echo "<div style='flex: 1;'>";
					echo "<div style='margin-left: 5px'> <img style='width: 100%;max-width: 250px;' src='$attachment' alt='{$result->entry_title}'> </div>";
					echo "</div>";

					echo "<div style='flex: 3;margin: auto;'>";
					echo "<div><b style='width: 150px;font-size: 1.3em;display: inline-block'>".__("Title",'bic')       ."</b> : <span style='margin-left: 5px'> <a href='$post_url'>" . ($result->entry_title ?:"-" )        . "</a> </span> </div>";
					echo "<div><b style='width: 150px;font-size: 1.3em;display: inline-block'>".__("Name",'bic')        ."</b> : <span style='margin-left: 5px'> <a href='$user_url'>" . ($user->first_name   ?:"-" )         . "</a> </span> </div>";
					echo "<div><b style='width: 150px;font-size: 1.3em;display: inline-block'>".__("Email",'bic')       ."</b> : <span style='margin-left: 5px'>" . ($user->user_email   ?:"-" )                              . "</span> </div>";
					echo "<div><b style='width: 150px;font-size: 1.3em;display: inline-block'>".__("Phone",'bic')       ."</b> : <span style='margin-left: 5px'>" . ($phone              ?:"-" )                              . "</span> </div>";
					echo "<div><b style='width: 150px;font-size: 1.3em;display: inline-block'>".__("Country",'bic')     ."</b> : <span style='margin-left: 5px'>" . ($state->country     ?:"-" )                              . "</span> </div>";
					echo "<div><b style='width: 150px;font-size: 1.3em;display: inline-block'>".__("City",'bic')        ."</b> : <span style='margin-left: 5px'>" . ($state->state_name  ?:"-" )                              . "</span> </div>";
					echo "<div><b style='width: 150px;font-size: 1.3em;display: inline-block'>".__("Nationality",'bic') ."</b> : <span style='margin-left: 5px'>" . ($nationality        ?:"-" )                              . "</span> </div>";
					echo "</div>";

					echo "</div>";
					echo "</div>";
				}

			}else{
				echo "<div class='postbox' style='margin-right: 20px;'>";
				echo "<div class='inside' style='display: flex;'>";
				echo "<p style='text-align: center'>".__('No results found','bic')."</p>";
				echo "</div>";
				echo "</div>";
			}

		}
   }
}
//SELECT apm.post_id, ppm.post_id,pp.post_title
// FROM wp_postmeta as apm
// inner join wp_postmeta as ppm on (apm.post_id = ppm.meta_value and ppm.meta_key='_thumbnail_id')
// inner join wp_posts as pp on (apm.post_id = ppm.post_id)
// WHERE apm.meta_key = '_wp_attached_file' and
// apm.meta_value like %s