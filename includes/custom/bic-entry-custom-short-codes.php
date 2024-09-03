<?php

class Bic_Entry_Custom_Short_codes
{
	use Bic_Entry_helper;
    public function __construct()
    {
        add_action('init',array($this,'bic_register_assets'));
        add_shortcode('bic-contest-entry', array($this,'bic_register_shortcode'));
        add_action('wp_enqueue_scripts', array($this,'bic_add_assets'));
    }
    public function bic_add_assets(){
        wp_register_style( 'bic-contest-general',BIC_PLUGIN_URL.'assets/css/general.css' ,[], BIC_VERSION );
        wp_enqueue_style ( 'bic-contest-general');
    }
    public function bic_register_shortcode($attrs){
        wp_enqueue_style('bic-contest-entry-shortcode');
        wp_enqueue_script('bic-contest-entry-shortcode');
        if(!is_user_logged_in()){
            ob_start();
            include BIC_PLUGIN_PATH."templates/bic-contest-entry.php";
            return ob_get_clean();

        }else{
	        ob_start();
	        $type = "account_already_exists";
	        $is_show_success = get_user_meta(get_current_user_id(),'_show_success',true);
	        if($is_show_success==="1") {
		        $type = "account_created_successfully";
		        update_user_meta(get_current_user_id(),"_show_success",'0');
	        }
	        include BIC_PLUGIN_PATH."templates/bic-alert.php";
	        return ob_get_clean();
        }

    }
    public function bic_register_assets(){
        wp_register_script( 'bic-contest-entry-shortcode-script',BIC_PLUGIN_URL.'assets/js/contest-entry.js' , array( 'jquery','select2' ), BIC_VERSION, true );
        wp_register_style( 'bic-contest-entry-shortcode-style',BIC_PLUGIN_URL.'assets/css/contest-entry.css' ,[], BIC_VERSION, 'all' );
        $this->add_contest_entry();
    }

    private function add_contest_entry()
    {
        if(!is_user_logged_in()&&isset($_POST['consent'])){
            $error = [];
            try{

				for($i=0;$i<count($_FILES["imgs"]["size"]);$i++){
					$size = $_FILES["imgs"]["size"][$i];
					// Check file size
					if ($size > 10485760) {
						wp_safe_redirect( add_query_arg(array("entry_error"=>"img_{$i}_size"),$_POST['current_url']) );
						exit;
					}
					if(empty($_FILES["imgs"]['name'][$i])) continue;
					$file = $_FILES["imgs"]['name'][$i];
					$ext = strtolower(pathinfo($file,PATHINFO_EXTENSION));
					if(empty($ext)||!in_array($ext,['jpg','jpeg','png'])) {
						wp_safe_redirect( add_query_arg(array("entry_error"=>"img_{$i}_filename"),$_POST['current_url']) );
						exit;
					}
				}
                if(empty($error)){
                    $_POST['id'] = $this->save_user();
                    if(is_wp_error($_POST['id'])) {
                        wp_safe_redirect( add_query_arg(array("entry_error"=>"email_exits"),$_POST['current_url']) );
                    }else{
                        $this ->save_entries($_POST['id']);
	                    wp_safe_redirect( add_query_arg(array("success"=>time()),$_POST['current_url']) );
                    }
                    exit;
                }

            }catch (Exception $e){
                wp_safe_redirect( add_query_arg(array("entry_error"=>"general"),$_POST['current_url']) );
            }
        }
    }
    public function get_entries(){
        $current_year = date('Y');
        $entries = get_user_meta(get_current_user_id(),"_attachments_$current_year",true);
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
    private function save_user()
    {
        $id = wp_insert_user([
            "user_email"    => $_POST['email'],
            "user_login"    => $_POST['email'],
            "first_name"    => $_POST['name'],
            "user_pass"     => $_POST['password']
        ]);
        if(is_wp_error($id)) return $id;
        update_user_meta($id,'_country','field_66070eef7bfc2');
        update_user_meta($id,'country',[
            "countryCode" => $_POST['acfcs_country'],
            "stateCode"   => $_POST['acfcs_state']
        ]);
        update_user_meta($id,'_nationality','field_660746d95e742');
        update_user_meta($id,'nationality',$_POST['nationality']);

        update_user_meta($id,'_phone','field_660c6fc890102');
        update_user_meta($id,'phone',$_POST['phone']);

        wp_clear_auth_cookie();
        wp_set_current_user ($id );
        wp_set_auth_cookie  ( $id );

	    $current_lang=get_user_locale($id)?:"en";
	    ob_start();
	    $banner = BIC_PLUGIN_URL."assets/images/email-banner-$current_lang.png";
	    include BIC_PLUGIN_PATH."templates/bic-register-email.php";
	    $message = ob_get_clean();
	    wp_mail( $_POST['email'],
		    __('Thank You for Your Artwork Submission to BIC Art Master 2024','bic') ,
		    $message );
        return $id;
    }
    public function save_entry($id,$title,$state_code,$nationality,$phone){
        $post_id = wp_insert_post([
            "post_author"   => $id,
            "post_title"    => $title,
            "post_status"   => "publish",
            "post_type"     => "contest-entries",
        ]);
		$term_name = date('Y');
	    $x = wp_set_object_terms($post_id,$term_name,'contest-year');
		$state = Bic_common::acfcs_get_state_data($state_code);
        update_post_meta($post_id,'country_code' , $state->country_code);
        update_post_meta($post_id,'country'      , $state->country);
        update_post_meta($post_id,'city'         , $state->state_name);
        update_post_meta($post_id,'nationality'  , $nationality);
        update_post_meta($post_id,'phone'        ,$phone);
        return $post_id;
    }
    private function save_entries($id)
    {
        $files     = $_FILES["imgs"]['name'];
        $country_name=acfcs_get_country_name($_POST["acfcs_country"]);
        $post_ids=[];

        foreach ($files as $i=>$file){
            if(empty($file)){
                $post_ids[]="";
                continue;
            }

            $post_id    = $this->save_entry($id, $_POST["title"][$i],$_POST["acfcs_state"],$_POST["nationality"], $_POST["phone"]);
            $post_ids[] = $post_id;

            $ext = strtolower(pathinfo($file,PATHINFO_EXTENSION));
            //filename: {userId}{postId}-{countryName}({countryCode}).{Ext}
            $fileName="$id$post_id-$country_name({$_POST["acfcs_country"]})";
            $attachment_id = $this->upload_media($_FILES["imgs"]["tmp_name"][$i],$fileName.".$ext",$fileName,"Entry Number $post_id for user $id from {$_POST["acfcs_country"]}",$_POST['acfcs_country'],$country_name);
            set_post_thumbnail( $post_id, $attachment_id );
        }
        $current_year = date('Y');
        update_user_meta($id,"_attachments_$current_year",$post_ids);
        update_user_meta($id,"_show_success",'1');
    }
}
/*
 * SELECT
	c.id,
    c.name,
    c.state_code,
    s.name,
    c.country_code,
    cn.name,
    cn.region
FROM world.`cities` AS c
LEFT JOIN  world.`states`    AS s  ON c.state_id   = s.id
LEFT JOIN  world.`countries` AS cn ON c.country_id = cn.id
WHERE cn.region = 'Africa'
 * */