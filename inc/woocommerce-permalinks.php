<?php
/**
 * Flat WooCommerce permalinks — domain/category-slug and domain/product-slug.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

define( 'RPT_WOO_PERMALINKS_VERSION', '1' );

/**
 * Normalize WooCommerce permalink option to remove product/category bases.
 *
 * @param mixed $value Stored option value.
 * @return array<string, mixed>
 */
function rpt_filter_woocommerce_permalinks_option( $value ) {
	if ( ! is_array( $value ) ) {
		$value = array();
	}

	$value['category_base'] = '';
	$value['product_base']  = '';

	return $value;
}
add_filter( 'option_woocommerce_permalinks', 'rpt_filter_woocommerce_permalinks_option' );

/**
 * Persist empty bases when permalinks are saved in admin.
 *
 * @param mixed $value New option value.
 * @return mixed
 */
function rpt_pre_update_woocommerce_permalinks( $value ) {
	if ( ! is_array( $value ) ) {
		return $value;
	}

	$value['category_base'] = '';
	$value['product_base']  = '';

	return $value;
}
add_filter( 'pre_update_option_woocommerce_permalinks', 'rpt_pre_update_woocommerce_permalinks' );

/**
 * Register product rewrite rules at site root.
 *
 * @param array<string, mixed> $args Product post type args.
 * @return array<string, mixed>
 */
function rpt_flat_product_post_type_rewrite( $args ) {
	$args['rewrite'] = array(
		'slug'       => '.',
		'with_front' => false,
		'feeds'      => true,
	);

	return $args;
}
add_filter( 'woocommerce_register_post_type_product', 'rpt_flat_product_post_type_rewrite', 99 );

/**
 * Register product category rewrite rules at site root.
 *
 * @param array<string, mixed> $args Taxonomy args.
 * @return array<string, mixed>
 */
function rpt_flat_product_cat_taxonomy_rewrite( $args ) {
	$args['rewrite'] = array(
		'slug'         => '.',
		'with_front'   => false,
		'hierarchical' => true,
	);

	return $args;
}
add_filter( 'woocommerce_taxonomy_args_product_cat', 'rpt_flat_product_cat_taxonomy_rewrite', 99 );

/**
 * Build hierarchical product category path segments.
 *
 * @param WP_Term $term Category term.
 * @return array<int, string>
 */
function rpt_get_product_cat_path_slugs( WP_Term $term ) {
	$slugs     = array( $term->slug );
	$parent_id = (int) $term->parent;

	while ( $parent_id ) {
		$parent = get_term( $parent_id, 'product_cat' );

		if ( ! $parent instanceof WP_Term ) {
			break;
		}

		array_unshift( $slugs, $parent->slug );
		$parent_id = (int) $parent->parent;
	}

	return $slugs;
}

/**
 * Resolve a product category from a URL path.
 *
 * @param string $path URL path without leading/trailing slashes.
 * @return WP_Term|null
 */
function rpt_get_product_cat_by_path( $path ) {
	$path  = trim( (string) $path, '/' );
	$slugs = array_filter( explode( '/', $path ) );

	if ( empty( $slugs ) ) {
		return null;
	}

	$parent_id = 0;
	$term      = null;

	foreach ( $slugs as $slug ) {
		$terms = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'slug'       => $slug,
				'parent'     => $parent_id,
				'hide_empty' => false,
				'number'     => 1,
			)
		);

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return null;
		}

		$term      = $terms[0];
		$parent_id = (int) $term->term_id;
	}

	return $term instanceof WP_Term ? $term : null;
}

/**
 * Product category term links without taxonomy base.
 *
 * @param string  $url      Term URL.
 * @param WP_Term $term     Term object.
 * @param string  $taxonomy Taxonomy slug.
 * @return string
 */
