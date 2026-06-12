<?php
/**
 * Homepage helpers and layout detection.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

define( 'RPT_HOME_PAGE_TEMPLATE', 'page-templates/template-home.php' );

/**
 * Whether current view uses the homepage layout.
 *
 * @return bool
 */
function rpt_is_home_page() {
	if ( is_front_page() ) {
		return true;
	}

	return is_page_template( RPT_HOME_PAGE_TEMPLATE );
}

/**
 * Homepage ACF field with fallback.
 *
 * @param string $field   Field name.
 * @param mixed  $default Default value.
 * @return mixed
 */
function rpt_get_home_field( $field, $default = '' ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}

	$post_id = (int) get_queried_object_id();

	if ( ! $post_id && is_front_page() ) {
		$post_id = (int) get_option( 'page_on_front' );
	}

	if ( ! $post_id ) {
		return $default;
	}

	$value = get_field( $field, $post_id );

	if ( null === $value || false === $value || '' === $value ) {
		return $default;
	}

	return $value;
}

/**
 * Hero slides for homepage slider.
 *
 * @return array<int, array{image: string, alt: string, link: string, target: string}>
 */
function rpt_get_home_hero_slides() {
	$rows = rpt_get_home_field( 'home_hero_slides', array() );

	if ( ! is_array( $rows ) ) {
		return array();
	}

	$slides = array();

	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$image = isset( $row['slide_image'] ) ? $row['slide_image'] : null;

		if ( ! is_array( $image ) || empty( $image['url'] ) ) {
			continue;
		}

		$link = isset( $row['slide_link'] ) ? trim( (string) $row['slide_link'] ) : '';

		if ( $link && 0 === strpos( $link, '/' ) && 0 !== strpos( $link, '//' ) ) {
			$link = home_url( $link );
		}

		$target = isset( $row['slide_link_target'] ) ? (string) $row['slide_link_target'] : '_self';

		if ( ! in_array( $target, array( '_self', '_blank' ), true ) ) {
			$target = '_self';
		}

		$alt = '';

		if ( ! empty( $image['alt'] ) ) {
			$alt = (string) $image['alt'];
		} elseif ( ! empty( $image['title'] ) ) {
			$alt = (string) $image['title'];
		}

		$attachment_id = 0;

		if ( ! empty( $image['ID'] ) ) {
			$attachment_id = (int) $image['ID'];
		} elseif ( ! empty( $image['id'] ) ) {
			$attachment_id = (int) $image['id'];
		}

		$slides[] = array(
			'image'         => esc_url( $image['url'] ),
			'attachment_id' => $attachment_id,
			'alt'           => $alt,
			'link'          => $link ? esc_url( $link ) : '',
			'target'        => $target,
		);
	}

	return $slides;
}

/**
 * Render a homepage hero slide image with responsive sizes and LCP hints.
 *
 * @param array<string, mixed> $slide Slide data.
 * @param int                  $index Slide index.
 */
function rpt_render_home_hero_slide_image( $slide, $index ) {
	$attrs = array(
		'class'    => 'rpt-hero-slider__image',
		'loading'  => 0 === $index ? 'eager' : 'lazy',
		'decoding' => 'async',
		'sizes'    => '100vw',
	);

	if ( 0 === $index ) {
		$attrs['fetchpriority'] = 'high';
	}

	if ( ! empty( $slide['attachment_id'] ) ) {
		echo wp_get_attachment_image( (int) $slide['attachment_id'], 'rpt-hero-banner', false, $attrs );
		return;
	}

	if ( empty( $slide['image'] ) ) {
		return;
	}

	$width  = ! empty( $slide['width'] ) ? (int) $slide['width'] : 0;
	$height = ! empty( $slide['height'] ) ? (int) $slide['height'] : 0;

	printf(
		'<img class="rpt-hero-slider__image" src="%1$s" alt="%2$s" loading="%3$s" decoding="async"%4$s%5$s%6$s />',
		esc_url( $slide['image'] ),
		esc_attr( $slide['alt'] ?? '' ),
		esc_attr( $attrs['loading'] ),
		0 === $index ? ' fetchpriority="high"' : '',
		$width ? sprintf( ' width="%d"', $width ) : '',
		$height ? sprintf( ' height="%d"', $height ) : ''
	);
}

/**
 * Resolve ACF image field to URL.
 *
 * @param mixed $image Image field value.
 * @return string
 */
