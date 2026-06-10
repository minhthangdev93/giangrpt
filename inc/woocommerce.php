<?php
/**
 * WooCommerce customizations — catalog mode, product hub, inquiry CTAs.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Adjust shop loop hooks for RPT product card layout.
 */
function rpt_woocommerce_loop_setup() {
	remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
	remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
}
add_action( 'woocommerce_init', 'rpt_woocommerce_loop_setup' );

/**
 * Add rpt-card classes to product loop items.
 *
 * @param array      $classes CSS classes.
 * @param WC_Product $product Product object.
 * @return array
 */
function rpt_wc_product_post_class( $classes, $product ) {
	$classes[] = 'rpt-card';
	$classes[] = 'rpt-product-card';

	return $classes;
}
add_filter( 'woocommerce_post_class', 'rpt_wc_product_post_class', 10, 2 );

/**
 * Whether the main shop page uses the B2B product hub layout.
 *
 * @return bool
 */
function rpt_is_product_hub() {
	return function_exists( 'is_shop' ) && is_shop() && ! is_search() && ! is_product_taxonomy();
}

/**
 * Back-compat alias.
 *
 * @return bool
 */
function rpt_is_custom_shop_catalog() {
	return rpt_is_product_hub();
}

/**
 * Product category archive with custom B2B layout.
 *
 * @return bool
 */
function rpt_is_product_category_archive() {
	return function_exists( 'is_product_category' ) && is_product_category() && ! is_search();
}

/**
 * Shop hub or category archive — custom catalog layout.
 *
 * @return bool
 */
function rpt_uses_custom_catalog_layout() {
	return rpt_is_product_hub() || rpt_is_product_category_archive();
}

/**
 * Single product detail page.
 *
 * @return bool
 */
function rpt_is_single_product_page() {
	return function_exists( 'is_product' ) && is_product();
}

/**
 * WooCommerce pages without theme sidebar (full-width content).
 *
 * @return bool
 */
function rpt_uses_full_width_woo_layout() {
	return rpt_uses_custom_catalog_layout() || rpt_is_single_product_page();
}

/**
 * Back-compat alias.
 *
 * @return bool
 */
function rpt_is_full_width_woo_archive() {
	return rpt_uses_full_width_woo_layout();
}

/**
 * All top-level categories for the product hub sidebar.
 *
 * @return array<int, WP_Term>
 */
function rpt_get_shop_sidebar_categories() {
	return rpt_get_top_level_product_categories(
		array(
			'hide_empty'      => false,
			'exclude_default' => true,
			'orderby'         => 'menu_order',
			'order'           => 'ASC',
		)
	);
}

/**
 * Published product count for a category (includes child categories).
 *
 * @param WP_Term $term Category term.
 * @return int
 */
function rpt_get_shop_category_product_count( $term ) {
	if ( ! $term instanceof WP_Term ) {
		return 0;
	}

	$query = new WP_Query(
		array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => false,
			'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy'         => 'product_cat',
					'field'            => 'term_id',
					'terms'            => array( (int) $term->term_id ),
					'include_children' => true,
				),
			),
		)
	);

	return (int) $query->found_posts;
}

/**
 * Products for a hub category section.
 *
 * @param WP_Term $term  Category term.
 * @param int     $limit Max products.
 * @return array<int, WC_Product>
 */
function rpt_get_shop_category_products( $term, $limit = 8 ) {
	if ( ! $term instanceof WP_Term || ! function_exists( 'wc_get_product' ) ) {
		return array();
	}

	/**
	 * Filter products per category on the product hub.
	 *
	 * @param int     $limit Product limit.
	 * @param WP_Term $term  Category term.
	 */
	$limit = (int) apply_filters( 'rpt_shop_category_products_limit', $limit, $term );
	$limit = min( 8, max( 1, $limit ) );

	$query = new WP_Query(
		array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
			),
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy'         => 'product_cat',
					'field'            => 'term_id',
					'terms'            => array( (int) $term->term_id ),
					'include_children' => true,
				),
			),
		)
	);

	if ( empty( $query->posts ) ) {
		return array();
	}

	$products = array();

	foreach ( $query->posts as $product_id ) {
		$product = wc_get_product( $product_id );

		if ( $product instanceof WC_Product && $product->is_visible() ) {
			$products[] = $product;
		}
	}

	return $products;
}

