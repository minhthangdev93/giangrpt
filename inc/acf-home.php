<?php
/**
 * ACF — Homepage settings.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register local ACF field group for the homepage.
 */
function rpt_register_home_page_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_rpt_home_page_settings',
			'title'                 => __( 'Cài đặt Trang chủ', 'generatepress_child' ),
			'fields'                => array(
				array(
					'key'          => 'field_rpt_home_tab_hero',
					'label'        => __( 'Homepage Hero Slider', 'generatepress_child' ),
					'type'         => 'tab',
				),
				array(
					'key'          => 'field_rpt_home_hero_slides',
					'label'        => __( 'Home Hero Slides', 'generatepress_child' ),
					'name'         => 'home_hero_slides',
					'type'         => 'repeater',
					'layout'       => 'row',
					'button_label' => __( 'Thêm slide', 'generatepress_child' ),
					'instructions' => __( 'Upload ảnh banner full width. Text có thể nằm sẵn trong ảnh.', 'generatepress_child' ),
					'sub_fields'   => array(
						array(
							'key'           => 'field_rpt_home_slide_image',
							'label'         => __( 'Ảnh slider', 'generatepress_child' ),
							'name'          => 'slide_image',
							'type'          => 'image',
							'return_format' => 'array',
							'required'      => 1,
							'parent'        => 'field_rpt_home_hero_slides',
						),
						array(
							'key'         => 'field_rpt_home_slide_link',
							'label'       => __( 'Link slide', 'generatepress_child' ),
							'name'        => 'slide_link',
							'type'        => 'text',
							'placeholder' => '/danh-muc-san-pham/thay-the-axit-chi/',
							'instructions'=> __( 'URL đầy đủ hoặc đường dẫn tương đối (bắt đầu bằng /). Để trống nếu slide không click được.', 'generatepress_child' ),
							'parent'      => 'field_rpt_home_hero_slides',
						),
						array(
							'key'           => 'field_rpt_home_slide_link_target',
							'label'         => __( 'Cách mở link', 'generatepress_child' ),
							'name'          => 'slide_link_target',
							'type'          => 'select',
							'choices'       => array(
								'_self'  => __( 'Mở trong tab hiện tại', 'generatepress_child' ),
								'_blank' => __( 'Mở tab mới', 'generatepress_child' ),
							),
							'default_value' => '_self',
							'parent'        => 'field_rpt_home_hero_slides',
						),
					),
				),
				array(
					'key'   => 'field_rpt_home_tab_categories',
					'label' => __( 'Danh mục', 'generatepress_child' ),
					'type'  => 'tab',
				),
				array(
					'key'           => 'field_rpt_home_categories_title',
					'label'         => __( 'Tiêu đề danh mục', 'generatepress_child' ),
					'name'          => 'home_categories_title',
					'type'          => 'text',
					'default_value' => __( 'Lưu trữ năng lượng dân cư & C&I Energy Storage nhà sản xuất', 'generatepress_child' ),
				),
				array(
					'key'          => 'field_rpt_home_category_items',
					'label'        => __( 'Danh mục tùy chỉnh', 'generatepress_child' ),
					'name'         => 'home_category_items',
					'type'         => 'repeater',
					'layout'       => 'row',
					'button_label' => __( 'Thêm danh mục', 'generatepress_child' ),
					'instructions' => __( 'Để trống sẽ tự lấy 6 danh mục sản phẩm cấp 1 có ảnh đại diện.', 'generatepress_child' ),
					'sub_fields'   => array(
						array(
							'key'           => 'field_rpt_home_category_image',
							'label'         => __( 'Icon / ảnh', 'generatepress_child' ),
							'name'          => 'item_image',
							'type'          => 'image',
							'return_format' => 'array',
							'parent'        => 'field_rpt_home_category_items',
						),
						array(
							'key'    => 'field_rpt_home_category_label',
							'label'  => __( 'Tên', 'generatepress_child' ),
							'name'   => 'item_label',
							'type'   => 'text',
							'parent' => 'field_rpt_home_category_items',
						),
						array(
							'key'    => 'field_rpt_home_category_url',
							'label'  => __( 'Liên kết', 'generatepress_child' ),
							'name'   => 'item_url',
							'type'   => 'url',
							'parent' => 'field_rpt_home_category_items',
						),
					),
				),
				array(
					'key'   => 'field_rpt_home_tab_products',
					'label' => __( 'Bán chạy', 'generatepress_child' ),
					'type'  => 'tab',
				),
				array(
					'key'           => 'field_rpt_home_bestsellers_title',
					'label'         => __( 'Tiêu đề sản phẩm bán chạy', 'generatepress_child' ),
					'name'          => 'home_bestsellers_title',
					'type'          => 'text',
					'default_value' => __( 'Bán Chạy', 'generatepress_child' ),
				),
				array(
					'key'           => 'field_rpt_home_bestsellers_limit',
					'label'         => __( 'Số sản phẩm', 'generatepress_child' ),
					'name'          => 'home_bestsellers_limit',
					'type'          => 'number',
					'default_value' => 24,
					'min'           => 1,
					'max'           => 24,
				),
				array(
					'key'          => 'field_rpt_home_bestsellers_products',
					'label'        => __( 'Sản phẩm chọn thủ công', 'generatepress_child' ),
					'name'         => 'home_bestsellers_products',
					'type'         => 'relationship',
					'post_type'    => array( 'product' ),
					'filters'      => array( 'search' ),
					'return_format'=> 'id',
					'min'          => 0,
					'max'          => 24,
					'instructions' => __( 'Để trống sẽ lấy sản phẩm bán chạy theo tag best-seller, ACF is_best_seller hoặc total_sales.', 'generatepress_child' ),
				),
				array(
					'key'           => 'field_rpt_home_bestsellers_more_label',
					'label'         => __( 'Nút xem thêm', 'generatepress_child' ),
					'name'          => 'home_bestsellers_more_label',
					'type'          => 'text',
					'default_value' => __( 'Xem thêm', 'generatepress_child' ),
				),
				array(
					'key'          => 'field_rpt_home_bestsellers_more_url',
					'label'        => __( 'Link xem thêm', 'generatepress_child' ),
					'name'         => 'home_bestsellers_more_url',
					'type'         => 'url',
					'instructions' => __( 'Để trống sẽ dùng trang tag best-seller nếu có.', 'generatepress_child' ),
				),
				array(
					'key'   => 'field_rpt_home_tab_achievements',
					'label' => __( 'Thành tựu', 'generatepress_child' ),
					'type'  => 'tab',
				),
				array(
					'key'           => 'field_rpt_home_achievements_title',
					'label'         => __( 'Tiêu đề thành tựu', 'generatepress_child' ),
					'name'          => 'home_achievements_title',
					'type'          => 'text',
					'default_value' => __( 'Thành tựu của chúng tôi', 'generatepress_child' ),
				),
				array(
					'key'          => 'field_rpt_home_achievements',
					'label'        => __( 'Thành tựu', 'generatepress_child' ),
					'name'         => 'home_achievements',
					'type'         => 'repeater',
					'layout'       => 'table',
					'button_label' => __( 'Thêm thành tựu', 'generatepress_child' ),
					'sub_fields'   => array(
						array(
							'key'           => 'field_rpt_home_achievement_icon',
							'label'         => __( 'Icon', 'generatepress_child' ),
							'name'          => 'achievement_icon',
							'type'          => 'select',
							'choices'       => array(
								'calendar'  => __( 'Lịch', 'generatepress_child' ),
								'team'      => __( 'Nhân sự', 'generatepress_child' ),
								'revenue'   => __( 'Doanh thu', 'generatepress_child' ),
								'customers' => __( 'Khách hàng', 'generatepress_child' ),
							),
							'default_value' => 'calendar',
							'parent'        => 'field_rpt_home_achievements',
						),
						array(
							'key'    => 'field_rpt_home_achievement_value',
							'label'  => __( 'Giá trị', 'generatepress_child' ),
							'name'   => 'achievement_value',
							'type'   => 'text',
							'parent' => 'field_rpt_home_achievements',
						),
						array(
							'key'    => 'field_rpt_home_achievement_label',
							'label'  => __( 'Nhãn', 'generatepress_child' ),
							'name'   => 'achievement_label',
							'type'   => 'text',
							'parent' => 'field_rpt_home_achievements',
						),
					),
				),
				array(
					'key'   => 'field_rpt_home_tab_about',
					'label' => __( 'Về chúng tôi', 'generatepress_child' ),
					'type'  => 'tab',
				),
				array(
					'key'           => 'field_rpt_home_about_title',
					'label'         => __( 'Tiêu đề', 'generatepress_child' ),
					'name'          => 'home_about_title',
					'type'          => 'text',
					'default_value' => __( 'Về chúng tôi', 'generatepress_child' ),
				),
				array(
					'key'           => 'field_rpt_home_about_image',
					'label'         => __( 'Ảnh công ty', 'generatepress_child' ),
					'name'          => 'home_about_image',
					'type'          => 'image',
					'return_format' => 'array',
				),
				array(
					'key'          => 'field_rpt_home_about_tagline',
					'label'        => __( 'Khẩu hiệu', 'generatepress_child' ),
					'name'         => 'home_about_tagline',
					'type'         => 'text',
					'instructions' => __( 'Để trống sẽ dùng mô tả công ty từ Cài đặt trang web.', 'generatepress_child' ),
				),
				array(
					'key'   => 'field_rpt_home_tab_certificates',
					'label' => __( 'Chứng nhận', 'generatepress_child' ),
					'type'  => 'tab',
				),
				array(
					'key'           => 'field_rpt_home_certificates_title',
					'label'         => __( 'Tiêu đề chứng nhận', 'generatepress_child' ),
					'name'          => 'home_certificates_title',
					'type'          => 'text',
					'default_value' => __( 'Giấy chứng nhận của chúng tôi', 'generatepress_child' ),
				),
				array(
					'key'          => 'field_rpt_home_certificates',
					'label'        => __( 'Chứng nhận', 'generatepress_child' ),
					'name'         => 'home_certificates',
					'type'         => 'repeater',
					'layout'       => 'row',
					'button_label' => __( 'Thêm chứng nhận', 'generatepress_child' ),
					'sub_fields'   => array(
						array(
							'key'           => 'field_rpt_home_certificate_image',
							'label'         => __( 'Ảnh', 'generatepress_child' ),
							'name'          => 'certificate_image',
							'type'          => 'image',
							'return_format' => 'array',
							'parent'        => 'field_rpt_home_certificates',
						),
						array(
							'key'    => 'field_rpt_home_certificate_title',
							'label'  => __( 'Tên', 'generatepress_child' ),
							'name'   => 'certificate_title',
							'type'   => 'text',
							'parent' => 'field_rpt_home_certificates',
						),
					),
				),
				array(
					'key'   => 'field_rpt_home_tab_news',
					'label' => __( 'Tin tức', 'generatepress_child' ),
					'type'  => 'tab',
				),
				array(
					'key'           => 'field_rpt_home_news_title',
					'label'         => __( 'Tiêu đề tin tức', 'generatepress_child' ),
					'name'          => 'home_news_title',
					'type'          => 'text',
					'default_value' => __( 'Tin tức mới nhất', 'generatepress_child' ),
				),
				array(
					'key'           => 'field_rpt_home_news_limit',
					'label'         => __( 'Số bài viết', 'generatepress_child' ),
					'name'          => 'home_news_limit',
					'type'          => 'number',
					'default_value' => 9,
					'min'           => 1,
					'max'           => 9,
				),
				array(
					'key'           => 'field_rpt_home_news_button_label',
					'label'         => __( 'Nút xem thêm', 'generatepress_child' ),
					'name'          => 'home_news_button_label',
					'type'          => 'text',
					'default_value' => __( 'Xem thêm tin tức', 'generatepress_child' ),
				),
			),
			'location'              => rpt_get_home_page_acf_locations(),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
		)
	);
}
add_action( 'acf/init', 'rpt_register_home_page_fields' );

/**
 * ACF location rules for the homepage.
 *
 * @return array<int, array<int, array<string, string>>>
 */
function rpt_get_home_page_acf_locations() {
	$locations = array(
		array(
			array(
				'param'    => 'page_type',
				'operator' => '==',
				'value'    => 'front_page',
			),
		),
		array(
			array(
				'param'    => 'page_template',
				'operator' => '==',
				'value'    => RPT_HOME_PAGE_TEMPLATE,
			),
		),
	);

	foreach ( array( 'trang-chu', 'home' ) as $slug ) {
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
