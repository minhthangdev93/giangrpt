<?php
/**
 * Video hub helpers and layout detection.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Videos page template path.
 */
define( 'RPT_VIDEOS_PAGE_TEMPLATE', 'page-templates/template-videos.php' );

/**
 * Query var for filtering videos by product category slug.
 */
define( 'RPT_VIDEO_CAT_QUERY_VAR', 'product_cat' );

/**
 * Max videos per category section on the hub overview.
 */
define( 'RPT_VIDEOS_PER_SECTION', 8 );

/**
 * Register public query var for video category filter.
 *
 * @param array $vars Query vars.
 * @return array
 */
function rpt_register_video_query_vars( $vars ) {
	$vars[] = RPT_VIDEO_CAT_QUERY_VAR;

	return $vars;
}
add_filter( 'query_vars', 'rpt_register_video_query_vars' );

/**
 * Whether current request uses the video hub layout.
 *
 * @return bool
 */
function rpt_uses_video_layout() {
	return rpt_is_videos_page() || rpt_is_single_video_page();
}

/**
 * Main videos hub page.
 *
 * @return bool
 */
function rpt_is_videos_page() {
	if ( is_page_template( RPT_VIDEOS_PAGE_TEMPLATE ) ) {
		return true;
	}

	return is_page( 'videos' );
}

/**
 * Single video detail page.
 *
 * @return bool
 */
function rpt_is_single_video_page() {
	return is_singular( 'rpt_video' );
}

/**
 * Whether videos hub is filtered by a product category.
 *
 * @return bool
 */
function rpt_is_videos_category_filter() {
	return rpt_get_current_video_category_term() instanceof WP_Term;
}

/**
 * Get videos hub page URL.
 *
 * @return string
 */
function rpt_get_videos_page_url() {
	$pages = get_posts(
		array(
			'post_type'              => 'page',
			'post_status'            => 'publish',
			'posts_per_page'         => 1,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'meta_key'               => '_wp_page_template',
			'meta_value'             => RPT_VIDEOS_PAGE_TEMPLATE,
			'fields'                 => 'ids',
		)
	);

	if ( ! empty( $pages[0] ) ) {
		return get_permalink( (int) $pages[0] );
	}

	return home_url( '/videos/' );
}

/**
 * Read requested product category slug for video filtering.
 *
 * @return string
 */
function rpt_get_requested_video_category_slug() {
	$keys = array( RPT_VIDEO_CAT_QUERY_VAR, 'video_cat' );

	foreach ( $keys as $key ) {
		$slug = get_query_var( $key );

		if ( is_string( $slug ) && '' !== $slug ) {
			return sanitize_title( $slug );
		}

		if ( isset( $_GET[ $key ] ) ) {
			return sanitize_title( wp_unslash( (string) $_GET[ $key ] ) );
		}
	}

	return '';
}

/**
 * Current product category filter term, if any.
 *
 * @return WP_Term|null
 */
function rpt_get_current_video_category_term() {
	static $resolved = null;

	if ( null !== $resolved ) {
		return $resolved;
	}

	$slug = rpt_get_requested_video_category_slug();

	if ( '' === $slug || ! taxonomy_exists( 'product_cat' ) ) {
		$resolved = null;
		return null;
	}

	$term = get_term_by( 'slug', $slug, 'product_cat' );

	$resolved = ( $term instanceof WP_Term && ! is_wp_error( $term ) ) ? $term : null;

	return $resolved;
}

/**
 * Build shared video query args.
 *
 * @param array $args Query overrides.
 * @return array
 */
function rpt_build_video_query_args( $args = array() ) {
	$defaults = array(
		'post_type'              => 'rpt_video',
		'post_status'            => 'publish',
		'posts_per_page'         => 8,
		'orderby'                => 'date',
		'order'                  => 'DESC',
		'ignore_sticky_posts'    => true,
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => true,
	);

	$query_args = wp_parse_args( $args, $defaults );

	$term = isset( $args['category_term'] ) ? $args['category_term'] : rpt_get_current_video_category_term();

	if ( $term instanceof WP_Term ) {
		$query_args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy'         => 'product_cat',
				'field'            => 'term_id',
				'terms'            => array( (int) $term->term_id ),
				'include_children' => true,
			),
		);
	}

	unset( $query_args['category_term'] );

	return $query_args;
}