/**
 * WooCommerce product category archive URL.
 *
 * @param WP_Term $term Category term.
 * @return string
 */
function rpt_get_product_category_link( $term ) {
	if ( ! $term instanceof WP_Term ) {
		return '#';
	}

	$link = get_term_link( $term );

	return is_wp_error( $link ) ? '#' : $link;
}

/**
 * Whether a sidebar category item should show as active.
 *
 * @param WP_Term $term Category term.
 * @return bool
 */
function rpt_is_shop_sidebar_category_active( $term ) {
	if ( ! $term instanceof WP_Term || rpt_is_product_hub() ) {
		return false;
	}

	if ( ! function_exists( 'is_product_category' ) || ! is_product_category() ) {
		return false;
	}

	$queried = get_queried_object();

	if ( ! ( $queried instanceof WP_Term && 'product_cat' === $queried->taxonomy ) ) {
		return false;
	}

	return (int) $queried->term_id === (int) $term->term_id;
}

/**
 * Contact rows for the product hub sidebar card.
 *
 * @return array<int, array<string, string>>
 */
function rpt_get_product_hub_contact_rows() {
	$keys  = array( 'email', 'phone', 'whatsapp', 'skype' );
	$rows  = array();
	$map   = array(
		'email'    => __( 'E-mail', 'generatepress_child' ),
		'phone'    => __( 'Điện thoại', 'generatepress_child' ),
		'whatsapp' => __( 'WhatsApp', 'generatepress_child' ),
		'skype'    => __( 'Skype', 'generatepress_child' ),
	);

	foreach ( $keys as $key ) {
		$value = rpt_get_site_info( $key );

		if ( ! $value ) {
			continue;
		}

		$rows[] = array(
			'key'   => $key,
			'label' => $map[ $key ],
			'value' => $value,
			'url'   => rpt_get_site_info_link( $key ),
		);
	}

	/**
	 * Filter product hub sidebar contact rows.
	 *
	 * @param array<int, array<string, string>> $rows Contact rows.
	 */
	return apply_filters( 'rpt_product_hub_contact_rows', $rows );
}

/**
 * Social icon links for the product hub contact card.
 *
 * @return array<int, array<string, string>>
 */
function rpt_get_product_hub_social_links() {
	$channels = array(
		array(
			'key'   => 'email',
			'label' => __( 'E-mail', 'generatepress_child' ),
			'url'   => rpt_get_site_info_link( 'email' ),
		),
		array(
			'key'   => 'phone',
			'label' => __( 'Điện thoại', 'generatepress_child' ),
			'url'   => rpt_get_site_info_link( 'phone' ),
		),
		array(
			'key'   => 'whatsapp',
			'label' => __( 'WhatsApp', 'generatepress_child' ),
			'url'   => rpt_get_site_info_link( 'whatsapp' ),
		),
		array(
			'key'   => 'skype',
			'label' => __( 'Skype', 'generatepress_child' ),
			'url'   => rpt_get_site_info_link( 'skype' ),
		),
	);

	return array_values(
		array_filter(
			$channels,
			static function ( $channel ) {
				return ! empty( $channel['url'] );
			}
		)
	);
}

/**
 * Inline SVG icon for product hub social links.
 *
 * @param string $key Channel key.
 * @return string
 */
function rpt_get_product_hub_social_icon_svg( $key ) {
	switch ( $key ) {
		case 'email':
			return '<svg class="rpt-product-hub-social__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6h16v12H4V6Z" stroke="currentColor" stroke-width="1.8"/><path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="1.8"/></svg>';
		case 'phone':
			return '<svg class="rpt-product-hub-social__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6.5 4h3l1.5 4-2.2 1.4a13 13 0 0 0 5.8 5.8L15 13l4 1.5v3A2.5 2.5 0 0 1 16.5 20 14.5 14.5 0 0 1 4 7.5 2.5 2.5 0 0 1 6.5 4Z" stroke="currentColor" stroke-width="1.8"/></svg>';
		case 'whatsapp':
			return '<svg class="rpt-product-hub-social__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3a9 9 0 0 0-7.8 13.5L3 21l4.6-1.2A9 9 0 1 0 12 3Z" stroke="currentColor" stroke-width="1.8"/><path d="M9.2 9.4c.2-.5.4-.5.7-.5h.6c.2 0 .4 0 .5.4l.8 1.9c.1.2.1.4 0 .6l-.5.6c-.1.2-.1.3 0 .5.4.8 1.1 1.5 1.9 1.9.2.1.3.1.5 0l.6-.5c.2-.1.4-.1.6 0l1.9.8c.4.2.4.4.4.6v.6c0 .3 0 .5-.5.7-1 .5-2.2.4-3.8-.4-1.8-1-3.5-2.7-4.5-4.5-.8-1.6-.9-2.8-.4-3.8Z" fill="currentColor"/></svg>';
		case 'skype':
			return '<svg class="rpt-product-hub-social__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.8"/><path d="M8.5 10.8c0-1.6 1.2-2.4 2.8-2.4 1.2 0 2 .4 2 1.4 0 .8-.5 1.1-1.5 1.4-1.2.4-2.5.8-2.5 2.3 0 1.3 1.1 2.1 2.7 2.1 1.5 0 2.6-.5 3.1-1.3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>';
		default:
			return '';
	}
}

