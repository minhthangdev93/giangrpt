<?php
/**
 * Shared site information — single source of truth.
 *
 * Aliases (no duplicate storage):
 * - skype  → same as phone
 * - wechat → same as whatsapp (+852 display format)
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

define( 'RPT_SITE_INFO_OPTION', 'rpt_site_info' );

/**
 * Default company and contact details.
 *
 * @return array<string, string>
 */
function rpt_get_site_info_defaults() {
	return array(
		'company_name'        => 'Shenzhen Renergy Power Technology Co., Ltd.',
		'company_description' => 'Một nhà cung cấp giải pháp lưu trữ năng lượng pin lithium ion hàng đầu thế giới',
		'address'             => 'Công viên công nghiệp Wandajie, số 1-12, Đại lộ Jinlong, quận Pingshan, Shenzhen, Guangdong, Trung Quốc, 518118',
		'phone'               => '+86 18129948166',
		'fax'                 => '86-755-8992-2558',
		'email'               => 'info@rpt-power.com',
		'working_hours'       => '9:00-18:00',
		'whatsapp'            => '85252269280',
		'footer_cta_url'       => '',
		'footer_bg_url'        => '',
		'header_contact_label' => 'Liên hệ với chúng tôi',
		'header_contact_url'   => '',
		'header_quote_label'   => 'Yêu cầu Đặt giá',
		'header_quote_url'     => '',
		'shop_contact_rep'     => '',
		'shop_cta_label'       => 'Yêu cầu ngay bây giờ',
		'shop_cta_url'         => '',
	);
}

/**
 * Product hub — contact rep / department label in sidebar.
 *
 * @return string
 */
function rpt_get_shop_contact_rep_name() {
	$custom = rpt_get_site_info( 'shop_contact_rep' );

	if ( $custom ) {
		return $custom;
	}

	/**
	 * Filter product hub sidebar contact rep name.
	 *
	 * @param string $name Display name.
	 */
	return apply_filters( 'rpt_shop_contact_rep_name', get_bloginfo( 'name' ) );
}

/**
 * Product hub — primary CTA button label.
 *
 * @return string
 */
function rpt_get_shop_hub_cta_label() {
	$custom = rpt_get_site_info( 'shop_cta_label' );

	if ( $custom ) {
		return $custom;
	}

	return __( 'Yêu cầu ngay bây giờ', 'generatepress_child' );
}

/**
 * Product card CTA label (compact — fits one line on desktop).
 *
 * @return string
 */
function rpt_get_product_card_cta_label() {
	/**
	 * Filter product card CTA label on the shop hub grid.
	 *
	 * @param string $label Button label.
	 */
	return apply_filters( 'rpt_product_card_cta_label', __( 'Yêu cầu báo giá', 'generatepress_child' ) );
}

/**
 * Product hub — primary CTA button URL.
 *
 * @return string
 */
function rpt_get_shop_hub_cta_url() {
	$custom = rpt_get_site_info( 'shop_cta_url' );

	if ( $custom ) {
		return $custom;
	}

	return rpt_get_inquiry_url();
}

/**
 * Footer background image URL.
 *
 * @return string
 */
function rpt_get_footer_background_url() {
	$custom = rpt_get_site_info( 'footer_bg_url' );

	if ( $custom ) {
		return esc_url( $custom );
	}

	/**
	 * Filter default footer background image URL.
	 *
	 * @param string $url Default background image URL.
	 */
	return apply_filters(
		'rpt_footer_background_url',
		content_url( '/uploads/2026/06/bg-footer.jpg' )
	);
}

/**
 * Contact page URL fallback.
 *
 * @return string
 */
function rpt_get_contact_url() {
	$contact_page = get_page_by_path( 'contact' );

	if ( ! $contact_page ) {
		$contact_page = get_page_by_path( 'lien-he' );
	}

	$url = $contact_page ? get_permalink( $contact_page ) : home_url( '/contact/' );

	/**
	 * Filter contact page URL.
	 *
	 * @param string $url Contact page URL.
	 */
	return apply_filters( 'rpt_contact_url', $url );
}

/**
 * Footer CTA button URL.
 *
 * @return string
 */
function rpt_get_footer_cta_url() {
	$custom = rpt_get_site_info( 'footer_cta_url' );

	if ( $custom ) {
		return esc_url( $custom );
	}

	$inquiry = rpt_get_inquiry_url();

	if ( $inquiry && home_url( '/' ) !== $inquiry ) {
		return $inquiry;
	}

	return rpt_get_contact_url();
}

/**
 * Footer contact rows (address, phone, fax, email, hours).
 *
 * @return array<int, array<string, string>>
 */
function rpt_get_footer_contact_rows() {
	$rows = array(
		array(
			'key'   => 'address',
			'label' => __( 'Địa chỉ', 'generatepress_child' ),
			'value' => rpt_get_site_info( 'address' ),
			'url'   => '',
		),
		array(
			'key'   => 'phone',
			'label' => __( 'Điện thoại', 'generatepress_child' ),
			'value' => rpt_get_site_info( 'phone' ),
			'url'   => rpt_get_site_info_link( 'phone' ),
		),
		array(
			'key'   => 'fax',
			'label' => __( 'Số fax', 'generatepress_child' ),
			'value' => rpt_get_site_info( 'fax' ),
			'url'   => '',
		),
		array(
			'key'   => 'email',
			'label' => __( 'E-mail', 'generatepress_child' ),
			'value' => rpt_get_site_info( 'email' ),
			'url'   => rpt_get_site_info_link( 'email' ),
		),
		array(
			'key'   => 'working_hours',
			'label' => __( 'Thời gian làm việc', 'generatepress_child' ),
			'value' => rpt_get_site_info( 'working_hours' ),
			'url'   => '',
		),
	);

	return array_values(
		array_filter(
			$rows,
			static function ( $row ) {
				return ! empty( $row['value'] );
			}
		)
	);
}