/**
 * Query videos by ACF true/false meta with fallback.
 *
 * @param string       $meta_key Meta key.
 * @param int          $limit    Max posts.
 * @param WP_Term|null $term     Category filter.
 * @param int[]        $exclude  Post IDs to exclude.
 * @return WP_Post[]
 */
function rpt_query_videos_by_flag( $meta_key, $limit, $term = null, $exclude = array() ) {
	$limit   = max( 1, (int) $limit );
	$exclude = array_filter( array_map( 'intval', (array) $exclude ) );

	$flagged = get_posts(
		rpt_build_video_query_args(
			array(
				'posts_per_page' => $limit,
				'category_term'  => $term,
				'post__not_in'   => $exclude,
				'meta_query'     => array(
					array(
						'key'     => $meta_key,
						'value'   => '1',
						'compare' => '=',
					),
				),
			)
		)
	);

	if ( count( $flagged ) >= $limit ) {
		return array_slice( $flagged, 0, $limit );
	}

	$fallback_exclude = array_merge( $exclude, wp_list_pluck( $flagged, 'ID' ) );
	$remaining        = $limit - count( $flagged );

	$fallback = get_posts(
		rpt_build_video_query_args(
			array(
				'posts_per_page' => $remaining,
				'category_term'  => $term,
				'post__not_in'   => $fallback_exclude,
			)
		)
	);

	return array_merge( $flagged, $fallback );
}

/**
 * Featured video for hub.
 *
 * @param WP_Term|null $term Optional category filter.
 * @return WP_Post|null
 */
function rpt_get_featured_video( $term = null ) {
	$term  = $term ? $term : rpt_get_current_video_category_term();
	$posts = rpt_query_videos_by_flag( 'video_is_featured', 1, $term );

	return ! empty( $posts[0] ) ? $posts[0] : null;
}

/**
 * Popular videos for hub.
 *
 * @param int          $limit Max posts.
 * @param WP_Term|null $term  Optional category filter.
 * @param int[]        $exclude Post IDs to exclude.
 * @return WP_Post[]
 */
function rpt_get_popular_videos( $limit = 8, $term = null, $exclude = array() ) {
	$term = $term ? $term : rpt_get_current_video_category_term();

	return rpt_query_videos_by_flag( 'video_is_popular', $limit, $term, $exclude );
}

/**
 * Latest videos for hub.
 *
 * @param int          $limit Max posts.
 * @param WP_Term|null $term  Optional category filter.
 * @param int[]        $exclude Post IDs to exclude.
 * @return WP_Post[]
 */
function rpt_get_latest_videos( $limit = 12, $term = null, $exclude = array() ) {
	$term = $term ? $term : rpt_get_current_video_category_term();

	return get_posts(
		rpt_build_video_query_args(
			array(
				'posts_per_page' => max( 1, (int) $limit ),
				'category_term'  => $term,
				'post__not_in'   => array_filter( array_map( 'intval', (array) $exclude ) ),
			)
		)
	);
}

/**
 * Linked WooCommerce product IDs for a video.
 *
 * @param int $post_id Video post ID.
 * @return int[]
 */
function rpt_get_video_linked_product_ids( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$ids     = array();

	if ( ! $post_id || ! function_exists( 'get_field' ) ) {
		return $ids;
	}

	$field_names = array(
		'video_related_products',
		'related_products',
		'related_product',
		'video_products',
		'products',
		'product',
	);

	foreach ( $field_names as $field_name ) {
		$value = get_field( $field_name, $post_id );

		if ( empty( $value ) ) {
			continue;
		}

		$items = is_array( $value ) ? $value : array( $value );

		foreach ( $items as $item ) {
			$product_id = 0;

			if ( $item instanceof WP_Post ) {
				$product_id = (int) $item->ID;
			} elseif ( is_numeric( $item ) ) {
				$product_id = (int) $item;
			}

			if ( $product_id > 0 && 'product' === get_post_type( $product_id ) ) {
				$ids[] = $product_id;
			}
		}
	}

	return array_values( array_unique( array_filter( $ids ) ) );
}

