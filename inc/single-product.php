<?php
/**
 * Single product — B2B catalog layout.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Remove default WooCommerce single product output.
 */
function rpt_setup_single_product_layout() {
	if ( ! rpt_is_single_product_page() ) {
		return;
	}

	remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

	remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
	remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );

	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );

	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
}
add_action( 'wp', 'rpt_setup_single_product_layout' );

/**
 * Contact rows for single product sidebar card (includes WeChat).
 *
 * @return array<int, array<string, string>>
 */
function rpt_get_product_contact_rows() {
	$keys = array( 'email', 'phone', 'whatsapp', 'wechat', 'skype' );
	$map  = array(
		'email'    => __( 'E-mail', 'generatepress_child' ),
		'phone'    => __( 'Điện thoại', 'generatepress_child' ),
		'whatsapp' => __( 'WhatsApp', 'generatepress_child' ),
		'wechat'   => __( 'WeChat', 'generatepress_child' ),
		'skype'    => __( 'Skype', 'generatepress_child' ),
	);

	$rows = array();

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

	return apply_filters( 'rpt_product_contact_rows', $rows );
}

/**
 * Gallery image data for a product.
 *
 * @param WC_Product $product Product.
 * @return array<int, array{id: int, full: string, thumb: string, alt: string}>
 */
function rpt_get_product_gallery_images( $product ) {
	if ( ! $product instanceof WC_Product ) {
		return array();
	}

	$image_ids = array();
	$featured  = $product->get_image_id();

	if ( $featured ) {
		$image_ids[] = (int) $featured;
	}

	foreach ( $product->get_gallery_image_ids() as $image_id ) {
		$image_id = (int) $image_id;

		if ( $image_id && ! in_array( $image_id, $image_ids, true ) ) {
			$image_ids[] = $image_id;
		}
	}

	if ( empty( $image_ids ) ) {
		return array(
			array(
				'id'     => 0,
				'full'   => wc_placeholder_img_src( 'woocommerce_single' ),
				'thumb'  => wc_placeholder_img_src( 'woocommerce_thumbnail' ),
				'alt'    => $product->get_name(),
			),
		);
	}

	$images = array();

	foreach ( $image_ids as $image_id ) {
		$full  = wp_get_attachment_image_url( $image_id, 'woocommerce_single' );
		$thumb = wp_get_attachment_image_url( $image_id, 'woocommerce_thumbnail' );

		if ( ! $full ) {
			continue;
		}

		$images[] = array(
			'id'    => $image_id,
			'full'  => $full,
			'thumb' => $thumb ? $thumb : $full,
			'alt'   => get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ?: $product->get_name(),
		);
	}

	return $images;
}

/**
 * Gallery items for single product, including optional video slide.
 *
 * @param WC_Product $product Product.
 * @return array<int, array<string, mixed>>
 */
function rpt_get_product_gallery_items( $product ) {
	if ( ! $product instanceof WC_Product ) {
		return array();
	}

	$images     = rpt_get_product_gallery_images( $product );
	$video_data = function_exists( 'rpt_get_product_video_lightbox_data' )
		? rpt_get_product_video_lightbox_data( $product )
		: null;

	foreach ( $images as $index => $image ) {
		$images[ $index ]['type'] = 'image';
	}

	if ( ! is_array( $video_data ) || empty( $video_data['src'] ) ) {
		return $images;
	}

	$poster       = function_exists( 'rpt_get_product_video_poster' ) ? rpt_get_product_video_poster( $product ) : null;
	$poster_full  = '';
	$poster_thumb = '';

	if ( is_array( $poster ) ) {
		$poster_full  = ! empty( $poster['url'] ) ? $poster['url'] : '';
		$poster_thumb = ! empty( $poster['sizes']['woocommerce_thumbnail'] ) ? $poster['sizes']['woocommerce_thumbnail'] : $poster_full;
	}

	if ( '' === $poster_full && ! empty( $images[0]['full'] ) ) {
		$poster_full  = $images[0]['full'];
		$poster_thumb = $images[0]['thumb'];
	}

	if ( '' === $poster_full ) {
		$poster_full  = wc_placeholder_img_src( 'woocommerce_single' );
		$poster_thumb = wc_placeholder_img_src( 'woocommerce_thumbnail' );
	}

	if ( '' === $poster_thumb ) {
		$poster_thumb = $poster_full;
	}

	array_unshift(
		$images,
		array(
			'id'    => 0,
			'type'  => 'video',
			'full'  => $poster_full,
			'thumb' => $poster_thumb,
			'alt'   => $video_data['title'],
		)
	);

	return $images;
}

/**
 * Related products for the single product page.
 *
 * @param WC_Product $product Product.
 * @param int        $limit   Max products.
 * @return array<int, WC_Product>
 */
function rpt_get_single_related_products( $product, $limit = 4 ) {
	if ( ! $product instanceof WC_Product || ! function_exists( 'wc_get_product' ) ) {
		return array();
	}

	$limit = (int) apply_filters( 'rpt_single_related_products_limit', $limit, $product );
	$limit = max( 1, min( 8, $limit ) );

	$related_ids = wc_get_related_products( $product->get_id(), $limit );
	$products    = array();

	foreach ( $related_ids as $related_id ) {
		$related = wc_get_product( $related_id );

		if ( $related instanceof WC_Product && $related->is_visible() ) {
			$products[] = $related;
		}
	}

	return $products;
}

/**
 * Main CTA URL on single product.
 *
 * @param WC_Product|null $product Product.
 * @return string
 */
function rpt_get_single_product_cta_url( $product = null ) {
	$url = rpt_get_shop_hub_cta_url();

	if ( $product instanceof WC_Product ) {
		$inquiry = rpt_get_product_inquiry_url( $product );

		if ( $inquiry ) {
			$url = $inquiry;
		}
	}

	return $url;
}
