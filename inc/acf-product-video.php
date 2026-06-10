<?php
/**
 * ACF — Product video settings.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register local ACF field group for product videos.
 */
function rpt_register_product_video_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_rpt_product_video_settings',
			'title'    => __( 'Product Video Settings', 'generatepress_child' ),
			'fields'   => array(
				array(
					'key'           => 'field_rpt_product_video_url',
					'label'         => __( 'Link video sản phẩm', 'generatepress_child' ),
					'name'          => 'product_video_url',
					'type'          => 'url',
					'placeholder'   => __( 'Nhập link YouTube, Vimeo hoặc video MP4', 'generatepress_child' ),
					'instructions'  => __( 'Nếu sản phẩm có video, nhập link video tại đây. Frontend sẽ hiển thị nút play trên card sản phẩm.', 'generatepress_child' ),
				),
				array(
					'key'           => 'field_rpt_product_video_poster',
					'label'         => __( 'Ảnh cover video', 'generatepress_child' ),
					'name'          => 'product_video_poster',
					'type'          => 'image',
					'return_format' => 'array',
					'preview_size'  => 'medium',
					'library'       => 'all',
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'product',
					),
				),
			),
			'active'   => true,
		)
	);
}
add_action( 'acf/init', 'rpt_register_product_video_fields' );

/**
 * Get product video URL.
 *
 * @param int|WC_Product $product Product ID or object.
 * @return string
 */
function rpt_get_product_video_url( $product = 0 ) {
	$product_id = 0;

	if ( $product instanceof WC_Product ) {
		$product_id = $product->get_id();
	} elseif ( is_numeric( $product ) ) {
		$product_id = (int) $product;
	}

	if ( ! $product_id || ! function_exists( 'get_field' ) ) {
		return '';
	}

	$url = get_field( 'product_video_url', $product_id );

	if ( ! is_string( $url ) ) {
		return '';
	}

	$url = esc_url_raw( trim( $url ) );

	return $url ? $url : '';
}

/**
 * Get product video poster image array from ACF.
 *
 * @param int|WC_Product $product Product ID or object.
 * @return array<string, mixed>|null
 */
function rpt_get_product_video_poster( $product = 0 ) {
	$product_id = 0;

	if ( $product instanceof WC_Product ) {
		$product_id = $product->get_id();
	} elseif ( is_numeric( $product ) ) {
		$product_id = (int) $product;
	}

	if ( ! $product_id || ! function_exists( 'get_field' ) ) {
		return null;
	}

	$poster = get_field( 'product_video_poster', $product_id );

	return is_array( $poster ) && ! empty( $poster['url'] ) ? $poster : null;
}

/**
 * Whether product has a valid video URL.
 *
 * @param int|WC_Product $product Product ID or object.
 * @return bool
 */
function rpt_product_has_video( $product = 0 ) {
	return '' !== rpt_get_product_video_url( $product );
}

/**
 * Whether URL points to a direct video file.
 *
 * @param string $url Video URL.
 * @return bool
 */
function rpt_product_video_url_is_file( $url ) {
	return (bool) preg_match( '/\.(mp4|webm|ogg)(\?.*)?$/i', (string) $url );
}

/**
 * Build embed src for YouTube/Vimeo URLs.
 *
 * @param string $url Video URL.
 * @return string
 */
function rpt_get_product_video_embed_src( $url ) {
	$url = trim( (string) $url );

	if ( '' === $url ) {
		return '';
	}

	if ( preg_match( '/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/|live\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches ) ) {
		return 'https://www.youtube.com/embed/' . $matches[1] . '?autoplay=1&rel=0';
	}

	if ( preg_match( '/youtube-nocookie\.com\/embed\/([a-zA-Z0-9_-]{11})/', $url, $matches ) ) {
		return 'https://www.youtube-nocookie.com/embed/' . $matches[1] . '?autoplay=1&rel=0';
	}

	if ( preg_match( '/vimeo\.com\/(?:video\/)?(\d+)/', $url, $matches ) ) {
		return 'https://player.vimeo.com/video/' . $matches[1] . '?autoplay=1';
	}

	if ( function_exists( 'wp_oembed_get' ) ) {
		$oembed = wp_oembed_get( $url );

		if ( $oembed && preg_match( '/src="([^"]+)"/', $oembed, $matches ) ) {
			$src = html_entity_decode( $matches[1], ENT_QUOTES );

			return add_query_arg( 'autoplay', '1', $src );
		}
	}

	return '';
}

/**
 * Lightbox payload for a product video.
 *
 * @param WC_Product $product Product object.
 * @return array<string, string>|null
 */
function rpt_get_product_video_lightbox_data( WC_Product $product ) {
	$url = rpt_get_product_video_url( $product );

	if ( '' === $url ) {
		return null;
	}

	$poster     = rpt_get_product_video_poster( $product );
	$poster_url = ( is_array( $poster ) && ! empty( $poster['url'] ) ) ? esc_url_raw( $poster['url'] ) : '';

	$data = array(
		'title'  => $product->get_name(),
		'poster' => $poster_url,
	);

	if ( rpt_product_video_url_is_file( $url ) ) {
		$data['type'] = 'file';
		$data['src']  = $url;

		return $data;
	}

	$embed_src = rpt_get_product_video_embed_src( $url );

	if ( '' !== $embed_src ) {
		$data['type'] = 'iframe';
		$data['src']  = $embed_src;

		return $data;
	}

	$data['type'] = 'file';
	$data['src']  = $url;

	return $data;
}

/**
 * Whether product video lightbox assets should load.
 *
 * @return bool
 */
function rpt_should_load_product_video_lightbox() {
	if ( function_exists( 'rpt_uses_video_layout' ) && rpt_uses_video_layout() ) {
		return true;
	}

	if ( function_exists( 'rpt_uses_custom_catalog_layout' ) && rpt_uses_custom_catalog_layout() ) {
		return true;
	}

	return function_exists( 'rpt_is_single_product_page' ) && rpt_is_single_product_page();
}

/**
 * Enqueue product video lightbox script.
 */
function rpt_enqueue_product_video_lightbox_assets() {
	if ( ! rpt_should_load_product_video_lightbox() ) {
		return;
	}

	wp_enqueue_style(
		'rpt-video-lightbox',
		get_stylesheet_directory_uri() . '/assets/css/video-lightbox.css',
		array( 'rpt-components' ),
		rpt_get_asset_version( 'assets/css/video-lightbox.css' )
	);

	wp_enqueue_script(
		'rpt-product-video-lightbox',
		get_stylesheet_directory_uri() . '/assets/js/product-video-lightbox.js',
		array(),
		rpt_get_asset_version( 'assets/js/product-video-lightbox.js' ),
		rpt_get_theme_script_args()
	);
}
add_action( 'wp_enqueue_scripts', 'rpt_enqueue_product_video_lightbox_assets', 31 );

/**
 * Render shared product video lightbox markup.
 */
function rpt_render_product_video_lightbox() {
	if ( ! rpt_should_load_product_video_lightbox() ) {
		return;
	}

	get_template_part( 'template-parts/product/product', 'video-lightbox' );
}
add_action( 'wp_footer', 'rpt_render_product_video_lightbox', 5 );