/**
 * Map a legacy or custom term slug/name to a product category.
 *
 * @param string $slug Term slug.
 * @param string $name Term name.
 * @return WP_Term|null
 */
function rpt_map_term_to_product_cat( $slug, $name = '' ) {
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		return null;
	}

	$term = get_term_by( 'slug', $slug, 'product_cat' );

	if ( ! ( $term instanceof WP_Term ) && '' !== $name ) {
		$term = get_term_by( 'name', $name, 'product_cat' );
	}

	if ( ! ( $term instanceof WP_Term ) || is_wp_error( $term ) ) {
		return null;
	}

	return rpt_get_video_top_level_product_category( $term );
}

/**
 * Legacy video_category terms mapped to product_cat.
 *
 * @param int $post_id Video post ID.
 * @return WP_Term[]
 */
function rpt_get_legacy_video_category_product_terms( $post_id ) {
	$post_id = (int) $post_id;
	$terms   = array();

	if ( taxonomy_exists( 'video_category' ) ) {
		$legacy_terms = get_the_terms( $post_id, 'video_category' );

		if ( ! empty( $legacy_terms ) && ! is_wp_error( $legacy_terms ) ) {
			foreach ( $legacy_terms as $legacy_term ) {
				if ( ! $legacy_term instanceof WP_Term ) {
					continue;
				}

				$mapped = rpt_map_term_to_product_cat( $legacy_term->slug, $legacy_term->name );

				if ( $mapped instanceof WP_Term ) {
					$terms[ (int) $mapped->term_id ] = $mapped;
				}
			}
		}

		return array_values( $terms );
	}

	global $wpdb;

	$rows = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT t.slug, t.name
			FROM {$wpdb->term_relationships} tr
			INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
			INNER JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
			WHERE tr.object_id = %d AND tt.taxonomy = %s",
			$post_id,
			'video_category'
		)
	);

	if ( empty( $rows ) ) {
		return array();
	}

	foreach ( $rows as $row ) {
		$mapped = rpt_map_term_to_product_cat( (string) $row->slug, (string) $row->name );

		if ( $mapped instanceof WP_Term ) {
			$terms[ (int) $mapped->term_id ] = $mapped;
		}
	}

	return array_values( $terms );
}

/**
 * All product categories associated with a video.
 *
 * @param int $post_id Video post ID.
 * @return WP_Term[]
 */
function rpt_get_video_product_category_terms( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$map     = array();

	if ( ! $post_id || ! taxonomy_exists( 'product_cat' ) ) {
		return array();
	}

	$direct_terms = get_the_terms( $post_id, 'product_cat' );

	if ( ! empty( $direct_terms ) && ! is_wp_error( $direct_terms ) ) {
		foreach ( $direct_terms as $term ) {
			if ( $term instanceof WP_Term ) {
				$top                          = rpt_get_video_top_level_product_category( $term );
				$map[ (int) $top->term_id ] = $top;
			}
		}
	}

	foreach ( rpt_get_video_linked_product_ids( $post_id ) as $product_id ) {
		$product_terms = get_the_terms( $product_id, 'product_cat' );

		if ( empty( $product_terms ) || is_wp_error( $product_terms ) ) {
			continue;
		}

		foreach ( $product_terms as $term ) {
			if ( $term instanceof WP_Term ) {
				$top                          = rpt_get_video_top_level_product_category( $term );
				$map[ (int) $top->term_id ] = $top;
			}
		}
	}

	foreach ( rpt_get_legacy_video_category_product_terms( $post_id ) as $term ) {
		$map[ (int) $term->term_id ] = $term;
	}

	return array_values( $map );
}

/**
 * Product categories for a WooCommerce product video.
 *
 * @param int $product_id Product ID.
 * @return WP_Term[]
 */
function rpt_get_product_video_category_terms( $product_id ) {
	$product_id = (int) $product_id;
	$map        = array();

	if ( ! $product_id || ! taxonomy_exists( 'product_cat' ) ) {
		return array();
	}

	$terms = get_the_terms( $product_id, 'product_cat' );

	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return array();
	}

	foreach ( $terms as $term ) {
		if ( $term instanceof WP_Term ) {
			$top                          = rpt_get_video_top_level_product_category( $term );
			$map[ (int) $top->term_id ] = $top;
		}
	}

	return array_values( $map );
}

