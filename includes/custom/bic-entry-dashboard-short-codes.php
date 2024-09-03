<?php

class Bic_Dashboard_Custom_Short_codes
{
	use Bic_Entry_helper;
    public function __construct()
    {
        add_action('init',array($this,'bic_register_assets'));
        add_shortcode('bic-contest-dashboard', array($this,'bic_register_shortcode'));
        add_action('wp_enqueue_scripts', array($this,'bic_add_assets'));
    }
    public function bic_add_assets(){
	    wp_register_script( 'bic-contest-entry-shortcode-script',BIC_PLUGIN_URL.'assets/js/contest-entry.js' , array( 'jquery','select2' ), BIC_VERSION, true );
	    wp_register_style( 'bic-contest-entry-shortcode-style',BIC_PLUGIN_URL.'assets/css/contest-entry.css' ,[], BIC_VERSION, 'all' );
    }
    public function bic_register_shortcode($attrs){
        wp_enqueue_style('bic-contest-entry-shortcode-style');
        wp_enqueue_script('bic-contest-entry-shortcode-script');
        if(!is_user_logged_in()){
            ob_start();
			$type = "create_an_account";
            include BIC_PLUGIN_PATH."templates/bic-alert.php";
            return ob_get_clean();
        }else{
            ob_start();
            include BIC_PLUGIN_PATH."templates/bic-dashboard.php";
            return ob_get_clean();
        }

    }
    public function bic_register_assets(){
        wp_register_script( 'bic-contest-entry-shortcode',BIC_PLUGIN_URL.'assets/js/contest-entry.js' , array( 'jquery','select2' ), BIC_VERSION, true );
        wp_register_style( 'bic-contest-entry-shortcode',BIC_PLUGIN_URL.'assets/css/contest-entry.css' ,[], BIC_VERSION, 'all' );
        $this->add_contest_entry();
    }

    private function add_contest_entry()
    {
       if(is_user_logged_in()&&isset($_POST['current_url'])){
           $this->edit_entries();
       }
    }
    public function get_entries(){
        $current_year = date('Y');
        $entries = get_user_meta(get_current_user_id(),"_attachments_$current_year",true);
        foreach ($entries as $i=>$post_id){
            if(!get_post($post_id) ) {
                $entries[$i]="";
            }
        }
        return $entries;
    }
    public function get_attachments($entries=null){
        $attachments=[];
        if(!$entries) $entries = $this->get_entries();
        if($entries){
            foreach ($entries as $entry){
                $attachments[] = wp_get_attachment_url( get_post_thumbnail_id( $entry ));
            }
        }
        return $attachments;
    }
   private function edit_entries()
    {
        $entries      = $this->get_entries();
        if(empty($entries)) $entries= array("","","");
        $id           = get_current_user_id();
        $country      = get_user_meta($id,'country',true);
        $country_name = acfcs_get_country_name($country["countryCode"]??"");
        $files        = $_FILES["imgs"]['name']?:[];
        $title        = $_POST['title'];
        $nationality  = get_user_meta($id,'nationality',true);
        $phone        = get_user_meta($id,'phone',true);
        foreach ($files as $i=>$file){
	        $post_id        = $entries[$i];
            if(!$file&&!empty($post_id)){
	            wp_update_post([
		            "ID"         => $post_id,
		            "post_title" => $title[$i]
	            ]);
	            continue;
            }
	        $size = $_FILES["imgs"]["size"][$i];
	        if(empty($size)) continue;
	        // Check file size
	        if ($size > 10485760) {
		        wp_safe_redirect( add_query_arg(array("entry_error"=>"img_{$i}_size"),$_POST['current_url']) );
		        exit;
	        }
	        if(empty($file)) continue;
	        $ext            = strtolower(pathinfo($file,PATHINFO_EXTENSION));
	        if(empty($ext)||!in_array($ext,['jpg','jpeg','png'])) {
		        wp_safe_redirect( add_query_arg(array("entry_error"=>"img_{$i}_filename"),$_POST['current_url']) );
		        exit;
	        }
            if(empty($post_id)){
                $post_id     = $this->save_entry($id, $title[$i], $country['stateCode'], $nationality, $phone);
                $entries[$i] = $post_id;
            }
            //filename: {userId}{postId}-{countryName}({countryCode}).{Ext}
            $fileName       = "$id$post_id-$country_name({$country["countryCode"]})";
            $old_attachment = get_post_thumbnail_id( $post_id );
            $attachment_id  = $this->upload_media($_FILES["imgs"]["tmp_name"][$i],$fileName.".$ext",$fileName,"Entry Number $post_id for user $id from {$_POST["acfcs_country"]}",$country["countryCode"],$country_name);
            set_post_thumbnail( $post_id, $attachment_id );
            if(!empty($old_attachment)) wp_delete_attachment($old_attachment,true);
        }
	    $deletes        = $_POST['delete_post'];
		foreach ($deletes as $post_id=>$post_to_delete){
			if($post_to_delete=='confirmed'){
				$old_attachment = get_post_thumbnail_id( $post_id );
				wp_delete_attachment($old_attachment,true);
				wp_delete_post($post_id);
			}
		}
        $entries      = array_diff($entries,$deletes);
        $current_year = date('Y');
        update_user_meta($id,"_attachments_$current_year",$entries);
    }
}
