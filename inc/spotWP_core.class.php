<?php

/**
* 
*/
class spotWP_core {
	
	function init(){
		self::options_panel();
		add_action('init', array('spotWP_core', 'post_types'));
		add_action('admin_head', array('spotWP_core', 'remove_publish_actions'));
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	function remove_publish_actions(){
		global $pagenow;
		if(is_admin() and ($pagenow == 'post-new.php' or $pagenow == 'post.php') and get_post_type() == 'spotwp_ad'){
			echo '<style> #minor-publishing { display: none; } </style>';
		}
	}


	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	function options_panel(){
		
		$titan = TitanFramework::getInstance('spotWP');

		$panel = $titan->createAdminPanel(array(
			'name' 		=> 'spotWP',
			'parent' 	=> 'options-general.php',
			));

	}


	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	function post_types(){
		$labels = array(
			'name'                => __('Ads', 'spotwp'),
			'singular_name'       => __('Ad', 'spotwp'),
			'add_new'             => _x('Add New Ad', 'spotwp', 'spotwp'),
			'add_new_item'        => __('Add New Ad', 'spotwp'),
			'edit_item'           => __('Edit Ad', 'spotwp'),
			'new_item'            => __('New Ad', 'spotwp'),
			'view_item'           => __('View Ad', 'spotwp'),
			'search_items'        => __('Search Ads', 'spotwp'),
			'not_found'           => __('No Ads found', 'spotwp'),
			'not_found_in_trash'  => __('No Ads found in Trash', 'spotwp'),
			'parent_item_colon'   => __('Parent Ad:', 'spotwp'),
			'menu_name'           => __('Ads', 'spotwp'),
		);
		
		$args = array(
			'labels'              => $labels,
			'hierarchical'        => false,
			'description'         => 'Ads for spotWP',
			'taxonomies'          => array(),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => null,
			'menu_icon'           => 'dashicons-slides',
			'show_in_nav_menus'   => true,
			'publicly_queryable'  => false,
			'exclude_from_search' => false,
			'has_archive'         => true,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => false,
			'capability_type'     => 'post',
			'supports'            => array(
				'title'
				)
		);
		
		register_post_type('spotwp_ad', $args);
				
	}

}


?>