/**
 * Published WooCommerce products that have a hub video URL.
 *
 * @return WC_Product[]
 */
function rpt_get_products_with_hub_video() {
	if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'rpt_get_product_video_url' ) ) {
		return array();
	}

	$product_ids = get_posts(
		array(
			'post_type'              => 'product',
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'fields'                 => 'ids',
			'orderby'                => 'date',
			'order'                  => 'DESC',
			'no_found_rows'          => true,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => true,
			'meta_query'             => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => 'product_video_url',
					'compare' => 'EXISTS',
				),
			),
		)
	);

	$products = array();

	foreach ( $product_ids as $product_id ) {
		$product = wc_get_product( $product_id );

		if ( ! $product || ! $product->is_visible() ) {
			continue;
		}

		if ( '' === rpt_get_product_video_url( $product ) ) {
			continue;
		}

		$products[] = $product;
	}

	return $products;
}

/**
 * Normalize a hub video item.
 *
 * @param mixed $item Hub item or legacy post object.
 * @return array{type: string, object: WP_Post|WC_Product}|null
 */
function rpt_normalize_hub_video_item( $item ) {
	if ( is_array( $item ) && isset( $item['type'], $item['object'] ) ) {
		return $item;
	}

	if ( $item instanceof WP_Post && 'rpt_video' === $item->post_type ) {
		return array(
			'type'   => 'rpt_video',
			'object' => $item,
		);
	}

	if ( is_a( $item, 'WC_Product' ) ) {
		return array(
			'type'   => 'product',
			'object' => $item,
		);
	}

	return null;
}

/**
 * Sort timestamp for a hub video item.
 *
 * @param array{type: string, object: WP_Post|WC_Product} $item Hub item.
 * @return int
 */
function rpt_get_hub_video_sort_timestamp( $item ) {
	$item = rpt_normalize_hub_video_item( $item );

	if ( ! $item ) {
		return 0;
	}

	if ( 'rpt_video' === $item['type'] && $item['object'] instanceof WP_Post ) {
		return (int) get_post_timestamp( $item['object'], 'date' );
	}

	if ( 'product' === $item['type'] && is_a( $item['object'], 'WC_Product' ) ) {
		$post = get_post( $item['object']->get_id() );

		return $post instanceof WP_Post ? (int) get_post_timestamp( $post, 'date' ) : 0;
	}

	return 0;
}

/**
 * Product categories associated with a hub video item.
 *
 * @param array{type: string, object: WP_Post|WC_Product} $item Hub item.
 * @return WP_Term[]
 */
function rpt_get_hub_video_category_terms( $item ) {
	$item = rpt_normalize_hub_video_item( $item );

	if ( ! $item ) {
		return array();
	}

	if ( 'rpt_video' === $item['type'] && $item['object'] instanceof WP_Post ) {
		return rpt_get_video_product_category_terms( $item['object']->ID );
	}

	if ( 'product' === $item['type'] && is_a( $item['object'], 'WC_Product' ) ) {
		return rpt_get_product_video_category_terms( $item['object']->get_id() );
	}

	return array();
}

/**
 * Unique key for deduplicating hub items inside a category.
 *
 * @param array{type: string, object: WP_Post|WC_Product} $item Hub item.
 * @return string
 */
function rpt_get_hub_video_item_key( $item ) {
	$item = rpt_normalize_hub_video_item( $item );

	if ( ! $item ) {
		return '';
	}

	if ( 'rpt_video' === $item['type'] && $item['object'] instanceof WP_Post ) {
		return 'rpt_video:' . (int) $item['object']->ID;
	}

	if ( 'product' === $item['type'] && is_a( $item['object'], 'WC_Product' ) ) {
		return 'product:' . (int) $item['object']->get_id();
	}

	return '';
}

/**
 * All hub video items from CPT videos and WooCommerce products.
 *
 * @return array<int, array{type: string, object: WP_Post|WC_Product}>
 */
