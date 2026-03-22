<?php
/**
 * Custom post types and taxonomies (theme).
 */

add_action( 'after_switch_theme', 'client_flush_rewrite_rules' );

function client_flush_rewrite_rules() {
	flush_rewrite_rules();
}

function custom_post() {

	// People: data-only post type for About/Team pages.
	register_post_type(
		'people',
		array(
			'labels'              => array(
				'name'               => __( 'People', 'client_theme' ),
				'singular_name'      => __( 'Person', 'client_theme' ),
				'all_items'          => __( 'All People', 'client_theme' ),
				'add_new'            => __( 'Add New', 'client_theme' ),
				'add_new_item'       => __( 'Add New Person', 'client_theme' ),
				'edit_item'          => __( 'Edit Person', 'client_theme' ),
				'new_item'           => __( 'New Person', 'client_theme' ),
				'view_item'          => __( 'View Person', 'client_theme' ),
				'search_items'       => __( 'Search People', 'client_theme' ),
				'not_found'          => __( 'No people found.', 'client_theme' ),
				'not_found_in_trash' => __( 'No people found in Trash', 'client_theme' ),
				'parent_item_colon'  => '',
			),
			'description'         => __( 'People used on About/Team style pages.', 'client_theme' ),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'query_var'           => true,
			'menu_position'       => 10,
			'menu_icon'           => 'dashicons-groups',
			'rewrite'             => array( 'slug' => 'people', 'with_front' => false ),
			'has_archive'         => false,
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
			'taxonomies'          => array( 'person_category', 'person_tag' ),
		)
	);

	register_taxonomy(
		'person_category',
		array( 'people' ),
		array(
			'hierarchical'      => true,
			'labels'            => array(
				'name'              => __( 'People Categories', 'client_theme' ),
				'singular_name'     => __( 'People Category', 'client_theme' ),
				'search_items'      => __( 'Search People Categories', 'client_theme' ),
				'all_items'         => __( 'All People Categories', 'client_theme' ),
				'parent_item'       => __( 'Parent People Category', 'client_theme' ),
				'parent_item_colon' => __( 'Parent People Category:', 'client_theme' ),
				'edit_item'         => __( 'Edit People Category', 'client_theme' ),
				'update_item'       => __( 'Update People Category', 'client_theme' ),
				'add_new_item'      => __( 'Add New People Category', 'client_theme' ),
				'new_item_name'     => __( 'New People Category Name', 'client_theme' ),
			),
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'person-category' ),
		)
	);

	register_taxonomy(
		'person_tag',
		array( 'people' ),
		array(
			'hierarchical'      => false,
			'labels'            => array(
				'name'                       => __( 'People Tags', 'client_theme' ),
				'singular_name'              => __( 'People Tag', 'client_theme' ),
				'search_items'               => __( 'Search People Tags', 'client_theme' ),
				'all_items'                  => __( 'All People Tags', 'client_theme' ),
				'edit_item'                  => __( 'Edit People Tag', 'client_theme' ),
				'update_item'                => __( 'Update People Tag', 'client_theme' ),
				'add_new_item'               => __( 'Add New People Tag', 'client_theme' ),
				'new_item_name'              => __( 'New People Tag Name', 'client_theme' ),
				'separate_items_with_commas' => __( 'Separate people tags with commas', 'client_theme' ),
				'add_or_remove_items'        => __( 'Add or remove people tags', 'client_theme' ),
				'choose_from_most_used'      => __( 'Choose from the most used people tags', 'client_theme' ),
			),
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'person-tag' ),
		)
	);
}

add_action( 'init', 'custom_post' );