/**
 * Field aliases resolved at read time.
 *
 * @return array<string, string>
 */
function rpt_get_site_info_aliases() {
	return array(
		'skype'  => 'phone',
		'wechat' => 'whatsapp',
	);
}

/**
 * All site info (saved options merged with defaults).
 *
 * @return array<string, string>
 */
function rpt_get_all_site_info() {
	$defaults = rpt_get_site_info_defaults();
	$saved    = get_option( RPT_SITE_INFO_OPTION, array() );

	if ( ! is_array( $saved ) ) {
		$saved = array();
	}

	$info = wp_parse_args( $saved, $defaults );

	/**
	 * Filter all site information values.
	 *
	 * @param array<string, string> $info     Merged site info.
	 * @param array<string, string> $defaults Default values.
	 */
	return apply_filters( 'rpt_site_info', $info, $defaults );
}

/**
 * Get a single site info value.
 *
 * @param string $key     Field key.
 * @param string $default Fallback if empty.
 * @return string
 */
function rpt_get_site_info( $key, $default = '' ) {
	$aliases = rpt_get_site_info_aliases();

	$original_key = $key;

	if ( isset( $aliases[ $key ] ) ) {
		$key = $aliases[ $key ];
	}

	$info = rpt_get_all_site_info();

	if ( ! isset( $info[ $key ] ) || '' === $info[ $key ] ) {
		return $default;
	}

	$value = $info[ $key ];

	if ( 'wechat' === $original_key ) {
		return rpt_format_wechat_display( $value );
	}

	if ( 'skype' === $original_key ) {
		return $info['phone'];
	}

	return $value;
}

/**
 * WeChat display format (+852…).
 *
 * @param string $number WhatsApp / WeChat number digits.
 * @return string
 */
function rpt_format_wechat_display( $number ) {
	$digits = preg_replace( '/\D+/', '', $number );

	if ( '' === $digits ) {
		return '';
	}

	if ( 0 === strpos( $digits, '852' ) ) {
		return '+' . $digits;
	}

	return '+852' . $digits;
}

/**
 * Strip non-digits for tel:/skype: links.
 *
 * @param string $number Phone or fax string.
 * @return string
 */
function rpt_normalize_phone_digits( $number ) {
	return preg_replace( '/\D+/', '', $number );
}

/**
 * Get contact link URL for a channel.
 *
 * @param string $key email|phone|fax|whatsapp|skype|wechat
 * @return string
 */
function rpt_get_site_info_link( $key ) {
	switch ( $key ) {
		case 'email':
			$email = rpt_get_site_info( 'email' );
			return $email ? 'mailto:' . sanitize_email( $email ) : '';

		case 'phone':
			$digits = rpt_normalize_phone_digits( rpt_get_site_info( 'phone' ) );
			return $digits ? 'tel:+' . $digits : '';

		case 'fax':
			$digits = rpt_normalize_phone_digits( rpt_get_site_info( 'fax' ) );
			return $digits ? 'tel:+' . $digits : '';

		case 'whatsapp':
			$digits = rpt_normalize_phone_digits( rpt_get_site_info( 'whatsapp' ) );
			return $digits ? 'https://wa.me/' . $digits : '';

		case 'skype':
			$digits = rpt_normalize_phone_digits( rpt_get_site_info( 'phone' ) );
			return $digits ? 'skype:+' . $digits . '?call' : '';

		case 'wechat':
			return '';

		default:
			return '';
	}
}

/**
 * Contact channels for footer / contact blocks.
 *
 * @return array<int, array<string, string>>
 */
function rpt_get_contact_channels() {
	$channels = array(
		array(
			'key'   => 'email',
			'label' => __( 'E-mail', 'generatepress_child' ),
			'value' => rpt_get_site_info( 'email' ),
			'url'   => rpt_get_site_info_link( 'email' ),
		),
		array(
			'key'   => 'phone',
			'label' => __( 'Điện thoại', 'generatepress_child' ),
			'value' => rpt_get_site_info( 'phone' ),
			'url'   => rpt_get_site_info_link( 'phone' ),
		),
		array(
			'key'   => 'fax',
			'label' => __( 'Số fax', 'generatepress_child' ),
			'value' => rpt_get_site_info( 'fax' ),
			'url'   => '',
		),
		array(
			'key'   => 'whatsapp',
			'label' => __( 'WhatsApp', 'generatepress_child' ),
			'value' => rpt_get_site_info( 'whatsapp' ),
			'url'   => rpt_get_site_info_link( 'whatsapp' ),
		),
		array(
			'key'   => 'skype',
			'label' => __( 'Skype', 'generatepress_child' ),
			'value' => rpt_get_site_info( 'skype' ),
			'url'   => rpt_get_site_info_link( 'skype' ),
		),
		array(
			'key'   => 'wechat',
			'label' => __( 'WeChat', 'generatepress_child' ),
			'value' => rpt_get_site_info( 'wechat' ),
			'url'   => '',
		),
	);

	return array_values(
		array_filter(
			$channels,
			static function ( $channel ) {
				return ! empty( $channel['value'] );
			}
		)
	);
}

/**
 * Primary inquiry recipient email.
 *
 * @return string
 */
function rpt_get_inquiry_email() {
	$admin_email = sanitize_email( get_option( 'admin_email' ) );

	/**
	 * Filter inquiry form recipient email.
	 *
	 * @param string $email Recipient email address.
	 */
	return apply_filters( 'rpt_inquiry_email', $admin_email );
}
