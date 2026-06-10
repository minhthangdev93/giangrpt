<?php
/**
 * Single news post helpers and layout.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether current view is a single blog post.
 *
 * @return bool
 */
function rpt_is_single_news_post() {
	return is_singular( 'post' );
}

/**
 * Whether news archive or single styles should load.
 *
 * @return bool
 */
function rpt_uses_news_layout() {
	return rpt_is_news_page() || rpt_is_single_news_post();
}

/**
 * News archive URL for breadcrumbs.
 *
 * @return string
 */
function rpt_get_news_archive_url() {
	$pages = get_pages(
		array(
			'meta_key'   => '_wp_page_template',
			'meta_value' => RPT_NEWS_PAGE_TEMPLATE,
			'number'     => 1,
		)
	);

	if ( ! empty( $pages[0] ) ) {
		return get_permalink( $pages[0] );
	}

	$posts_page_id = (int) get_option( 'page_for_posts' );

	if ( $posts_page_id ) {
		return get_permalink( $posts_page_id );
	}

	return home_url( '/' );
}

/**
 * Display date for single post hero.
 *
 * @param int|null $post_id Post ID.
 * @return string
 */
function rpt_get_single_post_date_display( $post_id = null ) {
	$post_id   = $post_id ? (int) $post_id : get_the_ID();
	$timestamp = get_post_timestamp( $post_id );

	if ( ! $timestamp ) {
		return '';
	}

	$day   = wp_date( 'j', $timestamp );
	$month = wp_date( 'n', $timestamp );
	$year  = wp_date( 'Y', $timestamp );

	return sprintf(
		/* translators: 1: day, 2: month number, 3: year */
		__( '%1$s tháng %2$s, %3$s', 'generatepress_child' ),
		$day,
		$month,
		$year
	);
}

/**
 * Render single post breadcrumb.
 */
function rpt_render_single_post_breadcrumb() {
	rpt_render_breadcrumb(
		array(
			array(
				'label' => __( 'Trang chủ', 'generatepress_child' ),
				'url'   => home_url( '/' ),
			),
			array(
				'label' => rpt_get_news_page_title(),
				'url'   => rpt_get_news_archive_url(),
			),
			array(
				'label' => get_the_title(),
			),
		)
	);
}

/**
 * Disable default GeneratePress single post chrome.
 */
function rpt_setup_single_post_layout() {
	if ( ! rpt_is_single_news_post() ) {
		return;
	}

	remove_action( 'generate_before_content', 'generate_featured_page_header_inside_single', 10 );
	remove_action( 'generate_after_entry_header', 'generate_post_image', 10 );
	remove_action( 'generate_after_entry_title', 'generate_post_meta', 10 );
	remove_action( 'generate_after_entry_content', 'generate_footer_meta', 10 );
}
add_action( 'wp', 'rpt_setup_single_post_layout' );

/**
 * Hide default entry header on single posts.
 *
 * @param bool $show Whether to show entry header.
 * @return bool
 */
function rpt_single_post_show_entry_header( $show ) {
	if ( rpt_is_single_news_post() ) {
		return false;
	}

	return $show;
}
add_filter( 'generate_show_entry_header', 'rpt_single_post_show_entry_header' );

/**
 * Force full-width layout on single posts.
 *
 * @param string $layout Sidebar layout slug.
 * @return string
 */
function rpt_single_post_sidebar_layout( $layout ) {
	if ( rpt_is_single_news_post() ) {
		return 'no-sidebar';
	}

	return $layout;
}
add_filter( 'generate_sidebar_layout', 'rpt_single_post_sidebar_layout' );

/**
 * Body classes for single posts.
 *
 * @param array $classes Body classes.
 * @return array
 */
function rpt_single_post_body_class( $classes ) {
	if ( ! rpt_is_single_news_post() ) {
		return $classes;
	}

	$classes[] = 'rpt-single-post-body';
	$classes[] = 'rpt-news-page-body';
	$classes[] = 'full-width-content';
	$classes[] = 'no-sidebar';

	$remove = array( 'right-sidebar', 'left-sidebar', 'both-sidebars', 'both-left', 'both-right' );

	return array_values( array_diff( $classes, $remove ) );
}
add_filter( 'body_class', 'rpt_single_post_body_class', 20 );

/**
 * Related posts for single news article.
 *
 * @param int|null $post_id Post ID.
 * @param int      $limit   Number of posts.
 * @return WP_Query
 */
function rpt_get_related_news_posts( $post_id = null, $limit = 8 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$limit   = max( 1, (int) $limit );

	$args = array(
		'post_type'              => 'post',
		'post_status'            => 'publish',
		'posts_per_page'         => $limit,
		'post__not_in'           => array( $post_id ),
		'ignore_sticky_posts'    => true,
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	);

	$categories = wp_get_post_categories( $post_id );

	if ( ! empty( $categories ) ) {
		$args['category__in'] = array_map( 'intval', $categories );
	}

	return new WP_Query( $args );
}