function rpt_get_all_hub_video_items() {
	$items = array();

	$videos = get_posts(
		array(
			'post_type'              => 'rpt_video',
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'orderby'                => 'date',
			'order'                  => 'DESC',
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => true,
		)
	);

	foreach ( $videos as $video ) {
		if ( $video instanceof WP_Post ) {
			$items[] = array(
				'type'   => 'rpt_video',
				'object' => $video,
			);
		}
	}

	foreach ( rpt_get_products_with_hub_video() as $product ) {
		$items[] = array(
			'type'   => 'product',
			'object' => $product,
		);
	}

	usort(
		$items,
		static function ( $a, $b ) {
			return rpt_get_hub_video_sort_timestamp( $b ) <=> rpt_get_hub_video_sort_timestamp( $a );
		}
	);

	return $items;
}

/**
 * Indexed hub data: categories with videos and videos grouped by category.
 *
 * @return array{categories: WP_Term[], videos_by_category: array<int, array<int, array{type: string, object: WP_Post|WC_Product}>>}
 */
function rpt_get_video_hub_index() {
	static $index = null;

	if ( null !== $index ) {
		return $index;
	}

	$index = array(
		'categories'         => array(),
		'videos_by_category' => array(),
	);

	if ( ! taxonomy_exists( 'product_cat' ) ) {
		return $index;
	}

	$hub_items              = rpt_get_all_hub_video_items();
	$term_ids_with_videos   = array();

	if ( empty( $hub_items ) ) {
		return $index;
	}

	foreach ( $hub_items as $item ) {
		$terms = rpt_get_hub_video_category_terms( $item );

		foreach ( $terms as $term ) {
			$term_id = (int) $term->term_id;
			$item_key = rpt_get_hub_video_item_key( $item );

			$term_ids_with_videos[ $term_id ] = true;

			if ( ! isset( $index['videos_by_category'][ $term_id ] ) ) {
				$index['videos_by_category'][ $term_id ] = array();
			}

			if ( '' !== $item_key && isset( $index['videos_by_category'][ $term_id ][ $item_key ] ) ) {
				continue;
			}

			$index['videos_by_category'][ $term_id ][ $item_key ] = $item;
		}
	}

	if ( empty( $term_ids_with_videos ) ) {
		return $index;
	}

	foreach ( $index['videos_by_category'] as $term_id => $bucket ) {
		$index['videos_by_category'][ $term_id ] = array_values( $bucket );

		usort(
			$index['videos_by_category'][ $term_id ],
			static function ( $a, $b ) {
				return rpt_get_hub_video_sort_timestamp( $b ) <=> rpt_get_hub_video_sort_timestamp( $a );
			}
		);
	}

	$all_categories = rpt_get_top_level_product_categories(
		array(
			'hide_empty'      => false,
			'exclude_default' => true,
			'orderby'         => 'menu_order',
			'order'           => 'ASC',
		)
	);

	foreach ( $all_categories as $term ) {
		if ( isset( $term_ids_with_videos[ (int) $term->term_id ] ) ) {
			$index['categories'][] = $term;
		}
	}

	return $index;
}

/**
 * Product categories that have at least one published video.
 *
 * @return WP_Term[]
 */
function rpt_get_video_hub_product_categories() {
	$index = rpt_get_video_hub_index();

	return ! empty( $index['categories'] ) ? $index['categories'] : array();
}

/**
 * Product categories for the video sidebar.
 *
 * @return WP_Term[]
 */
function rpt_get_video_categories() {
	return rpt_get_video_hub_product_categories();
}

/**
 * Count published videos in a product category.
 *
 * @param WP_Term $term Product category term.
 * @return int
 */
function rpt_count_videos_in_product_category( WP_Term $term ) {
	$term  = rpt_get_video_top_level_product_category( $term );
	$index = rpt_get_video_hub_index();
	$term_id = (int) $term->term_id;

	return isset( $index['videos_by_category'][ $term_id ] )
		? count( $index['videos_by_category'][ $term_id ] )
		: 0;
}

/**
 * Videos for a product category.
 *
 * @param WP_Term $term  Product category term.
 * @param int     $limit Max posts. -1 for all.
 * @return array<int, array{type: string, object: WP_Post|WC_Product}>
 */
