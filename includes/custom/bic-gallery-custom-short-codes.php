<?php

class Bic_Gallery_Custom_Short_codes
{
    public function __construct()
    {
        add_action('init',array($this,'bic_register_assets'));
        add_shortcode('bic-gallery', array($this,'bic_register_shortcode'));
	    add_action("wp_ajax_get_gallery_entries", array($this,"get_gallery_entries"));
	    add_action("wp_ajax_nopriv_get_gallery_entries", array($this,"get_gallery_entries"));
    }
    public function bic_register_shortcode($attrs){
        wp_enqueue_style('bic-contest-entry-shortcode');
	    wp_enqueue_script('bic-contest-gallery');
        ob_start();
        include BIC_PLUGIN_PATH."templates/bic-gallery.php";
        return ob_get_clean();
    }
    public function bic_register_assets(){
        wp_register_style( 'bic-contest-entry-shortcode',BIC_PLUGIN_URL.'assets/css/contest-entry.css' ,[], BIC_VERSION, 'all' );
	    wp_register_script( 'bic-contest-gallery',BIC_PLUGIN_URL.'assets/js/contest-gallery.js' , array( 'jquery'), BIC_VERSION, true );
	    wp_localize_script( 'bic-contest-gallery', 'bic_contest', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
    }
	public function get_entries($page=1,$limit=9,$search=null){
		global $wpdb;

		$offest = ($page-1)*$limit;

		$sql    = "
					SELECT p.ID , p.post_title , u.meta_value
					FROM       {$wpdb->prefix}posts    as p
					INNER join {$wpdb->prefix}usermeta as u  on( p.post_author = u.user_id AND u.meta_key='first_name' )
					LEFT  join {$wpdb->prefix}postmeta as pm on(p.ID = pm.post_id AND pm.meta_key = 'show_in_gallery' ) 
					WHERE pm.meta_value = 1 
					  AND ( 
					        p.`post_title` like %s
						or  u.meta_value   like %s
					  )
					LIMIT $limit OFFSET $offest;";
		$query   = $wpdb->prepare($sql,"%".$search."%","%".$search."%");
		$result  = $wpdb->get_results( $query );
		$posts_html = "";
		foreach ($result as $post){
			ob_start();
			include BIC_PLUGIN_PATH."templates/bic-gallery-img.php";
			$posts_html.= ob_get_clean();
		}
		return $posts_html;
	}

	public function get_gallery_entries(){
		$page   = (int)$_POST['page']?:1;
		$limit  = (int)$_POST['limit']?:9;
		$search = $_POST['search']?:null;
		echo json_encode([
			"status"  => 'OK',
			"entries" => $this->get_entries($page,$limit,$search)
		]);
		exit();
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