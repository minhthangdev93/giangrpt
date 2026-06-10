<?php
/**
 * Contact page helpers and layout detection.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

define( 'RPT_CONTACT_PAGE_TEMPLATE', 'page-templates/template-contact.php' );

/**
 * Whether current page uses the contact template.
 *
 * @return bool
 */
function rpt_is_contact_page() {
	return is_page_template( RPT_CONTACT_PAGE_TEMPLATE );
}

/**
 * Contact page ACF field with optional fallback.
 *
 * @param string $field   Field name.
 * @param mixed  $default Default value.
 * @return mixed
 */
function rpt_get_contact_field( $field, $default = '' ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}

	$post_id = get_queried_object_id();
	$value   = get_field( $field, $post_id );

	if ( null === $value || false === $value || '' === $value ) {
		return $default;
	}

	return $value;
}

/**
 * Contact page hero title.
 *
 * @return string
 */
function rpt_get_contact_page_title() {
	$title = rpt_get_contact_field( 'contact_page_title', '' );

	if ( is_string( $title ) && '' !== trim( $title ) ) {
		return trim( $title );
	}

	return __( 'Liên hệ đội ngũ của chúng tôi', 'generatepress_child' );
}

/**
 * Inquiry form section title.
 *
 * @return string
 */
function rpt_get_contact_form_title() {
	$title = rpt_get_contact_field( 'contact_form_title', '' );

	if ( is_string( $title ) && '' !== trim( $title ) ) {
		return trim( $title );
	}

	return __( 'Yêu cầu Đặt giá', 'generatepress_child' );
}

/**
 * Inquiry form intro text.
 *
 * @return string
 */
function rpt_get_contact_form_intro() {
	$intro = rpt_get_contact_field( 'contact_form_intro', '' );

	if ( is_string( $intro ) && '' !== trim( $intro ) ) {
		return trim( $intro );
	}

	return __(
		'Vui lòng sử dụng biểu mẫu liên hệ trực tuyến bên dưới nếu bạn có bất kỳ câu hỏi nào, đội ngũ của chúng tôi sẽ phản hồi sớm nhất có thể.',
		'generatepress_child'
	);
}

/**
 * Location section title.
 *
 * @return string
 */
function rpt_get_contact_location_title() {
	$title = rpt_get_contact_field( 'contact_location_title', '' );

	if ( is_string( $title ) && '' !== trim( $title ) ) {
		return trim( $title );
	}

	return __( 'Vị trí của chúng tôi', 'generatepress_child' );
}

/**
 * Contact rep display name.
 *
 * @return string
 */
function rpt_get_contact_rep_name() {
	$name = rpt_get_contact_field( 'contact_rep_name', '' );

	if ( is_string( $name ) && '' !== trim( $name ) ) {
		return trim( $name );
	}

	return rpt_get_shop_contact_rep_name();
}

/**
 * Contact rep avatar URL.
 *
 * @return string
 */
function rpt_get_contact_rep_avatar_url() {
	$image = rpt_get_contact_field( 'contact_rep_photo', null );

	if ( is_array( $image ) && ! empty( $image['url'] ) ) {
		return esc_url( $image['url'] );
	}

	if ( is_numeric( $image ) ) {
		$url = wp_get_attachment_image_url( (int) $image, 'thumbnail' );

		if ( $url ) {
			return esc_url( $url );
		}
	}

	return '';
}

/**
 * Contact rows for rep card.
 *
 * @return array<int, array{key: string, label: string, value: string, url: string}>
 */
function rpt_get_contact_rep_rows() {
	if ( function_exists( 'rpt_get_product_contact_rows' ) ) {
		return rpt_get_product_contact_rows();
	}

	return rpt_get_contact_channels();
}

/**
 * Google Maps embed HTML for location section.
 *
 * @return string
 */
function rpt_get_contact_map_embed() {
	$embed = rpt_get_contact_field( 'contact_map_embed', '' );

	if ( is_string( $embed ) && '' !== trim( $embed ) ) {
		return trim( $embed );
	}

	$address = rpt_get_site_info( 'address' );

	if ( ! $address ) {
		return '';
	}

	$query = rawurlencode( $address );
	$src   = 'https://maps.google.com/maps?q=' . $query . '&output=embed';

	return sprintf(
		'<iframe src="%1$s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="%2$s"></iframe>',
		esc_url( $src ),
		esc_attr__( 'Bản đồ vị trí công ty', 'generatepress_child' )
	);
}