function rpt_get_videos_for_product_category( WP_Term $term, $limit = RPT_VIDEOS_PER_SECTION ) {
	$limit   = (int) $limit;
	$term    = rpt_get_video_top_level_product_category( $term );
	$index   = rpt_get_video_hub_index();
	$term_id = (int) $term->term_id;
	$videos  = isset( $index['videos_by_category'][ $term_id ] )
		? $index['videos_by_category'][ $term_id ]
		: array();

	if ( -1 === $limit ) {
		return $videos;
	}

	return array_slice( $videos, 0, max( 1, $limit ) );
}

/**
 * Resolve top-level product category for display/filtering.
 *
 * @param WP_Term $term Product category term.
 * @return WP_Term
 */
function rpt_get_video_top_level_product_category( WP_Term $term ) {
	if ( 0 === (int) $term->parent ) {
		return $term;
	}

	$ancestors = get_ancestors( $term->term_id, 'product_cat', 'taxonomy' );

	if ( empty( $ancestors ) ) {
		return $term;
	}

	$top_id = (int) end( $ancestors );
	$top    = get_term( $top_id, 'product_cat' );

	return ( $top instanceof WP_Term && ! is_wp_error( $top ) ) ? $top : $term;
}

/**
 * Primary product category for a video.
 *
 * @param int $post_id Post ID.
 * @return WP_Term|null
 */
function rpt_get_video_primary_category( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$terms   = rpt_get_video_product_category_terms( $post_id );

	return ! empty( $terms[0] ) ? $terms[0] : null;
}

/**
 * Product category name label for a video.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function rpt_get_video_category_name( $post_id = 0 ) {
	$term = rpt_get_video_primary_category( $post_id );

	return $term instanceof WP_Term ? $term->name : '';
}

/**
 * Video hub filter URL for a product category.
 *
 * @param WP_Term $term Product category term.
 * @return string
 */
function rpt_get_video_category_link( WP_Term $term ) {
	$term = rpt_get_video_top_level_product_category( $term );

	return add_query_arg(
		RPT_VIDEO_CAT_QUERY_VAR,
		$term->slug,
		rpt_get_videos_page_url()
	);
}

/**
 * Whether sidebar category is active.
 *
 * @param WP_Term|null $term Product category term.
 * @return bool
 */
function rpt_is_video_category_active( $term ) {
	if ( ! $term instanceof WP_Term ) {
		return false;
	}

	$current = rpt_get_current_video_category_term();

	if ( ! $current instanceof WP_Term ) {
		return false;
	}

	$term    = rpt_get_video_top_level_product_category( $term );
	$current = rpt_get_video_top_level_product_category( $current );

	return (int) $current->term_id === (int) $term->term_id;
}

/**
 * Whether "All Videos" sidebar item is active.
 *
 * @return bool
 */
function rpt_is_all_videos_active() {
	return rpt_is_videos_page() && ! rpt_is_videos_category_filter();
}

/**
 * Formatted publish date for video cards.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function rpt_get_video_publish_date( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$date    = get_the_date( '', $post_id );

	return is_string( $date ) ? $date : '';
}

/**
 * Short excerpt/description for featured video.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function rpt_get_video_excerpt( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$excerpt = get_the_excerpt( $post_id );

	if ( '' === $excerpt ) {
		$content = get_post_field( 'post_content', $post_id );
		$excerpt = wp_trim_words( wp_strip_all_tags( (string) $content ), 40, '...' );
	}

	return $excerpt;
}

/**
 * Detect direct MP4/file URL.
 *
 * @param string $url Video URL.
 * @return bool
 */
function rpt_video_url_is_file( $url ) {
	return (bool) preg_match( '/\.(mp4|webm|ogg)(\?.*)?$/i', $url );
}

/**
 * Lightbox payload for an rpt_video post.
 *
 * @param WP_Post $video Video post.
 * @return array<string, string>|null
 */
function rpt_get_rpt_video_lightbox_data( WP_Post $video ) {
	$url = rpt_get_video_url( $video->ID );

	if ( '' === $url ) {
		return null;
	}

	$poster_url = '';

	if ( has_post_thumbnail( $video ) ) {
		$thumb = get_the_post_thumbnail_url( $video, 'large' );

		if ( $thumb ) {
			$poster_url = esc_url_raw( $thumb );
		}
	}

	return rpt_get_video_lightbox_payload( $url, get_the_title( $video ), $poster_url );
}

