<?php
/**
 * Footer helpers — menus, categories, fallbacks.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Product category term IDs excluded from public lists (e.g. default "Uncategorized").
 *
 * @return array<int>
 */
function rpt_get_excluded_product_category_ids() {
	$exclude = array();

	$default_cat_id = (int) get_option( 'default_product_cat', 0 );
	if ( $default_cat_id > 0 ) {
		$exclude[] = $default_cat_id;
	}

	/**
	 * Filter excluded WooCommerce product category IDs.
	 *
	 * @param array<int> $exclude Term IDs to exclude.
	 */
	return array_values( array_unique( array_map( 'intval', apply_filters( 'rpt_excluded_product_category_ids', $exclude ) ) ) );
}

/**
 * Top-level WooCommerce product categories.
 *
 * @param array<string, mixed> $args {
 *     Optional query arguments.
 *
 *     @type int    $limit            Max categories. 0 = all.
 *     @type bool   $hide_empty       Hide categories without products.
 *     @type bool   $exclude_default  Exclude default WooCommerce category.
 *     @type string $orderby          Term order field. Use `menu_order` for admin drag order.
 * }
 * @return array<int, WP_Term>
 */
function rpt_get_top_level_product_categories( $args = array() ) {
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		return array();
	}

	$defaults = array(
		'limit'            => 0,
		'hide_empty'       => (bool) apply_filters( 'rpt_product_categories_hide_empty', true ),
		'exclude_default'  => false,
		'orderby'          => 'menu_order',
		'order'            => 'ASC',
	);

	$args = wp_parse_args( $args, $defaults );

	$query = array(
		'taxonomy'   => 'product_cat',
		'parent'     => 0,
		'hide_empty' => (bool) $args['hide_empty'],
		'orderby'    => $args['orderby'],
		'order'      => $args['order'],
	);

	if ( (int) $args['limit'] > 0 ) {
		$query['number'] = (int) $args['limit'];
	}

	if ( ! empty( $args['exclude_default'] ) ) {
		$exclude = rpt_get_excluded_product_category_ids();
		if ( ! empty( $exclude ) ) {
			$query['exclude'] = $exclude;
		}
	}

	$terms = get_terms( $query );

	if ( is_wp_error( $terms ) || ! is_array( $terms ) ) {
		return array();
	}

	return $terms;
}

/**
 * Top-level WooCommerce product categories for footer.
 *
 * @param int $limit Max categories to return. 0 = all.
 * @return array<int, WP_Term>
 */
function rpt_get_footer_product_categories( $limit = 0 ) {
	/**
	 * Filter footer product category count.
	 *
	 * @param int $limit Default category limit.
	 */
	$limit = (int) apply_filters( 'rpt_footer_product_categories_limit', $limit );

	/**
	 * Filter whether empty categories are hidden in the footer list.
	 *
	 * @param bool $hide_empty Hide categories without products.
	 */
	$hide_empty = (bool) apply_filters( 'rpt_footer_product_categories_hide_empty', false );

	return rpt_get_top_level_product_categories(
		array(
			'limit'           => $limit,
			'hide_empty'      => $hide_empty,
			'exclude_default' => true,
			'orderby'         => 'menu_order',
			'order'           => 'ASC',
		)
	);
}

/**
 * Fallback quick links when no footer menu is assigned.
 *
 * @return array<int, array<string, string>>
 */