/**
 * Render contact page breadcrumb.
 */
function rpt_render_contact_breadcrumb() {
	rpt_render_breadcrumb(
		array(
			array(
				'label' => __( 'Trang chủ', 'generatepress_child' ),
				'url'   => home_url( '/' ),
			),
			array(
				'label' => __( 'Liên hệ', 'generatepress_child' ),
			),
		)
	);
}

/**
 * Inquiry flash notice after form submit.
 *
 * @return array{status: string, message: string}|null
 */
function rpt_get_inquiry_flash_notice() {
	if ( empty( $_GET['inquiry'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return null;
	}

	$status = sanitize_key( wp_unslash( $_GET['inquiry'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( 'success' === $status ) {
		return array(
			'status'  => 'success',
			'message' => __( 'Cảm ơn bạn! Yêu cầu đã được gửi thành công. Chúng tôi sẽ phản hồi sớm nhất có thể.', 'generatepress_child' ),
		);
	}

	if ( 'error' === $status ) {
		$code = isset( $_GET['code'] ) ? sanitize_key( wp_unslash( $_GET['code'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$messages = array(
			'nonce'     => __( 'Phiên làm việc đã hết hạn. Vui lòng thử lại.', 'generatepress_child' ),
			'email'     => __( 'Vui lòng nhập địa chỉ e-mail hợp lệ.', 'generatepress_child' ),
			'message'   => __( 'Vui lòng nhập nội dung yêu cầu.', 'generatepress_child' ),
			'files'     => __( 'Một hoặc nhiều tệp đính kèm không hợp lệ.', 'generatepress_child' ),
			'file_size' => __( 'Mỗi tệp đính kèm không được vượt quá 10MB.', 'generatepress_child' ),
			'file_count'=> __( 'Bạn chỉ có thể tải lên tối đa 5 tệp.', 'generatepress_child' ),
			'mail'      => __( 'Không thể gửi yêu cầu lúc này. Vui lòng thử lại sau.', 'generatepress_child' ),
		);

		return array(
			'status'  => 'error',
			'message' => isset( $messages[ $code ] ) ? $messages[ $code ] : __( 'Không thể gửi yêu cầu. Vui lòng kiểm tra lại thông tin.', 'generatepress_child' ),
		);
	}

	return null;
}

/**
 * Force full-width layout on contact page.
 *
 * @param string $layout Sidebar layout slug.
 * @return string
 */
function rpt_contact_sidebar_layout( $layout ) {
	if ( rpt_is_contact_page() ) {
		return 'no-sidebar';
	}

	return $layout;
}
add_filter( 'generate_sidebar_layout', 'rpt_contact_sidebar_layout' );

/**
 * Body classes for contact page.
 *
 * @param array $classes Body classes.
 * @return array
 */
function rpt_contact_body_class( $classes ) {
	if ( ! rpt_is_contact_page() ) {
		return $classes;
	}

	$classes[] = 'rpt-contact-page-body';
	$classes[] = 'full-width-content';
	$classes[] = 'no-sidebar';

	$remove = array( 'right-sidebar', 'left-sidebar', 'both-sidebars', 'both-left', 'both-right' );

	return array_values( array_diff( $classes, $remove ) );
}
add_filter( 'body_class', 'rpt_contact_body_class', 20 );

/**
 * Use contact page as default inquiry URL when available.
 *
 * @param string $url Default inquiry URL.
 * @return string
 */
function rpt_contact_inquiry_url( $url ) {
	$contact = get_pages(
		array(
			'meta_key'   => '_wp_page_template',
			'meta_value' => RPT_CONTACT_PAGE_TEMPLATE,
			'number'     => 1,
		)
	);

	if ( ! empty( $contact[0] ) ) {
		return get_permalink( $contact[0] );
	}

	return $url;
}
add_filter( 'rpt_inquiry_url', 'rpt_contact_inquiry_url', 15 );
