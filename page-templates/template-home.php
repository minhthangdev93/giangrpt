<?php
/**
 * Template Name: Trang chủ
 * Template Post Type: page
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) {
	the_post();
	get_template_part( 'template-parts/home/home', 'page' );
}

get_footer();