/**
 * Render pagination for product category archives.
 */
function rpt_render_category_archive_pagination() {
	global $wp_query;

	$total = isset( $wp_query->max_num_pages ) ? (int) $wp_query->max_num_pages : 0;

	if ( $total <= 1 ) {
		return;
	}

	$current = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );

	echo '<nav class="rpt-category-pagination__nav" aria-label="' . esc_attr__( 'Phân trang sản phẩm', 'generatepress_child' ) . '">';
	echo '<ul class="rpt-category-pagination__list">';

	for ( $page = 1; $page <= $total; $page++ ) {
		$is_current = ( $page === $current );
		$class      = $is_current ? ' is-current' : '';

		printf(
			'<li><a class="rpt-category-pagination__page%1$s" href="%2$s"%3$s>%4$d</a></li>',
			esc_attr( $class ),
			esc_url( get_pagenum_link( $page ) ),
			$is_current ? ' aria-current="page"' : '',
			(int) $page
		);
	}

	if ( $current < $total ) {
		printf(
			'<li><a class="rpt-category-pagination__next" href="%1$s">%2$s</a></li>',
			esc_url( get_pagenum_link( $current + 1 ) ),
			esc_html__( 'Tiếp theo', 'generatepress_child' )
		);
	}

	echo '</ul>';
	echo '</nav>';
}

/**
 * Products per page on category archives.
 *
 * @param int $per_page Products per page.
 * @return int
 */
function rpt_category_archive_products_per_page( $per_page ) {
	if ( rpt_is_product_category_archive() ) {
		return 12;
	}

	return $per_page;
}
add_filter( 'loop_shop_per_page', 'rpt_category_archive_products_per_page', 20 );

/**
 * Configure hooks for custom catalog layouts.
 */
function rpt_setup_shop_catalog_layout() {
	if ( rpt_is_product_hub() ) {
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
		remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
	}

	if ( rpt_is_product_category_archive() ) {
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
		remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
		remove_action( 'woocommerce_shop_loop_header', 'woocommerce_product_taxonomy_archive_header', 10 );
	}

	if ( ! rpt_uses_full_width_woo_layout() ) {
		return;
	}

	remove_action( 'woocommerce_sidebar', 'generate_construct_sidebars', 10 );
	remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
}
add_action( 'wp', 'rpt_setup_shop_catalog_layout' );

/**
 * Body class for product hub layout.
 *
 * @param array $classes Body classes.
 * @return array
 */
function rpt_shop_catalog_body_class( $classes ) {
	if ( rpt_is_product_hub() ) {
		$classes[] = 'rpt-product-hub-page';
		$classes[] = 'rpt-shop-catalog-page';
	}

	if ( rpt_is_product_category_archive() ) {
		$classes[] = 'rpt-product-category-page';
		$classes[] = 'rpt-product-category-archive';
	}

	if ( rpt_is_single_product_page() ) {
		$classes[] = 'rpt-single-product-page';
	}

	if ( ! rpt_uses_full_width_woo_layout() ) {
		return $classes;
	}

	$classes[] = 'full-width-content';
	$classes[] = 'no-sidebar';

	$remove = array( 'right-sidebar', 'left-sidebar', 'both-sidebars', 'both-left', 'both-right' );

	return array_values( array_diff( $classes, $remove ) );
}
add_filter( 'body_class', 'rpt_shop_catalog_body_class' );
