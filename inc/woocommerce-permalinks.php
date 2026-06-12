<?php
/**
 * WooCommerce permalinks — standard product/category URL bases.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

define( 'RPT_WOO_PERMALINKS_VERSION', '3' );

/**
 * Default WooCommerce permalink settings (with taxonomy/post type bases).
 *
 * @return array<string, mixed>
 */
function rpt_get_standard_woocommerce_permalinks() {
	return array(
		'product_base'           => 'product',
		'category_base'          => 'product-category',
		'tag_base'               => 'product-tag',
		'attribute_base'         => '',
		'use_verbose_page_rules' => false,
	);
}

/**
 * Restore standard WooCommerce URL bases after flat-permalink experiment.
 *
 * Category example: /product-category/pin-luu-tru-dien-mat-troi/
 * Product example:   /product/ten-san-pham/
 */
function rpt_maybe_restore_standard_woocommerce_permalinks() {
	if ( get_option( 'rpt_woo_permalinks_version' ) === RPT_WOO_PERMALINKS_VERSION ) {
		return;
	}

	$permalinks = (array) get_option( 'woocommerce_permalinks', array() );
	$defaults   = rpt_get_standard_woocommerce_permalinks();

	foreach ( $defaults as $key => $value ) {
		$permalinks[ $key ] = $value;
	}

	update_option( 'woocommerce_permalinks', $permalinks );
	flush_rewrite_rules( false );
	update_option( 'rpt_woo_permalinks_version', RPT_WOO_PERMALINKS_VERSION );
}
add_action( 'init', 'rpt_maybe_restore_standard_woocommerce_permalinks', 99 );

/**
 * Re-apply permalink defaults on theme switch.
 */
function rpt_restore_standard_woocommerce_permalinks_on_switch() {
	delete_option( 'rpt_woo_permalinks_version' );
	rpt_maybe_restore_standard_woocommerce_permalinks();
}
add_action( 'after_switch_theme', 'rpt_restore_standard_woocommerce_permalinks_on_switch' );
