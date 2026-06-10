<?php
/**
 * ACF — Video fields.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register local ACF field group for videos.
 */
function rpt_register_video_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_rpt_video_fields',
			'title'    => __( 'Thông tin video', 'generatepress_child' ),
			'fields'   => array(
				array(
					'key'          => 'field_rpt_video_url',
					'label'        => __( 'Video URL', 'generatepress_child' ),
					'name'         => 'video_url',
					'type'         => 'url',
					'instructions' => __( 'YouTube, Vimeo hoặc file MP4.', 'generatepress_child' ),
				),
				array(
					'key'           => 'field_rpt_video_duration',
					'label'         => __( 'Thời lượng video', 'generatepress_child' ),
					'name'          => 'video_duration',
					'type'          => 'text',
					'placeholder'   => '00:49',
				),
				array(
					'key'           => 'field_rpt_video_is_featured',
					'label'         => __( 'Video nổi bật', 'generatepress_child' ),
					'name'          => 'video_is_featured',
					'type'          => 'true_false',
					'ui'            => 1,
					'default_value' => 0,
				),
				array(
					'key'           => 'field_rpt_video_is_popular',
					'label'         => __( 'Video phổ biến', 'generatepress_child' ),
					'name'          => 'video_is_popular',
					'type'          => 'true_false',
					'ui'            => 1,
					'default_value' => 0,
				),
				array(
					'key'           => 'field_rpt_video_related_products',
					'label'         => __( 'Sản phẩm liên quan', 'generatepress_child' ),
					'name'          => 'video_related_products',
					'type'          => 'post_object',
					'post_type'     => array( 'product' ),
					'return_format' => 'id',
					'multiple'      => 1,
					'ui'            => 1,
					'instructions'  => __( 'Chọn sản phẩm WooCommerce gắn với video. Danh mục video sẽ lấy từ sản phẩm nếu chưa chọn Danh mục sản phẩm.', 'generatepress_child' ),
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'rpt_video',
					),
				),
			),
			'active'   => true,
		)
	);
}
add_action( 'acf/init', 'rpt_register_video_fields' );

/**
 * Get video URL.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function rpt_get_video_url( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();

	if ( ! $post_id || ! function_exists( 'get_field' ) ) {
		return '';
	}

	$url = get_field( 'video_url', $post_id );

	return is_string( $url ) ? esc_url_raw( trim( $url ) ) : '';
}

/**
 * Get formatted video duration.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function rpt_get_video_duration( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();

	if ( ! $post_id || ! function_exists( 'get_field' ) ) {
		return '';
	}

	$duration = get_field( 'video_duration', $post_id );

	return is_string( $duration ) ? trim( $duration ) : '';
}

/**
 * Whether video is marked featured.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function rpt_video_is_featured( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();

	if ( ! $post_id || ! function_exists( 'get_field' ) ) {
		return false;
	}

	return (bool) get_field( 'video_is_featured', $post_id );
}

/**
 * Whether video is marked popular.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function rpt_video_is_popular( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();

	if ( ! $post_id || ! function_exists( 'get_field' ) ) {
		return false;
	}

	return (bool) get_field( 'video_is_popular', $post_id );
}
