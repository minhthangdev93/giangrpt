<?php
/**
 * Back-compat wrapper — product hub category section.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

get_template_part(
	'template-parts/product/product',
	'category-section',
	isset( $args ) ? $args : array()
);
