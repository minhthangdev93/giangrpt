<?php
/**
 * ACF — Contact page settings.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register local ACF field group for the contact page.
 */
function rpt_register_contact_page_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_rpt_contact_page_settings',
			'title'                 => __( 'Cài đặt trang Liên hệ', 'generatepress_child' ),
			'fields'                => array(
				array(
					'key'          => 'field_rpt_contact_page_title',
					'label'        => __( 'Tiêu đề trang', 'generatepress_child' ),
					'name'         => 'contact_page_title',
					'type'         => 'text',
					'placeholder'  => __( 'Liên hệ đội ngũ của chúng tôi', 'generatepress_child' ),
				),
				array(
					'key'          => 'field_rpt_contact_rep_name',
					'label'        => __( 'Tên người liên hệ', 'generatepress_child' ),
					'name'         => 'contact_rep_name',
					'type'         => 'text',
					'placeholder'  => 'RPT-Barry',
					'instructions' => __( 'Để trống sẽ dùng tên đại diện từ Cài đặt trang web.', 'generatepress_child' ),
				),
				array(
					'key'           => 'field_rpt_contact_rep_photo',
					'label'         => __( 'Ảnh đại diện', 'generatepress_child' ),
					'name'          => 'contact_rep_photo',
					'type'          => 'image',
					'return_format' => 'array',
					'preview_size'  => 'thumbnail',
					'library'       => 'all',
				),
				array(
					'key'         => 'field_rpt_contact_form_title',
					'label'       => __( 'Tiêu đề biểu mẫu', 'generatepress_child' ),
					'name'        => 'contact_form_title',
					'type'        => 'text',
					'placeholder' => __( 'Yêu cầu Đặt giá', 'generatepress_child' ),
				),
				array(
					'key'       => 'field_rpt_contact_form_intro',
					'label'     => __( 'Mô tả biểu mẫu', 'generatepress_child' ),
					'name'      => 'contact_form_intro',
					'type'      => 'textarea',
					'rows'      => 3,
					'new_lines' => 'br',
				),
				array(
					'key'          => 'field_rpt_contact_location_title',
					'label'        => __( 'Tiêu đề phần vị trí', 'generatepress_child' ),
					'name'         => 'contact_location_title',
					'type'         => 'text',
					'placeholder'  => __( 'Vị trí của chúng tôi', 'generatepress_child' ),
				),
				array(
					'key'          => 'field_rpt_contact_map_embed',
					'label'        => __( 'Mã nhúng bản đồ', 'generatepress_child' ),
					'name'         => 'contact_map_embed',
					'type'         => 'textarea',
					'rows'         => 4,
					'instructions' => __( 'Dán iframe Google Maps. Để trống sẽ tự tạo từ địa chỉ công ty.', 'generatepress_child' ),
				),
			),
			'location'              => rpt_get_contact_page_acf_locations(),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
		)
	);
}
add_action( 'acf/init', 'rpt_register_contact_page_fields' );

/**
 * ACF location rules for the contact page.
 *
 * @return array<int, array<int, array<string, string>>>
 */
function rpt_get_contact_page_acf_locations() {
	$locations = array(
		array(
			array(
				'param'    => 'page_template',
				'operator' => '==',
				'value'    => RPT_CONTACT_PAGE_TEMPLATE,
			),
		),
	);

	foreach ( array( 'lien-he', 'contact' ) as $slug ) {
		$page = get_page_by_path( $slug );

		if ( $page instanceof WP_Post ) {
			$locations[] = array(
				array(
					'param'    => 'page',
					'operator' => '==',
					'value'    => (string) $page->ID,
				),
			);
		}
	}

	return $locations;
}
