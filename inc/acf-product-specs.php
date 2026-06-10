<?php
/**
 * ACF — Product technical specifications.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register local ACF field group for WooCommerce products.
 */
function rpt_register_product_technical_specs_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_rpt_product_technical_specs',
			'title'                 => __( 'Thông số kỹ thuật sản phẩm', 'generatepress_child' ),
			'fields'                => array(
				array(
					'key'          => 'field_rpt_technical_specs',
					'label'        => __( 'Thông số kỹ thuật', 'generatepress_child' ),
					'name'         => 'technical_specs',
					'type'         => 'repeater',
					'layout'       => 'table',
					'button_label' => 'Thêm thông số',
					'min'          => 0,
					'max'          => 0,
					'sub_fields'   => array(
						array(
							'key'           => 'field_rpt_spec_label',
							'label'         => 'Tên thông số',
							'name'          => 'spec_label',
							'type'          => 'text',
							'placeholder'   => __( 'Ví dụ: Loại pin', 'generatepress_child' ),
							'parent'        => 'field_rpt_technical_specs',
							'wrapper'       => array(
								'width' => '40',
							),
						),
						array(
							'key'           => 'field_rpt_spec_value',
							'label'         => 'Giá trị thông số',
							'name'          => 'spec_value',
							'type'          => 'textarea',
							'rows'          => 2,
							'placeholder'   => __( 'Ví dụ: LiFePO4 lithium iron phosphate', 'generatepress_child' ),
							'new_lines'     => 'br',
							'parent'        => 'field_rpt_technical_specs',
							'wrapper'       => array(
								'width' => '60',
							),
						),
					),
				),
				array(
					'key'           => 'field_rpt_product_highlight_text',
					'label'         => __( 'Điểm nổi bật', 'generatepress_child' ),
					'name'          => 'product_highlight_text',
					'type'          => 'textarea',
					'rows'          => 3,
					'placeholder'   => '50ah lưu trữ năng lượng điện áp cao, Lưu trữ năng lượng cao cấp 100v, ip20 ups lưu trữ năng lượng',
					'new_lines'     => 'br',
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
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
		)
	);
}
add_action( 'acf/init', 'rpt_register_product_technical_specs_fields' );

/**
 * Sanitized technical spec rows for a product.
 *
 * @param int|null $product_id Product ID.
 * @return array<int, array{label: string, value: string}>
 */
function rpt_get_product_technical_spec_rows( $product_id = null ) {
	if ( ! function_exists( 'get_field' ) ) {
		return array();
	}

	$product_id = $product_id ? (int) $product_id : get_the_ID();

	if ( ! $product_id ) {
		return array();
	}

	$rows  = get_field( 'technical_specs', $product_id );
	$specs = array();

	if ( ! is_array( $rows ) ) {
		return $specs;
	}

	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$label = isset( $row['spec_label'] ) ? trim( (string) $row['spec_label'] ) : '';
		$value = isset( $row['spec_value'] ) ? trim( (string) $row['spec_value'] ) : '';

		if ( '' === $label || '' === $value ) {
			continue;
		}

		$specs[] = array(
			'label' => $label,
			'value' => $value,
		);
	}

	return $specs;
}

/**
 * Product highlight text from ACF.
 *
 * @param int|null $product_id Product ID.
 * @return string
 */
function rpt_get_product_highlight_text( $product_id = null ) {
	if ( ! function_exists( 'get_field' ) ) {
		return '';
	}

	$product_id = $product_id ? (int) $product_id : get_the_ID();

	if ( ! $product_id ) {
		return '';
	}

	return trim( (string) get_field( 'product_highlight_text', $product_id ) );
}

/**
 * Whether a product has specs or highlight content to display.
 *
 * @param int|null $product_id Product ID.
 * @return bool
 */
function rpt_product_has_technical_specs( $product_id = null ) {
	return ! empty( rpt_get_product_technical_spec_rows( $product_id ) )
		|| '' !== rpt_get_product_highlight_text( $product_id );
}

/**
 * Split spec rows into two columns for desktop layout.
 *
 * @param array<int, array{label: string, value: string}> $rows Spec rows.
 * @return array{0: array<int, array{label: string, value: string}>, 1: array<int, array{label: string, value: string}>}
 */
function rpt_split_technical_spec_columns( array $rows ) {
	$count   = count( $rows );
	$split_at = (int) ceil( $count / 2 );

	return array(
		array_slice( $rows, 0, $split_at ),
		array_slice( $rows, $split_at ),
	);
}

/**
 * Format a spec value for frontend output.
 *
 * @param string $value Raw value.
 * @return string
 */
function rpt_format_spec_value( $value ) {
	return wp_kses_post( nl2br( esc_html( $value ) ) );
}

