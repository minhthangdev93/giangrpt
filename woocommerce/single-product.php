<?php
/**
 * Single product — full-width layout, no sidebar.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 1.6.4
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

do_action( 'woocommerce_before_main_content' );

while ( have_posts() ) {
	the_post();
	wc_get_template_part( 'content', 'single-product' );
}

do_action( 'woocommerce_after_main_content' );

if ( ! ( function_exists( 'rpt_uses_full_width_woo_layout' ) && rpt_uses_full_width_woo_layout() ) ) {
	do_action( 'woocommerce_sidebar' );
}

get_footer( 'shop' );