function rpt_get_home_image_url( $image ) {
	if ( is_array( $image ) && ! empty( $image['url'] ) ) {
		return esc_url( $image['url'] );
	}

	if ( is_numeric( $image ) ) {
		$url = wp_get_attachment_image_url( (int) $image, 'full' );

		return $url ? esc_url( $url ) : '';
	}

	return '';
}

/**
 * Full-size URL from ACF image field.
 *
 * @param mixed $image Image field value.
 * @return string
 */
function rpt_get_home_image_full_url( $image ) {
	if ( is_array( $image ) ) {
		$attachment_id = 0;

		if ( ! empty( $image['ID'] ) ) {
			$attachment_id = (int) $image['ID'];
		} elseif ( ! empty( $image['id'] ) ) {
			$attachment_id = (int) $image['id'];
		}

		if ( $attachment_id ) {
			$url = wp_get_attachment_image_url( $attachment_id, 'full' );

			if ( $url ) {
				return esc_url( $url );
			}
		}

		if ( ! empty( $image['url'] ) ) {
			return esc_url( $image['url'] );
		}
	}

	if ( is_numeric( $image ) ) {
		$url = wp_get_attachment_image_url( (int) $image, 'full' );

		return $url ? esc_url( $url ) : '';
	}

	return '';
}

/**
 * Whether certificate lightbox assets should load.
 *
 * @return bool
 */
function rpt_should_load_certificate_lightbox() {
	return function_exists( 'rpt_is_home_page' )
		&& rpt_is_home_page()
		&& ! empty( rpt_get_home_certificates() );
}

/**
 * Enqueue certificate lightbox assets on homepage.
 */
function rpt_enqueue_certificate_lightbox_assets() {
	if ( ! rpt_should_load_certificate_lightbox() ) {
		return;
	}

	wp_enqueue_script(
		'rpt-certificate-lightbox',
		get_stylesheet_directory_uri() . '/assets/js/certificate-lightbox.js',
		array( 'rpt-scroll-lock' ),
		rpt_get_asset_version( 'assets/js/certificate-lightbox.js' ),
		rpt_get_theme_script_args()
	);
}
add_action( 'wp_enqueue_scripts', 'rpt_enqueue_certificate_lightbox_assets', 31 );

/**
 * Render certificate lightbox markup in footer.
 */
function rpt_render_certificate_lightbox() {
	if ( ! rpt_should_load_certificate_lightbox() ) {
		return;
	}

	get_template_part( 'template-parts/home/certificate', 'lightbox' );
}
add_action( 'wp_footer', 'rpt_render_certificate_lightbox' );

/**
 * Category items for homepage icon row.
 *
 * @return array<int, array{term: WP_Term|null, label: string, url: string, image: string}>
 */
function rpt_get_home_category_items() {
	$custom = rpt_get_home_field( 'home_category_items', array() );

	if ( is_array( $custom ) && ! empty( $custom ) ) {
		$items = array();

		foreach ( $custom as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}

			$label = isset( $row['item_label'] ) ? trim( (string) $row['item_label'] ) : '';
			$url   = isset( $row['item_url'] ) ? trim( (string) $row['item_url'] ) : '';
			$image = rpt_get_home_image_url( isset( $row['item_image'] ) ? $row['item_image'] : null );

			if ( '' === $label || ! $image ) {
				continue;
			}

			if ( function_exists( 'rpt_resolve_product_category_url_from_media' ) ) {
				$url = rpt_resolve_product_category_url_from_media( $url );

				if ( '' === $url ) {
					$url = rpt_resolve_product_category_url_from_media( $image );
				}
			}

			$items[] = array(
				'term'  => null,
				'label' => $label,
				'url'   => $url ? $url : '#',
				'image' => $image,
			);
		}

		if ( ! empty( $items ) ) {
			return $items;
		}
	}

	$terms = rpt_get_top_level_product_categories(
		array(
			'limit'           => 6,
			'hide_empty'      => false,
			'exclude_default' => true,
			'orderby'         => 'menu_order',
		)
	);

	$items = array();

	foreach ( $terms as $term ) {
		if ( ! $term instanceof WP_Term ) {
			continue;
		}

		$thumb_id = (int) get_term_meta( $term->term_id, 'thumbnail_id', true );
		$image    = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'medium' ) : '';

		if ( ! $image ) {
			continue;
		}

		$link = rpt_get_product_category_link( $term );

		$items[] = array(
			'term'  => $term,
			'label' => $term->name,
			'url'   => is_string( $link ) ? $link : '#',
			'image' => esc_url( $image ),
		);
	}

	return $items;
}