function rpt_get_footer_quick_links_fallback_items() {
	$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
	$privacy  = get_privacy_policy_url();

	$items = array(
		array(
			'label' => __( 'Nhà', 'generatepress_child' ),
			'url'   => home_url( '/' ),
		),
		array(
			'label' => __( 'Về chúng tôi', 'generatepress_child' ),
			'url'   => rpt_get_page_url_by_slug( 'about-us', home_url( '/about-us/' ) ),
		),
		array(
			'label' => __( 'Các sản phẩm', 'generatepress_child' ),
			'url'   => $shop_url,
		),
		array(
			'label' => __( 'Liên hệ với chúng tôi', 'generatepress_child' ),
			'url'   => rpt_get_contact_url(),
		),
		array(
			'label' => __( 'Chính sách bảo mật', 'generatepress_child' ),
			'url'   => $privacy ? $privacy : home_url( '/privacy-policy/' ),
		),
		array(
			'label' => __( 'Sơ đồ trang web', 'generatepress_child' ),
			'url'   => rpt_get_page_url_by_slug( 'sitemap', home_url( '/sitemap/' ) ),
		),
	);

	/**
	 * Filter fallback footer quick links.
	 *
	 * @param array<int, array<string, string>> $items Link items.
	 */
	return apply_filters( 'rpt_footer_quick_links_fallback', $items );
}

/**
 * Resolve page URL by slug with fallback.
 *
 * @param string $slug     Page slug.
 * @param string $fallback Fallback URL.
 * @return string
 */
function rpt_get_page_url_by_slug( $slug, $fallback ) {
	$page = get_page_by_path( $slug );

	return $page ? get_permalink( $page ) : $fallback;
}

/**
 * wp_nav_menu fallback for footer quick links.
 *
 * @return void
 */
function rpt_footer_quick_links_fallback() {
	$items = rpt_get_footer_quick_links_fallback_items();

	echo '<ul class="rpt-footer-links">';
	foreach ( $items as $item ) {
		printf(
			'<li><a href="%1$s">%2$s</a></li>',
			esc_url( $item['url'] ),
			esc_html( $item['label'] )
		);
	}
	echo '</ul>';
}

/**
 * SVG icon for a footer contact row.
 *
 * @param string $key Contact row key.
 * @return string
 */
function rpt_get_footer_contact_icon( $key ) {
	$icons = array(
		'address'       => '<svg class="rpt-footer-contact__icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 21s7-4.5 7-11a7 7 0 1 0-14 0c0 6.5 7 11 7 11Z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="10" r="2.5" stroke="currentColor" stroke-width="1.8"/></svg>',
		'phone'         => '<svg class="rpt-footer-contact__icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6.5 4h3l1.5 4-2.2 1.4a13 13 0 0 0 5.8 5.8L15 13l4 1.5v3A2.5 2.5 0 0 1 16.5 20 14.5 14.5 0 0 1 4 7.5 2.5 2.5 0 0 1 6.5 4Z" stroke="currentColor" stroke-width="1.8"/></svg>',
		'fax'           => '<svg class="rpt-footer-contact__icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="4" y="8" width="16" height="10" rx="1.5" stroke="currentColor" stroke-width="1.8"/><path d="M7 8V6a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v2M8 14h2M12 14h2M16 14h2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
		'email'         => '<svg class="rpt-footer-contact__icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6h16v12H4V6Z" stroke="currentColor" stroke-width="1.8"/><path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="1.8"/></svg>',
		'working_hours' => '<svg class="rpt-footer-contact__icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.8"/><path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
	);

	if ( ! isset( $icons[ $key ] ) ) {
		return '';
	}

	return $icons[ $key ];
}

/**
 * Dynamic copyright line for footer.
 *
 * @return string
 */
function rpt_get_footer_copyright() {
	return sprintf(
		/* translators: 1: current year, 2: company name */
		__( 'Copyright © %1$s %2$s. Đã đăng ký Bản quyền.', 'generatepress_child' ),
		gmdate( 'Y' ),
		rpt_get_site_info( 'company_name' )
	);
}

/**
 * Inline CSS for footer background image.
 */
function rpt_footer_inline_styles() {
	$bg_url = rpt_get_footer_background_url();

	if ( ! $bg_url ) {
		return;
	}

	$css = sprintf(
		'.rpt-site-footer { --rpt-footer-bg-image: url(%s); }',
		esc_url( $bg_url )
	);

	wp_add_inline_style( 'rpt-layout-footer', $css );
}
add_action( 'wp_enqueue_scripts', 'rpt_footer_inline_styles', 30 );
