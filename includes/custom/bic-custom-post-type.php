<?php

class Bic_Custom_Post_type
{
    public function __construct(){
        add_action('init', array($this, 'bic_create_custom_post_types'), 101);
        add_action( 'add_meta_boxes_contest-entries', array($this, 'add_add_meta_boxes'));
//	    add_action('pre_get_posts', array($this, 'change_admin_search'));
    }
    public function bic_create_custom_post_types(){
        $types = [
            //================= Contest Entries =================
            'contest-entries'  => array(
                'rewrite'             => array('slug' => 'contest-entries'),
                'label'               => __('Contest Entries',  'bic'),
                'description'         => __('Contest Entries',  'bic'),
                'labels'              => array(
                    'name'               => __('Contest Entries', 'bic'),
                    'singular_name'      => __('Contest Entry',  'bic'),
                    'menu_name'          => __('Contest Entries',  'bic'),
                    'parent_item_colon'  => __('Contest Entry',  'bic'),
                    'all_items'          => __('All Contest Entries',  'bic'),
                    'view_item'          => __('View Contest Entry',  'bic'),
                    'add_new_item'       => __('Add New Contest Entry',  'bic'),
                    'add_new'            => __('Add New',  'bic'),
                    'edit_item'          => __('Edit Contest Entry',  'bic'),
                    'update_item'        => __('Update Contest Entry',  'bic'),
                    'search_items'       => __('Search Contest Entries',  'bic'),
                    'not_found'          => __('Not Found',  'bic'),
                    'not_found_in_trash' => __('Not found in Trash',  'bic'),
                ),
                'supports'            => array('title', 'thumbnail', 'editor'),
                'public'              => true,
                'has_archive'         => false,
                'hierarchical'        => false,
                'show_ui'             => true,
                'show_in_menu'        => true,
                'show_in_nav_menus'   => true,
                'show_in_admin_bar'   => true,
                'can_export'          => true,
                'exclude_from_search' => true,
                'publicly_queryable'  => true,
                'menu_position'       => 10,
            ),
            //================= Contest Entries =================

        ];
        foreach ($types as $type => $args) {
            register_post_type($type, $args);
        }
        register_taxonomy('contest-year', ['contest-entries'], [
            'label' => __('Contest Years', 'bic'),
            'hierarchical' => false,
            'rewrite' => ['slug' => 'Contest Year'],
            'show_admin_column' => true,
            'show_in_rest' => true,
            'labels' => [
                'singular_name' => __('Contest Year', 'bic'),
                'all_items' => __('All Contest Years', 'bic'),
                'edit_item' => __('Edit Contest Year', 'bic'),
                'view_item' => __('View Contest Year', 'bic'),
                'update_item' => __('Update Contest Year', 'bic'),
                'add_new_item' => __('Add New Contest Year', 'bic'),
                'new_item_name' => __('New Contest Year', 'bic'),
                'search_items' => __('Search Contest Years', 'bic'),
                'popular_items' => __('Popular Contest Years', 'bic'),
                'separate_items_with_commas' => __('Separate Contest Years with comma', 'bic'),
                'choose_from_most_used' => __('Choose from most used Contest Years', 'bic'),
                'not_found' => __('No Contest Years found', 'bic'),
            ]
        ]);
    }
    public function add_add_meta_boxes(){
        add_meta_box( 'contest-entries_meta_box', __( 'User Data', 'bic' ), array($this,'build_contest_entries_meta_box'), 'contest-entries', 'normal', 'high' );
    }
    public function build_contest_entries_meta_box(WP_Post $post){
        $user        = get_user_by("ID",$post->post_author);
        $country     = get_user_meta($post->post_author,"country",true);
        $nationality = get_user_meta($post->post_author,"nationality",true);
        $phone       = get_user_meta($post->post_author,"phone",true);
        $state       = Bic_common::acfcs_get_state_data($country["stateCode"]);

	    echo "<div><b style='width: 150px;font-size: 1.3em;display: inline-block'>".__("Name",'bic')        ."</b> : <span style='margin-left: 5px'>" . ($user->first_name   ?:"-" ) . "</span> </div>";
        echo "<div><b style='width: 150px;font-size: 1.3em;display: inline-block'>".__("Email",'bic')       ."</b> : <span style='margin-left: 5px'>" . ($user->user_email   ?:"-" ) . "</span> </div>";
        echo "<div><b style='width: 150px;font-size: 1.3em;display: inline-block'>".__("Phone",'bic')       ."</b> : <span style='margin-left: 5px'>" . ($phone              ?:"-" ) . "</span> </div>";
        echo "<div><b style='width: 150px;font-size: 1.3em;display: inline-block'>".__("Country",'bic')     ."</b> : <span style='margin-left: 5px'>" . ($state->country     ?:"-" ) . "</span> </div>";
        echo "<div><b style='width: 150px;font-size: 1.3em;display: inline-block'>".__("City",'bic')        ."</b> : <span style='margin-left: 5px'>" . ($state->state_name  ?:"-" ) . "</span> </div>";
        echo "<div><b style='width: 150px;font-size: 1.3em;display: inline-block'>".__("Nationality",'bic') ."</b> : <span style='margin-left: 5px'>" . ($nationality        ?:"-" ) . "</span> </div>";
    }

	public function change_admin_search($query){
		if($query->query['post_type'] !== "contest-entries") return $query;
		$search_term = $query->query_vars['s'];
		if ( $search_term != '' ) {
			$query->set( 'meta_query',  array(
				'relation' => 'OR' ,
				 array(
					'key'     => 'user_firstname',
					'value'   => $search_term,
					'compare' => 'LIKE'
				 )
			));
		}
		return $query;
	}
}
/*
 * SELECT
	c.id,
    c.name,
    c.state_code,
    s.name,
    c.country_code,
    cn.name
FROM world.`cities` AS c
LEFT JOIN  world.`states`    AS s  ON c.state_id   = s.id
LEFT JOIN  world.`countries` AS cn ON c.country_id = cn.id
 * */