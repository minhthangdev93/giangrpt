<?php
/**
 * Flat WooCommerce permalinks — domain/category-slug and domain/product-slug.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

define( 'RPT_WOO_PERMALINKS_VERSION', '2' );

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
 * Extract the front-end path from main query vars.
 *
 * @param array<string, mixed> $query_vars Query vars.
 * @return string
 */
function rpt_flat_woocommerce_get_request_path( $query_vars ) {
	if ( ! empty( $query_vars['pagename'] ) ) {
		return trim( (string) $query_vars['pagename'], '/' );
	}

	if ( ! empty( $query_vars['attachment'] ) ) {
		return trim( (string) $query_vars['attachment'], '/' );
	}

	if ( ! empty( $query_vars['name'] ) ) {
		$post_type = isset( $query_vars['post_type'] ) ? (string) $query_vars['post_type'] : '';

		if ( '' === $post_type || 'attachment' === $post_type ) {
			return trim( (string) $query_vars['name'], '/' );
		}
	}

	return '';
}

/**
 * Clear query vars that would resolve to a page, attachment, or single post.
 *
 * @param array<string, mixed> $query_vars Query vars.
 * @return array<string, mixed>
 */
function rpt_flat_woocommerce_clear_content_query_vars( $query_vars ) {
	unset(
		$query_vars['pagename'],
		$query_vars['name'],
		$query_vars['attachment'],
		$query_vars['attachment_id'],
		$query_vars['post_type'],
		$query_vars['p'],
		$query_vars['page_id']
	);

	return $query_vars;
}

/**
 * Apply product category query vars for flat permalinks.
 *
 * @param array<string, mixed> $query_vars Query vars.
 * @param WP_Term              $term       Category term.
 * @return array<string, mixed>
 */
function rpt_flat_woocommerce_apply_product_cat_query_vars( $query_vars, WP_Term $term ) {
	$query_vars = rpt_flat_woocommerce_clear_content_query_vars( $query_vars );

	$cat_path = implode( '/', rpt_get_product_cat_path_slugs( $term ) );

	$query_vars['taxonomy']    = 'product_cat';
	$query_vars['term']        = $term->slug;
	$query_vars['product_cat'] = $cat_path;

	return $query_vars;
}

/**
 * Resolve a flat front-end path to a product category when possible.
 *
 * @param string $path URL path without leading/trailing slashes.
 * @return WP_Term|null
 */
function rpt_flat_woocommerce_resolve_product_cat_path( $path ) {
	$path = trim( (string) $path, '/' );

	if ( '' === $path || get_page_by_path( $path ) instanceof WP_Post ) {
		return null;
	}

	$term = rpt_get_product_cat_by_path( $path );

	return $term instanceof WP_Term ? $term : null;
}

/**
 * Resolve root-level slug conflicts between pages, categories, products, and attachments.
 *
 * Category thumbnails often share the same slug as their product_cat term; without this,
 * flat permalinks can resolve to the attachment and redirect to the raw uploads image URL.
 *
 * @param array<string, mixed> $query_vars Query vars.
 * @return array<string, mixed>
 */
function rpt_flat_woocommerce_parse_request( $query_vars ) {
	if ( is_admin() || ! empty( $query_vars['product_cat'] ) || ! empty( $query_vars['product'] ) ) {
		return $query_vars;
	}

	if ( ! empty( $query_vars['name'] ) && ! empty( $query_vars['post_type'] ) && 'product' === $query_vars['post_type'] ) {
		return $query_vars;
	}

	$path = rpt_flat_woocommerce_get_request_path( $query_vars );

	if ( '' === $path ) {
		return $query_vars;
	}

	$term = rpt_flat_woocommerce_resolve_product_cat_path( $path );

	if ( $term instanceof WP_Term ) {
		return rpt_flat_woocommerce_apply_product_cat_query_vars( $query_vars, $term );
	}

	$product = get_page_by_path( $path, OBJECT, 'product' );

	if ( $product instanceof WP_Post ) {
		$query_vars = rpt_flat_woocommerce_clear_content_query_vars( $query_vars );
		$query_vars['post_type'] = 'product';
		$query_vars['name']      = $product->post_name;
		$query_vars['product']   = $product->post_name;
	}

	return $query_vars;
}
add_filter( 'request', 'rpt_flat_woocommerce_parse_request', 11 );

/**
 * Re-map attachment/single-slug queries to product_cat before the main query runs.
 *
 * Redirecting attachment -> category URL caused infinite loops because both share the same flat slug.
 *
 * @param WP_Query $query Main query.
 */
function rpt_flat_woocommerce_pre_get_posts( $query ) {
	if ( is_admin() || ! $query->is_main_query() || $query->get( 'product_cat' ) ) {
		return;
	}

	$path = '';

	if ( $query->get( 'pagename' ) ) {
		$path = (string) $query->get( 'pagename' );
	} elseif ( $query->get( 'attachment' ) ) {
		$path = (string) $query->get( 'attachment' );
	} elseif ( $query->get( 'name' ) ) {
		$post_type = (string) $query->get( 'post_type' );

		if ( '' === $post_type || 'attachment' === $post_type ) {
			$path = (string) $query->get( 'name' );
		}
	}

	$term = rpt_flat_woocommerce_resolve_product_cat_path( $path );

	if ( ! $term instanceof WP_Term ) {
		return;
	}

	$cat_path = implode( '/', rpt_get_product_cat_path_slugs( $term ) );

	$query->set( 'attachment', '' );
	$query->set( 'attachment_id', 0 );
	$query->set( 'name', '' );
	$query->set( 'pagename', '' );
	$query->set( 'post_type', '' );
	$query->set( 'p', 0 );
	$query->set( 'page_id', 0 );
	$query->set( 'taxonomy', 'product_cat' );
	$query->set( 'term', $term->slug );
	$query->set( 'product_cat', $cat_path );

	$query->is_attachment = false;
	$query->is_singular   = false;
	$query->is_single     = false;
	$query->is_page       = false;
	$query->is_archive    = true;
	$query->is_tax        = true;
}
add_action( 'pre_get_posts', 'rpt_flat_woocommerce_pre_get_posts', 5 );

/**
 * Prevent canonical redirects from breaking flat product category URLs.
 *
 * @param string $redirect_url  Target URL.
 * @param string $requested_url Current URL.
 * @return string|false
 */
function rpt_flat_woocommerce_redirect_canonical( $redirect_url, $requested_url ) {
	if ( is_admin() || ! $redirect_url ) {
		return $redirect_url;
	}

	$path = wp_parse_url( $requested_url, PHP_URL_PATH );

	if ( ! is_string( $path ) || '' === trim( $path, '/' ) ) {
		return $redirect_url;
	}

	$term = rpt_flat_woocommerce_resolve_product_cat_path( trim( $path, '/' ) );

	if ( ! $term instanceof WP_Term ) {
		return $redirect_url;
	}

	if ( preg_match( '#/wp-content/uploads/.+\.(jpe?g|png|webp|gif|avif)(?:\?.*)?$#i', $redirect_url ) ) {
		return false;
	}

	if ( untrailingslashit( $redirect_url ) === untrailingslashit( $requested_url ) ) {
		return false;
	}

	return $redirect_url;
}
add_filter( 'redirect_canonical', 'rpt_flat_woocommerce_redirect_canonical', 10, 2 );

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
