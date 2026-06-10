<?php
/**
 * Back-compat wrapper — hub product card.
 *
 * @package WooCommerce\Templates
 */

defined( 'ABSPATH' ) || exit;

global $product;

get_template_part(
	'template-parts/product/product',
	'card',
	array(
		'product' => $product,
	)
);
