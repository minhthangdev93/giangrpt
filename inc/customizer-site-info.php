<?php
/**
 * Customizer — RPT Company Info.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register Customizer settings for shared site info.
 *
 * @param WP_Customize_Manager $wp_customize Customizer instance.
 */
function rpt_customize_register_site_info( $wp_customize ) {
	$wp_customize->add_section(
		'rpt_site_info',
		array(
			'title'       => __( 'RPT Company Info', 'generatepress_child' ),
			'description' => __( 'Thông tin công ty dùng chung trên toàn site. Skype = Điện thoại, WeChat = WhatsApp. Menu header: chỉnh tại Giao diện → Menu → RPT Primary Navigation (dropdown = kéo submenu bên dưới mục cha).', 'generatepress_child' ),
			'priority'    => 30,
		)
	);

	$fields = array(
		'company_name'        => array(
			'label' => __( 'Tên công ty', 'generatepress_child' ),
			'type'  => 'text',
		),
		'company_description' => array(
			'label' => __( 'Mô tả ngắn công ty (footer)', 'generatepress_child' ),
			'type'  => 'textarea',
		),
		'address'             => array(
			'label' => __( 'Địa chỉ', 'generatepress_child' ),
			'type'  => 'textarea',
		),
		'phone'               => array(
			'label' => __( 'Điện thoại (dùng cho Skype)', 'generatepress_child' ),
			'type'  => 'text',
		),
		'fax'                 => array(
			'label' => __( 'Số fax', 'generatepress_child' ),
			'type'  => 'text',
		),
		'email'               => array(
			'label' => __( 'E-mail', 'generatepress_child' ),
			'type'  => 'text',
		),
		'working_hours'       => array(
			'label' => __( 'Thời gian làm việc', 'generatepress_child' ),
			'type'  => 'text',
		),
		'whatsapp'            => array(
			'label' => __( 'WhatsApp / WeChat', 'generatepress_child' ),
			'type'  => 'text',
		),
		'footer_cta_url'      => array(
			'label' => __( 'Footer CTA URL (để trống = inquiry/contact)', 'generatepress_child' ),
			'type'  => 'url',
		),
		'footer_bg_url'       => array(
			'label' => __( 'Footer background URL (để trống = ảnh mặc định)', 'generatepress_child' ),
			'type'  => 'url',
		),
		'header_contact_label' => array(
			'label' => __( 'Header — nhãn Liên hệ', 'generatepress_child' ),
			'type'  => 'text',
		),
		'header_contact_url'   => array(
			'label' => __( 'Header — URL Liên hệ (để trống = trang Contact)', 'generatepress_child' ),
			'type'  => 'url',
		),
		'header_quote_label'   => array(
			'label' => __( 'Header — nhãn Yêu cầu Đặt giá', 'generatepress_child' ),
			'type'  => 'text',
		),
		'header_quote_url'     => array(
			'label' => __( 'Header — URL Yêu cầu Đặt giá (để trống = inquiry)', 'generatepress_child' ),
			'type'  => 'url',
		),
		'shop_contact_rep'     => array(
			'label' => __( 'Trang sản phẩm — tên nhân viên / bộ phận (sidebar)', 'generatepress_child' ),
			'type'  => 'text',
		),
		'shop_cta_label'       => array(
			'label' => __( 'Trang sản phẩm — nhãn nút CTA', 'generatepress_child' ),
			'type'  => 'text',
		),
		'shop_cta_url'         => array(
			'label' => __( 'Trang sản phẩm — URL nút CTA (để trống = inquiry)', 'generatepress_child' ),
			'type'  => 'url',
		),
	);

	$defaults = rpt_get_site_info_defaults();

	foreach ( $fields as $key => $field ) {
		$sanitize = ( 'textarea' === $field['type'] ) ? 'rpt_sanitize_site_info_textarea' : 'rpt_sanitize_site_info_field';

		if ( 'url' === $field['type'] ) {
			$sanitize = 'esc_url_raw';
		}

		$wp_customize->add_setting(
			RPT_SITE_INFO_OPTION . '[' . $key . ']',
			array(
				'type'              => 'option',
				'default'           => isset( $defaults[ $key ] ) ? $defaults[ $key ] : '',
				'sanitize_callback' => $sanitize,
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			RPT_SITE_INFO_OPTION . '[' . $key . ']',
			array(
				'label'   => $field['label'],
				'section' => 'rpt_site_info',
				'type'    => 'url' === $field['type'] ? 'url' : $field['type'],
			)
		);
	}
}
add_action( 'customize_register', 'rpt_customize_register_site_info' );

/**
 * Sanitize a site info field value.
 *
 * @param string $value Raw value.
 * @return string
 */
function rpt_sanitize_site_info_field( $value ) {
	if ( is_email( $value ) ) {
		return sanitize_email( $value );
	}

	return sanitize_text_field( $value );
}

/**
 * Sanitize multiline site info fields.
 *
 * @param string $value Raw value.
 * @return string
 */
function rpt_sanitize_site_info_textarea( $value ) {
	return sanitize_textarea_field( $value );
}
