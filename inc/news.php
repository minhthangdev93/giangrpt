<?php
/**
 * News archive helpers and layout detection.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

define( 'RPT_NEWS_PAGE_TEMPLATE', 'page-templates/template-news.php' );
define( 'RPT_NEWS_POSTS_PER_PAGE', 9 );

/**
 * Whether the current view uses the news layout.
 *
 * @return bool
 */
function rpt_is_news_page() {
	if ( is_page_template( RPT_NEWS_PAGE_TEMPLATE ) ) {
		return true;
	}

	return is_home() && ! is_front_page();
}

/**
 * News page title.
 *
 * @return string
 */
function rpt_get_news_page_title() {
	if ( is_page_template( RPT_NEWS_PAGE_TEMPLATE ) ) {
		$title = get_the_title();

		if ( is_string( $title ) && '' !== trim( $title ) ) {
			return trim( $title );
		}
	}

	$posts_page_id = (int) get_option( 'page_for_posts' );

	if ( $posts_page_id ) {
		$title = get_the_title( $posts_page_id );

		if ( is_string( $title ) && '' !== trim( $title ) ) {
			return trim( $title );
		}
	}

	return __( 'Tin tức', 'generatepress_child' );
}

/**
 * Current pagination page for news listings.
 *
 * @return int
 */
function rpt_get_news_paged() {
	return max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
}

/**
 * Query news posts for the page template.
 *
 * @return WP_Query
 */
function rpt_get_news_page_query() {
	return new WP_Query(
		array(
			'post_type'              => 'post',
			'post_status'            => 'publish',
			'posts_per_page'         => RPT_NEWS_POSTS_PER_PAGE,
			'paged'                  => rpt_get_news_paged(),
			'ignore_sticky_posts'    => true,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => false,
		)
	);
}

/**
 * Formatted publish date for news cards (dd/mm/yyyy).
 *
 * @param int|null $post_id Post ID.
 * @return string
 */
function rpt_get_news_date_display( $post_id = null ) {
	$post_id   = $post_id ? (int) $post_id : get_the_ID();
	$timestamp = get_post_timestamp( $post_id );

	if ( ! $timestamp ) {
		return '';
	}

	return wp_date( 'd/m/Y', $timestamp );
}

/**
 * Day and month label for desktop card date badge.
 *
 * @param int|null $post_id Post ID.
 * @return array{day: string, month: string}
 */
function rpt_get_news_date_parts( $post_id = null ) {
	$post_id   = $post_id ? (int) $post_id : get_the_ID();
	$timestamp = get_post_timestamp( $post_id );

	if ( ! $timestamp ) {
		return array(
			'day'   => '',
			'month' => '',
		);
	}

	$months = array(
		1  => 'Th1',
		2  => 'Th2',
		3  => 'Th3',
		4  => 'Th4',
		5  => 'Th5',
		6  => 'Th6',
		7  => 'Th7',
		8  => 'Th8',
		9  => 'Th9',
		10 => 'Th10',
		11 => 'Th11',
		12 => 'Th12',
	);

	$month_num = (int) wp_date( 'n', $timestamp );

	return array(
		'day'   => wp_date( 'j', $timestamp ),
		'month' => isset( $months[ $month_num ] ) ? $months[ $month_num ] : wp_date( 'M', $timestamp ),
	);
}

/**
 * Render news breadcrumb.
 */
function rpt_render_news_breadcrumb() {
	rpt_render_breadcrumb(
		array(
			array(
				'label' => __( 'Trang chủ', 'generatepress_child' ),
				'url'   => home_url( '/' ),
			),
			array(
				'label' => rpt_get_news_page_title(),
			),
		)
	);
}

/**
 * Render pagination for news listings.
 *
 * @param WP_Query|null $query News query.
 */
function rpt_render_news_pagination( $query = null ) {
	if ( ! $query instanceof WP_Query ) {
		global $wp_query;
		$query = $wp_query;
	}

	$total = (int) $query->max_num_pages;

	if ( $total <= 1 ) {
		return;
	}

	$current = rpt_get_news_paged();

	echo '<nav class="rpt-news-pagination" aria-label="' . esc_attr__( 'Phân trang tin tức', 'generatepress_child' ) . '">';
	echo '<ul class="rpt-news-pagination__list">';

	for ( $page = 1; $page <= $total; $page++ ) {
		$is_current = ( $page === $current );
		$class      = $is_current ? ' is-current' : '';

		printf(
			'<li><a class="rpt-news-pagination__page%1$s" href="%2$s"%3$s>%4$d</a></li>',
			esc_attr( $class ),
			esc_url( rpt_get_news_pagination_url( $page ) ),
			$is_current ? ' aria-current="page"' : '',
			(int) $page
		);
	}

	if ( $current < $total ) {
		printf(
			'<li><a class="rpt-news-pagination__next" href="%1$s">%2$s</a></li>',
			esc_url( rpt_get_news_pagination_url( $current + 1 ) ),
			esc_html__( 'Tiếp theo', 'generatepress_child' )
		);
	}

	echo '</ul>';
	echo '</nav>';
}

/**
 * Pagination URL for news listings.
 *
 * @param int $page Page number.
 * @return string
 */
function rpt_get_news_pagination_url( $page ) {
	if ( is_page_template( RPT_NEWS_PAGE_TEMPLATE ) ) {
		return 1 === (int) $page ? get_permalink() : trailingslashit( get_permalink() ) . user_trailingslashit( 'page/' . (int) $page );
	}

	return get_pagenum_link( $page );
}

/**
 * Nine posts per page on blog index.
 *
 * @param WP_Query $query Main query.
 */
function rpt_news_pre_get_posts( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( $query->is_home() ) {
		$query->set( 'posts_per_page', RPT_NEWS_POSTS_PER_PAGE );
	}
}
add_action( 'pre_get_posts', 'rpt_news_pre_get_posts' );

/**
 * Force full-width layout on news page.
 *
 * @param string $layout Sidebar layout slug.
 * @return string
 */
function rpt_news_sidebar_layout( $layout ) {
	if ( rpt_is_news_page() ) {
		return 'no-sidebar';
	}

	return $layout;
}
add_filter( 'generate_sidebar_layout', 'rpt_news_sidebar_layout' );

/**
 * Body classes for news page.
 *
 * @param array $classes Body classes.
 * @return array
 */
function rpt_news_body_class( $classes ) {
	if ( ! rpt_is_news_page() ) {
		return $classes;
	}

	$classes[] = 'rpt-news-page-body';
	$classes[] = 'full-width-content';
	$classes[] = 'no-sidebar';

	$remove = array( 'right-sidebar', 'left-sidebar', 'both-sidebars', 'both-left', 'both-right' );

	return array_values( array_diff( $classes, $remove ) );
}
add_filter( 'body_class', 'rpt_news_body_class', 20 );
