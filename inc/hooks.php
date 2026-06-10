<?php
/**
 * GeneratePress hooks — custom header and layout integration.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Body classes for RPT layout modes.
 *
 * @param array $classes Existing body classes.
 * @return array
 */
function rpt_body_classes( $classes ) {
	$classes[] = 'rpt-site';

	if ( class_exists( 'WooCommerce' ) ) {
		$classes[] = 'rpt-catalog-mode';
	}

	return $classes;
}
add_filter( 'body_class', 'rpt_body_classes' );

/**
 * Use classic editor for posts instead of Gutenberg.
 *
 * @param bool   $use_block_editor Whether the block editor is enabled.
 * @param string $post_type        Post type slug.
 * @return bool
 */
function rpt_disable_gutenberg_for_posts( $use_block_editor, $post_type ) {
	if ( 'post' === $post_type ) {
		return false;
	}

	return $use_block_editor;
}
add_filter( 'use_block_editor_for_post_type', 'rpt_disable_gutenberg_for_posts', 10, 2 );

/**
 * Disable comments on news posts.
 */
function rpt_disable_post_comments_support() {
	remove_post_type_support( 'post', 'comments' );
	remove_post_type_support( 'post', 'trackbacks' );
}
add_action( 'init', 'rpt_disable_post_comments_support', 100 );

/**
 * New posts should not accept comments.
 *
 * @param string $status       Default status.
 * @param string $post_type    Post type.
 * @param string $comment_type Comment type.
 * @return string
 */
function rpt_default_post_comment_status( $status, $post_type, $comment_type ) {
	if ( 'comment' === $comment_type && 'post' === $post_type ) {
		return 'closed';
	}

	return $status;
}
add_filter( 'default_comment_status', 'rpt_default_post_comment_status', 10, 3 );

/**
 * Close comments on the front end for posts.
 *
 * @param bool $open    Whether comments are open.
 * @param int  $post_id Post ID.
 * @return bool
 */
function rpt_close_post_comments( $open, $post_id ) {
	if ( 'post' === get_post_type( $post_id ) ) {
		return false;
	}

	return $open;
}
add_filter( 'comments_open', 'rpt_close_post_comments', 20, 2 );
add_filter( 'pings_open', 'rpt_close_post_comments', 20, 2 );

/**
 * Hide existing comments on posts.
 *
 * @param array    $comments Comments array.
 * @param int|WP_Post $post_id Post ID.
 * @return array
 */
function rpt_hide_post_comments( $comments, $post_id ) {
	if ( 'post' === get_post_type( $post_id ) ) {
		return array();
	}

	return $comments;
}
add_filter( 'comments_array', 'rpt_hide_post_comments', 20, 2 );

/**
 * Report zero comments on posts so themes skip the comments template.
 *
 * @param int $count   Comment count.
 * @param int $post_id Post ID.
 * @return int
 */
function rpt_post_comments_number( $count, $post_id ) {
	if ( 'post' === get_post_type( $post_id ) ) {
		return 0;
	}

	return $count;
}
add_filter( 'get_comments_number', 'rpt_post_comments_number', 20, 2 );

/**
 * Swap GeneratePress default header for RPT site header.
 */
function rpt_setup_header_hooks() {
	remove_action( 'generate_header', 'generate_construct_header' );
	add_action( 'generate_header', 'rpt_render_site_header' );

	// Prevent duplicate navigation output from GP placement hooks.
	remove_action( 'generate_after_header', 'generate_add_navigation_after_header', 5 );
	remove_action( 'generate_before_header', 'generate_add_navigation_before_header', 5 );
	remove_action( 'generate_after_header_content', 'generate_add_navigation_float_right', 5 );
}
add_action( 'after_setup_theme', 'rpt_setup_header_hooks', 20 );

/**
 * Render custom site header template part.
 */
function rpt_render_site_header() {
	get_template_part( 'template-parts/header/site', 'header' );
}

/**
 * Swap GeneratePress default footer for RPT site footer.
 */
function rpt_setup_footer_hooks() {
	remove_action( 'generate_footer', 'generate_construct_footer_widgets', 5 );
	remove_action( 'generate_footer', 'generate_construct_footer' );
	add_action( 'generate_footer', 'rpt_render_site_footer' );
}
add_action( 'after_setup_theme', 'rpt_setup_footer_hooks', 20 );

/**
 * Render custom site footer template part.
 */
function rpt_render_site_footer() {
	get_template_part( 'template-parts/footer/site', 'footer' );
}