/**
 * Best-selling products for homepage.
 *
 * @return array<int, WC_Product>
 */
function rpt_get_home_bestseller_products() {
	if ( ! function_exists( 'wc_get_product' ) ) {
		return array();
	}

	$limit    = (int) rpt_get_home_field( 'home_bestsellers_limit', 24 );
	$limit    = max( 1, min( 24, $limit ) );
	$selected = rpt_get_home_field( 'home_bestsellers_products', array() );
	$products = array();

	if ( is_array( $selected ) && ! empty( $selected ) ) {
		foreach ( $selected as $post_id ) {
			$product = wc_get_product( $post_id );

			if ( $product instanceof WC_Product && $product->is_visible() ) {
				$products[] = $product;
			}

			if ( count( $products ) >= $limit ) {
				break;
			}
		}

		if ( ! empty( $products ) ) {
			return $products;
		}
	}

	$post_ids = rpt_query_bestseller_product_ids( $limit );

	foreach ( $post_ids as $post_id ) {
		$product = wc_get_product( $post_id );

		if ( $product instanceof WC_Product && $product->is_visible() ) {
			$products[] = $product;
		}
	}

	return $products;
}

/**
 * Query best seller product IDs by tag, ACF flag, then total sales.
 *
 * @param int $limit Max products.
 * @return array<int, int>
 */
function rpt_query_bestseller_product_ids( $limit ) {
	$limit = max( 1, min( 24, (int) $limit ) );
	$ids   = array();

	$tag_slugs = array( 'best-seller', 'bestseller', 'ban-chay' );
	$tag_ids   = array();

	foreach ( $tag_slugs as $slug ) {
		$term = get_term_by( 'slug', $slug, 'product_tag' );

		if ( $term instanceof WP_Term ) {
			$tag_ids[] = (int) $term->term_id;
		}
	}

	if ( ! empty( $tag_ids ) ) {
		$tag_query = new WP_Query(
			array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => $limit,
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'orderby'        => 'meta_value_num',
				'meta_key'       => 'total_sales',
				'order'          => 'DESC',
				'tax_query'      => array(
					array(
						'taxonomy' => 'product_tag',
						'field'    => 'term_id',
						'terms'    => array_unique( $tag_ids ),
					),
				),
			)
		);

		$ids = array_merge( $ids, array_map( 'intval', $tag_query->posts ) );
	}

	$ids = array_values( array_unique( $ids ) );

	if ( count( $ids ) < $limit ) {
		$acf_query = new WP_Query(
			array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => $limit - count( $ids ),
				'post__not_in'   => $ids,
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'orderby'        => 'meta_value_num',
				'meta_key'       => 'total_sales',
				'order'          => 'DESC',
				'meta_query'     => array(
					array(
						'key'     => 'is_best_seller',
						'value'   => '1',
						'compare' => '=',
					),
				),
			)
		);

		$ids = array_merge( $ids, array_map( 'intval', $acf_query->posts ) );
	}

	$ids = array_values( array_unique( $ids ) );

	if ( count( $ids ) < $limit ) {
		$sales_query = new WP_Query(
			array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => $limit - count( $ids ),
				'post__not_in'   => $ids,
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'orderby'        => 'meta_value_num',
				'meta_key'       => 'total_sales',
				'order'          => 'DESC',
				'meta_query'     => array(
					array(
						'key'     => 'total_sales',
						'value'   => 0,
						'compare' => '>',
						'type'    => 'NUMERIC',
					),
				),
			)
		);

		$ids = array_merge( $ids, array_map( 'intval', $sales_query->posts ) );
	}

	return array_slice( array_values( array_unique( $ids ) ), 0, $limit );
}

/**
 * Optional "view more" URL for homepage best sellers section.
 *
 * @return string
 */
function rpt_get_home_bestsellers_more_url() {
	$url = rpt_get_home_field( 'home_bestsellers_more_url', '' );

	if ( is_string( $url ) && '' !== trim( $url ) ) {
		return esc_url( $url );
	}

	foreach ( array( 'best-seller', 'bestseller', 'ban-chay' ) as $slug ) {
		$term = get_term_by( 'slug', $slug, 'product_tag' );

		if ( $term instanceof WP_Term ) {
			$link = get_term_link( $term );

			if ( ! is_wp_error( $link ) ) {
				return esc_url( $link );
			}
		}
	}

	return '';
}

