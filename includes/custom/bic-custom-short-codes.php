<?php

class Bic_Custom_Short_codes
{
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
        if(!is_user_logged_in()&&isset($_POST['consent'])){
            $error = [];
            try{

                foreach ($_FILES["imgs"]["size"] as $i=>$size){
                    $index = $i+1;
                    // Check file size
                    if ($size > 10485760) {
                        wp_safe_redirect( add_query_arg(array("error"=>"img_{$i}_size"),$_POST['current_url']) );
                        exit;
                    }
                }
                if(empty($error)){
                    $_POST['id'] = $this->save_user();
                    if(is_wp_error($_POST['id'])) {
                        wp_safe_redirect( add_query_arg(array("error"=>"email_exits"),$_POST['current_url']) );
                    }else{
                        $this ->save_entries($_POST['id']);
                        wp_safe_redirect( $_POST['current_url'] );
                    }
                    exit;
                }

            }catch (Exception $e){
                wp_safe_redirect( add_query_arg(array("error"=>"general"),$_POST['current_url']) );
            }
        }
        elseif(is_user_logged_in()&&isset($_POST['current_url'])){
           $this->edit_entries();
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

        update_user_meta($id,'title',$_POST['title']);
        update_user_meta($id,'description',$_POST['description']);
        wp_clear_auth_cookie();
        wp_set_current_user ($id );
        wp_set_auth_cookie  ( $id );
        return $id;
    }
    public function save_entry($id,$title,$description,$state_code,$nationality,$phone){
        $term_name = date('Y');
        $term =  get_term_by('name',$term_name,'contest-year');
        if(!$term){
            $term_id = wp_insert_term($term_name, 'contest-year', array(
                'description' => "$term_name Contest",
                'slug' => $term_name,
            ));
        }else{
            $term_id = $term->term_id;
        }
        $post_id = wp_insert_post([
            "post_author"   => $id,
            "post_title"    => $title,
            "post_content"  => $description,
            "post_excerpt"  => $description,
            "post_status"   => "publish",
            "post_type"     => "contest-entries",
            "post_category" => [$term_id]
        ]);
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

            $post_id    = $this->save_entry($id, $_POST["title"], $_POST["description"],$_POST["acfcs_state"],$_POST["nationality"], $_POST["phone"]);
            $post_ids[] = $post_id;

            $ext = strtolower(pathinfo($file,PATHINFO_EXTENSION));
            //filename: {userId}{postId}-{countryName}({countryCode}).{Ext}
            $fileName="$id$post_id-$country_name({$_POST["acfcs_country"]})";
            $attachment_id = $this->upload_media($_FILES["imgs"]["tmp_name"][$i],$fileName.".$ext",$fileName,"Entry Number $post_id for user $id from {$_POST["acfcs_country"]}",$_POST['acfcs_country']);
            set_post_thumbnail( $post_id, $attachment_id );
        }
        $current_year = date('Y');
        update_user_meta($id,"_attachments_$current_year",$post_ids);
    }

    public function upload_media($image_temp,$filename, $name, $alt,$country) {
        $upload_dir   = wp_upload_dir();
        $current_year = date('Y');
        $bic_path     = "/bic-$current_year/{$country}";
        if(!file_exists($upload_dir["basedir"].$bic_path)){
            mkdir($upload_dir["basedir"].$bic_path,0777,true);
        }
        $direcreate   = wp_mkdir_p($upload_dir["basedir"].$bic_path);
        $file         = $upload_dir["basedir"].$bic_path . '/' . $filename;
        move_uploaded_file($image_temp, $file);
        $wp_filetype  = wp_check_filetype($filename, null);

        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => "$alt-$name",
            'post_content' => '',
            'post_status' => 'inherit'
        );
        $attach_id = wp_insert_attachment($attachment, $file);
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        $attach_data = wp_generate_attachment_metadata($attach_id, $file);
        wp_update_attachment_metadata($attach_id, $attach_data);
        return $attach_id;
    }

    private function edit_entries()
    {
        $entries      = $this->get_entries();
        if(empty($entries)) $entries= array("","","");
        $id           = get_current_user_id();
        $country      = get_user_meta($id,'country',true);
        $country_name = acfcs_get_country_name($country["countryCode"]??"");
        $files        = $_FILES["imgs"]['name']?:[];
        $title        = get_user_meta($id,'title',true);
        $description  = get_user_meta($id,'description',true);
        $nationality  = get_user_meta($id,'nationality',true);
        $phone        = get_user_meta($id,'phone',true);

        foreach ($files as $i=>$file){
            if(!$file) continue;
            $ext            = strtolower(pathinfo($file,PATHINFO_EXTENSION));
            $post_id        = $entries[$i];
            if(empty($post_id)){
                $post_id     = $this->save_entry($id, $title, $description,$country['stateCode'],$nationality,$phone);
                $entries[$i] = $post_id;
            }
            //filename: {userId}{postId}-{countryName}({countryCode}).{Ext}
            $fileName       = "$id$post_id-$country_name({$country["countryCode"]})";
            $old_attachment = get_post_thumbnail_id( $post_id );
            $attachment_id  = $this->upload_media($_FILES["imgs"]["tmp_name"][$i],$fileName.".$ext",$fileName,"Entry Number $post_id for user $id from {$_POST["acfcs_country"]}",$country["countryCode"]);
            set_post_thumbnail( $post_id, $attachment_id );
            if(!empty($old_attachment)) wp_delete_attachment($old_attachment,true);
        }
        $current_year = date('Y');
        update_user_meta($id,"_attachments_$current_year",$entries);
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