/**
 * Build lightbox data from a video URL.
 *
 * @param string $url        Video URL.
 * @param string $title      Video title.
 * @param string $poster_url Optional poster image URL.
 * @return array<string, string>|null
 */
function rpt_get_video_lightbox_payload( $url, $title = '', $poster_url = '' ) {
	$url = trim( (string) $url );

	if ( '' === $url ) {
		return null;
	}

	$data = array(
		'title'  => (string) $title,
		'poster' => (string) $poster_url,
	);

	if ( rpt_video_url_is_file( $url ) ) {
		$data['type'] = 'file';
		$data['src']  = esc_url_raw( $url );

		return $data;
	}

	$embed_src = function_exists( 'rpt_get_product_video_embed_src' )
		? rpt_get_product_video_embed_src( $url )
		: '';

	if ( '' !== $embed_src ) {
		$data['type'] = 'iframe';
		$data['src']  = $embed_src;

		return $data;
	}

	$data['type'] = 'file';
	$data['src']  = esc_url_raw( $url );

	return $data;
}

/**
 * Build embed HTML for a video URL.
 *
 * @param string $url Video URL.
 * @return string
 */
function rpt_get_video_embed_html( $url ) {
	$url = esc_url_raw( trim( (string) $url ) );

	if ( '' === $url ) {
		return '';
	}

	if ( rpt_video_url_is_file( $url ) ) {
		return sprintf(
			'<video class="rpt-video-player" controls playsinline preload="metadata" src="%1$s"></video>',
			esc_url( $url )
		);
	}

	$oembed = wp_oembed_get( $url, array( 'width' => 1280 ) );

	if ( $oembed ) {
		return $oembed;
	}

	return sprintf(
		'<video class="rpt-video-player" controls playsinline preload="metadata" src="%1$s"></video>',
		esc_url( $url )
	);
}

/**
 * Featured media markup for hub card.
 *
 * @param WP_Post $video Video post.
 * @return string
 */
function rpt_get_video_featured_media_html( WP_Post $video ) {
	$url   = rpt_get_video_url( $video->ID );
	$embed = $url ? rpt_get_video_embed_html( $url ) : '';

	if ( $embed ) {
		return '<div class="rpt-video-featured__embed">' . $embed . '</div>';
	}

	$permalink = get_permalink( $video );
	$thumb     = get_the_post_thumbnail(
		$video,
		'large',
		array(
			'class' => 'rpt-video-featured__thumb-img',
			'alt'   => esc_attr( get_the_title( $video ) ),
		)
	);

	if ( ! $thumb ) {
		$thumb = '<span class="rpt-video-featured__thumb-placeholder" aria-hidden="true"></span>';
	}

	return sprintf(
		'<a class="rpt-video-featured__thumb-link" href="%1$s" aria-label="%2$s"><span class="rpt-video-featured__play" aria-hidden="true"></span>%3$s</a>',
		esc_url( $permalink ),
		esc_attr( get_the_title( $video ) ),
		$thumb
	);
}

/**
 * Force full-width layout without theme sidebar on video pages.
 *
 * @param string $layout Sidebar layout slug.
 * @return string
 */
function rpt_video_sidebar_layout( $layout ) {
	if ( rpt_uses_video_layout() ) {
		return 'no-sidebar';
	}

	return $layout;
}
add_filter( 'generate_sidebar_layout', 'rpt_video_sidebar_layout' );

/**
 * Body class for video pages.
 *
 * @param array $classes Body classes.
 * @return array
 */
function rpt_video_body_class( $classes ) {
	if ( ! rpt_uses_video_layout() ) {
		return $classes;
	}

	$classes[] = 'rpt-video-page';
	$classes[] = 'full-width-content';
	$classes[] = 'no-sidebar';

	$remove = array( 'right-sidebar', 'left-sidebar', 'both-sidebars', 'both-left', 'both-right' );

	return array_values( array_diff( $classes, $remove ) );
}
add_filter( 'body_class', 'rpt_video_body_class', 20 );
