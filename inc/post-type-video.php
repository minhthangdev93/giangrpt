<?php
/**
 * Custom post type — Videos.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register video post type.
 */
function rpt_register_video_post_type() {
	$labels = array(
		'name'               => __( 'Videos', 'generatepress_child' ),
		'singular_name'      => __( 'Video', 'generatepress_child' ),
		'menu_name'          => __( 'Videos', 'generatepress_child' ),
		'add_new'            => __( 'Thêm video', 'generatepress_child' ),
		'add_new_item'       => __( 'Thêm video mới', 'generatepress_child' ),
		'edit_item'          => __( 'Sửa video', 'generatepress_child' ),
		'new_item'           => __( 'Video mới', 'generatepress_child' ),
		'view_item'          => __( 'Xem video', 'generatepress_child' ),
		'search_items'       => __( 'Tìm video', 'generatepress_child' ),
		'not_found'          => __( 'Không tìm thấy video.', 'generatepress_child' ),
		'not_found_in_trash' => __( 'Không có video trong thùng rác.', 'generatepress_child' ),
	);

	register_post_type(
		'rpt_video',
		array(
			'labels'              => $labels,
			'public'              => true,
			'has_archive'         => false,
			'show_ui'             => false,
			'show_in_menu'        => false,
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'show_in_rest'        => false,
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
			'rewrite'             => array(
				'slug'       => 'video',
				'with_front' => false,
			),
			'exclude_from_search' => false,
		)
	);
}
add_action( 'init', 'rpt_register_video_post_type' );

/**
 * Legacy: product_cat was attached to rpt_video CPT.
 * Videos are managed on WooCommerce products — no admin taxonomy UI needed.
 */
function rpt_register_video_product_categories() {
	// Intentionally empty — kept for theme switch rewrite flush hook.
}
add_action( 'init', 'rpt_register_video_product_categories', 20 );

/**
 * Remove legacy video_category taxonomy if present.
 */
function rpt_unregister_legacy_video_category_taxonomy() {
	if ( taxonomy_exists( 'video_category' ) ) {
		unregister_taxonomy( 'video_category' );
	}
}
add_action( 'init', 'rpt_unregister_legacy_video_category_taxonomy', 100 );

/**
 * Flush rewrite rules after theme switch.
 */
function rpt_video_flush_rewrite_rules() {
	rpt_register_video_post_type();
	rpt_register_video_product_categories();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'rpt_video_flush_rewrite_rules' );