function rpt_flat_product_cat_term_link( $url, $term, $taxonomy ) {
	if ( is_admin() || 'product_cat' !== $taxonomy || ! $term instanceof WP_Term ) {
		return $url;
	}

	$path = implode( '/', rpt_get_product_cat_path_slugs( $term ) );

	return home_url( user_trailingslashit( $path ) );
}
add_filter( 'term_link', 'rpt_flat_product_cat_term_link', 10, 3 );

/**
 * Product links without post type base.
 *
 * @param string  $permalink Post URL.
 * @param WP_Post $post      Post object.
 * @return string
 */
function rpt_flat_product_post_type_link( $permalink, $post ) {
	if ( is_admin() || ! $post instanceof WP_Post || 'product' !== $post->post_type ) {
		return $permalink;
	}

	if ( in_array( $post->post_status, array( 'draft', 'pending', 'auto-draft', 'future' ), true ) ) {
		return $permalink;
	}

	return home_url( user_trailingslashit( $post->post_name ) );
}
add_filter( 'post_type_link', 'rpt_flat_product_post_type_link', 10, 2 );

/**
 * Resolve root-level slug conflicts between pages, categories, and products.
 *
 * @param array<string, mixed> $query_vars Query vars.
 * @return array<string, mixed>
 */
function rpt_flat_woocommerce_parse_request( $query_vars ) {
	if ( is_admin() || ! empty( $query_vars['product_cat'] ) || ! empty( $query_vars['product'] ) ) {
		return $query_vars;
	}

	$path = '';

	if ( ! empty( $query_vars['pagename'] ) ) {
		$path = (string) $query_vars['pagename'];
	} elseif ( ! empty( $query_vars['name'] ) && empty( $query_vars['post_type'] ) ) {
		$path = (string) $query_vars['name'];
	} elseif ( ! empty( $query_vars['name'] ) && ! empty( $query_vars['post_type'] ) && 'product' === $query_vars['post_type'] ) {
		return $query_vars;
	}

	$path = trim( $path, '/' );

	if ( '' === $path ) {
		return $query_vars;
	}

	$page = get_page_by_path( $path );

	if ( $page instanceof WP_Post ) {
		return $query_vars;
	}

	$term = rpt_get_product_cat_by_path( $path );

	if ( $term instanceof WP_Term ) {
		unset( $query_vars['pagename'], $query_vars['name'], $query_vars['attachment'] );
		$query_vars['product_cat'] = $term->slug;

		return $query_vars;
	}

	$product = get_page_by_path( $path, OBJECT, 'product' );

	if ( $product instanceof WP_Post ) {
		unset( $query_vars['pagename'] );
		$query_vars['post_type'] = 'product';
		$query_vars['name']      = $product->post_name;
		$query_vars['product']   = $product->post_name;
	}

	return $query_vars;
}
add_filter( 'request', 'rpt_flat_woocommerce_parse_request', 11 );

/**
 * Flush rewrite rules after permalink module update.
 */
function rpt_maybe_flush_flat_woocommerce_permalinks() {
	if ( get_option( 'rpt_woo_permalinks_version' ) === RPT_WOO_PERMALINKS_VERSION ) {
		return;
	}

	$permalinks = (array) get_option( 'woocommerce_permalinks', array() );

	$permalinks['category_base'] = '';
	$permalinks['product_base']  = '';

	update_option( 'woocommerce_permalinks', $permalinks );
	flush_rewrite_rules( false );
	update_option( 'rpt_woo_permalinks_version', RPT_WOO_PERMALINKS_VERSION );
}
add_action( 'init', 'rpt_maybe_flush_flat_woocommerce_permalinks', 99 );

/**
 * Flush rewrite rules on theme switch.
 */
function rpt_flush_flat_woocommerce_permalinks_on_switch() {
	delete_option( 'rpt_woo_permalinks_version' );
	rpt_maybe_flush_flat_woocommerce_permalinks();
}
add_action( 'after_switch_theme', 'rpt_flush_flat_woocommerce_permalinks_on_switch' );