/**
 * Achievement stats for homepage.
 *
 * @return array<int, array{value: string, label: string, icon: string}>
 */
function rpt_get_home_achievements() {
	$rows = rpt_get_home_field( 'home_achievements', array() );

	if ( is_array( $rows ) && ! empty( $rows ) ) {
		$items = array();

		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}

			$value = isset( $row['achievement_value'] ) ? trim( (string) $row['achievement_value'] ) : '';
			$label = isset( $row['achievement_label'] ) ? trim( (string) $row['achievement_label'] ) : '';
			$icon  = isset( $row['achievement_icon'] ) ? sanitize_key( (string) $row['achievement_icon'] ) : 'calendar';

			if ( '' === $value || '' === $label ) {
				continue;
			}

			$items[] = array(
				'value' => $value,
				'label' => $label,
				'icon'  => $icon,
			);
		}

		if ( ! empty( $items ) ) {
			return $items;
		}
	}

	return array(
		array(
			'value' => '2.021',
			'label' => __( 'Năm thành lập', 'generatepress_child' ),
			'icon'  => 'calendar',
		),
		array(
			'value' => '300+',
			'label' => __( 'Nhân viên', 'generatepress_child' ),
			'icon'  => 'team',
		),
		array(
			'value' => '100.000.000+',
			'label' => __( 'Doanh thu hàng năm (USD)', 'generatepress_child' ),
			'icon'  => 'revenue',
		),
		array(
			'value' => '70+',
			'label' => __( 'Khách hàng được phục vụ', 'generatepress_child' ),
			'icon'  => 'customers',
		),
	);
}

/**
 * Certificate images for homepage.
 *
 * @return array<int, array{url: string, title: string}>
 */
function rpt_get_home_certificates() {
	$rows = rpt_get_home_field( 'home_certificates', array() );

	if ( ! is_array( $rows ) || empty( $rows ) ) {
		return array();
	}

	$items = array();

	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$url = rpt_get_home_image_url( isset( $row['certificate_image'] ) ? $row['certificate_image'] : null );

		if ( ! $url ) {
			continue;
		}

		$full_url = rpt_get_home_image_full_url( isset( $row['certificate_image'] ) ? $row['certificate_image'] : null );

		if ( ! $full_url ) {
			$full_url = $url;
		}

		$items[] = array(
			'url'      => $url,
			'full_url' => $full_url,
			'title'    => isset( $row['certificate_title'] ) ? trim( (string) $row['certificate_title'] ) : '',
		);
	}

	return $items;
}

/**
 * Latest news posts for homepage.
 *
 * @return WP_Query
 */
function rpt_get_home_latest_news_query() {
	$limit = (int) rpt_get_home_field( 'home_news_limit', 9 );
	$limit = max( 1, min( 9, $limit ) );

	return new WP_Query(
		array(
			'post_type'              => 'post',
			'post_status'            => 'publish',
			'posts_per_page'         => $limit,
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);
}

/**
 * About page URL.
 *
 * @return string
 */
function rpt_get_about_page_url() {
	return rpt_get_page_url_by_slug( 'gioi-thieu', home_url( '/gioi-thieu/' ) );
}

/**
 * Force full-width layout on homepage.
 *
 * @param string $layout Sidebar layout slug.
 * @return string
 */
function rpt_home_sidebar_layout( $layout ) {
	if ( rpt_is_home_page() ) {
		return 'no-sidebar';
	}

	return $layout;
}
add_filter( 'generate_sidebar_layout', 'rpt_home_sidebar_layout' );

/**
 * Body classes for homepage.
 *
 * @param array $classes Body classes.
 * @return array
 */
function rpt_home_body_class( $classes ) {
	if ( ! rpt_is_home_page() ) {
		return $classes;
	}

	$classes[] = 'rpt-home-page-body';
	$classes[] = 'full-width-content';
	$classes[] = 'no-sidebar';

	$remove = array( 'right-sidebar', 'left-sidebar', 'both-sidebars', 'both-left', 'both-right' );

	return array_values( array_diff( $classes, $remove ) );
}
add_filter( 'body_class', 'rpt_home_body_class', 20 );
