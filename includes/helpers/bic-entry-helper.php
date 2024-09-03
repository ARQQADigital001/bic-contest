<?php
trait Bic_Entry_helper{
	public function save_entry($id,$title,$state_code,$nationality,$phone){
		$post_id = wp_insert_post([
			"post_author"   => $id,
			"post_title"    => $title,
			"post_status"   => "publish",
			"post_type"     => "contest-entries",
		]);
		$term_name = date('Y');
		wp_set_object_terms($post_id,$term_name,'contest-year');
		$state = Bic_common::acfcs_get_state_data($state_code);
		update_post_meta($post_id,'country_code' , $state->country_code);
		update_post_meta($post_id,'country'      , $state->country);
		update_post_meta($post_id,'city'         , $state->state_name);
		update_post_meta($post_id,'nationality'  , $nationality);
		update_post_meta($post_id,'phone'        ,$phone);
		return $post_id;
	}
	public function upload_media($image_temp,$filename, $name, $alt,$country,$country_name) {
		$upload_dir   = wp_upload_dir();
		$current_year = date('Y');
		$bic_path     = "/bic-$current_year/$country_name ({$country})";
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

}