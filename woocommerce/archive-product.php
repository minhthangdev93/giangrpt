<?php
/**
 * Product archives — product hub (shop) or B2B category layout.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

do_action( 'woocommerce_before_main_content' );

if ( function_exists( 'rpt_is_product_hub' ) && rpt_is_product_hub() ) {
	get_template_part( 'template-parts/product/product', 'hub' );
} elseif ( function_exists( 'rpt_is_product_category_archive' ) && rpt_is_product_category_archive() ) {
	get_template_part( 'template-parts/product/product', 'category-archive' );
} else {
	do_action( 'woocommerce_shop_loop_header' );

	if ( woocommerce_product_loop() ) {
		do_action( 'woocommerce_before_shop_loop' );

		woocommerce_product_loop_start();

		if ( wc_get_loop_prop( 'total' ) ) {
			while ( have_posts() ) {
				the_post();
				do_action( 'woocommerce_shop_loop' );
				wc_get_template_part( 'content', 'product' );
			}
		}

		woocommerce_product_loop_end();

		do_action( 'woocommerce_after_shop_loop' );
	} else {
		do_action( 'woocommerce_no_products_found' );
	}
}

do_action( 'woocommerce_after_main_content' );

if ( ! ( function_exists( 'rpt_uses_full_width_woo_layout' ) && rpt_uses_full_width_woo_layout() ) ) {
	do_action( 'woocommerce_sidebar' );
}

get_footer( 'shop' );
