<?php
/**
 * ACF — Product basic information (flexible groups) for single product hero.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register local ACF field group.
 */
function rpt_register_product_basic_info_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_rpt_product_basic_info',
			'title'                 => __( 'Thông tin cơ bản sản phẩm', 'generatepress_child' ),
			'fields'                => array(
				array(
					'key'          => 'field_rpt_product_basic_info_groups',
					'label'        => __( 'Nhóm thông tin', 'generatepress_child' ),
					'name'         => 'product_basic_info_groups',
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => 'Thêm nhóm thông tin',
					'min'          => 0,
					'max'          => 0,
					'sub_fields'   => array(
						array(
							'key'           => 'field_rpt_basic_info_group_title',
							'label'         => 'Tiêu đề nhóm',
							'name'          => 'group_title',
							'type'          => 'text',
							'placeholder'   => 'Ví dụ: Các đặc tính cơ bản',
							'parent'        => 'field_rpt_product_basic_info_groups',
						),
						array(
							'key'          => 'field_rpt_basic_info_group_items',
							'label'        => __( 'Các dòng thông tin', 'generatepress_child' ),
							'name'         => 'group_items',
							'type'         => 'repeater',
							'layout'       => 'table',
							'button_label' => 'Thêm dòng thông tin',
							'parent'       => 'field_rpt_product_basic_info_groups',
							'sub_fields'   => array(
								array(
									'key'         => 'field_rpt_basic_info_item_label',
									'label'       => 'Tên thông tin',
									'name'        => 'item_label',
									'type'        => 'text',
									'placeholder' => 'Ví dụ: Nơi xuất xứ',
									'parent'      => 'field_rpt_basic_info_group_items',
								),
								array(
									'key'         => 'field_rpt_basic_info_item_value',
									'label'       => 'Giá trị',
									'name'        => 'item_value',
									'type'        => 'textarea',
									'rows'        => 2,
									'placeholder' => 'Ví dụ: Trung Quốc',
									'new_lines'   => 'br',
									'parent'      => 'field_rpt_basic_info_group_items',
								),
							),
						),
					),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'product',
					),
				),
			),
			'menu_order'            => 1,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
		)
	);
}
add_action( 'acf/init', 'rpt_register_product_basic_info_fields' );

/**
 * Format a basic info value for frontend output.
 *
 * @param string $value Raw value.
 * @return string
 */
function rpt_format_basic_info_value( $value ) {
	return wp_kses_post( nl2br( esc_html( $value ) ) );
}

/**
 * Sanitized basic info groups for a product.
 *
 * @param int|null $product_id Product ID.
 * @return array<int, array{title: string, items: array<int, array{label: string, value: string}>}>
 */
function rpt_get_product_basic_info_groups( $product_id = null ) {
	if ( ! function_exists( 'get_field' ) ) {
		return array();
	}

	$product_id = $product_id ? (int) $product_id : get_the_ID();

	if ( ! $product_id ) {
		return array();
	}

	$raw_groups = get_field( 'product_basic_info_groups', $product_id );
	$groups     = array();

	if ( ! is_array( $raw_groups ) ) {
		return $groups;
	}

	foreach ( $raw_groups as $raw_group ) {
		if ( ! is_array( $raw_group ) ) {
			continue;
		}

		$title = isset( $raw_group['group_title'] ) ? trim( (string) $raw_group['group_title'] ) : '';
		$items = array();

		if ( ! empty( $raw_group['group_items'] ) && is_array( $raw_group['group_items'] ) ) {
			foreach ( $raw_group['group_items'] as $raw_item ) {
				if ( ! is_array( $raw_item ) ) {
					continue;
				}

				$label = isset( $raw_item['item_label'] ) ? trim( (string) $raw_item['item_label'] ) : '';
				$value = isset( $raw_item['item_value'] ) ? trim( (string) $raw_item['item_value'] ) : '';

				if ( '' === $label || '' === $value ) {
					continue;
				}

				$items[] = array(
					'label' => $label,
					'value' => $value,
				);
			}
		}

		if ( empty( $items ) ) {
			continue;
		}

		$groups[] = array(
			'title' => $title,
			'items' => $items,
		);
	}

	/**
	 * Filter sanitized product basic info groups.
	 *
	 * @param array<int, array{title: string, items: array<int, array{label: string, value: string}>}> $groups     Groups.
	 * @param int                                                                                         $product_id Product ID.
	 */
	return apply_filters( 'rpt_product_basic_info_groups', $groups, $product_id );
}

/**
 * Whether the product has basic info groups to display.
 *
 * @param int|null $product_id Product ID.
 * @return bool
 */
function rpt_product_has_basic_info( $product_id = null ) {
	return ! empty( rpt_get_product_basic_info_groups( $product_id ) );
}
