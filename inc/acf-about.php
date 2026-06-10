<?php
/**
 * ACF — About page settings.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register local ACF field group for the about page.
 */
function rpt_register_about_page_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_rpt_about_page_settings',
			'title'                 => __( 'Cài đặt trang Giới thiệu', 'generatepress_child' ),
			'fields'                => array(
				array(
					'key'           => 'field_rpt_about_company_title',
					'label'         => __( 'Tiêu đề công ty', 'generatepress_child' ),
					'name'          => 'about_company_title',
					'type'          => 'text',
					'placeholder'   => 'Shenzhen Renergy Power Technology Co., Ltd.',
					'instructions'  => __( 'Hiển thị làm tiêu đề chính trên trang Giới thiệu. Để trống sẽ dùng tiêu đề trang.', 'generatepress_child' ),
				),
				array(
					'key'           => 'field_rpt_about_company_subtitle',
					'label'         => __( 'Mô tả ngắn', 'generatepress_child' ),
					'name'          => 'about_company_subtitle',
					'type'          => 'textarea',
					'rows'          => 3,
					'new_lines'     => 'br',
					'placeholder'   => __( 'Một nhà cung cấp giải pháp lưu trữ năng lượng pin lithium ion hàng đầu thế giới', 'generatepress_child' ),
				),
				array(
					'key'          => 'field_rpt_about_tabs',
					'label'        => __( 'Tabs giới thiệu', 'generatepress_child' ),
					'name'         => 'about_tabs',
					'type'         => 'repeater',
					'layout'       => 'row',
					'button_label' => __( 'Thêm tab', 'generatepress_child' ),
					'min'          => 0,
					'max'          => 0,
					'sub_fields'   => array(
						array(
							'key'         => 'field_rpt_about_tab_title',
							'label'       => __( 'Tên tab', 'generatepress_child' ),
							'name'        => 'tab_title',
							'type'        => 'text',
							'placeholder' => __( 'Ví dụ: Giới thiệu công ty', 'generatepress_child' ),
							'parent'      => 'field_rpt_about_tabs',
						),
						array(
							'key'          => 'field_rpt_about_tab_content',
							'label'        => __( 'Nội dung tab', 'generatepress_child' ),
							'name'         => 'tab_content',
							'type'         => 'wysiwyg',
							'tabs'         => 'all',
							'toolbar'      => 'full',
							'media_upload' => 1,
							'delay'        => 0,
							'parent'       => 'field_rpt_about_tabs',
						),
					),
				),
				array(
					'key'          => 'field_rpt_about_company_facts',
					'label'        => __( 'Thông tin doanh nghiệp', 'generatepress_child' ),
					'name'         => 'about_company_facts',
					'type'         => 'repeater',
					'layout'       => 'table',
					'button_label' => __( 'Thêm thông tin', 'generatepress_child' ),
					'min'          => 0,
					'max'          => 0,
					'sub_fields'   => array(
						array(
							'key'         => 'field_rpt_about_fact_label',
							'label'       => __( 'Tên thông tin', 'generatepress_child' ),
							'name'        => 'fact_label',
							'type'        => 'text',
							'placeholder' => __( 'Ví dụ: Thị trường chính', 'generatepress_child' ),
							'parent'      => 'field_rpt_about_company_facts',
							'wrapper'     => array(
								'width' => '35',
							),
						),
						array(
							'key'         => 'field_rpt_about_fact_value',
							'label'       => __( 'Giá trị', 'generatepress_child' ),
							'name'        => 'fact_value',
							'type'        => 'textarea',
							'rows'        => 2,
							'new_lines'   => 'br',
							'placeholder' => __( 'Ví dụ: Bắc Mỹ, Nam Mỹ, Tây Âu...', 'generatepress_child' ),
							'parent'      => 'field_rpt_about_company_facts',
							'wrapper'     => array(
								'width' => '65',
							),
						),
					),
				),
			),
			'location'              => rpt_get_about_page_acf_locations(),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
		)
	);
}
add_action( 'acf/init', 'rpt_register_about_page_fields' );

/**
 * ACF location rules for the about page.
 *
 * @return array<int, array<int, array<string, string>>>
 */
function rpt_get_about_page_acf_locations() {
	$locations = array(
		array(
			array(
				'param'    => 'page_template',
				'operator' => '==',
				'value'    => RPT_ABOUT_PAGE_TEMPLATE,
			),
		),
	);

	$page = get_page_by_path( 'gioi-thieu' );

	if ( $page instanceof WP_Post ) {
		$locations[] = array(
			array(
				'param'    => 'page',
				'operator' => '==',
				'value'    => (string) $page->ID,
			),
		);
	}

	return $locations;
}
