<?php
/**
 * Template Name: Videos
 * Template Post Type: page
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) {
	the_post();
	get_template_part( 'template-parts/video/video', 'hub' );
}

get_footer();
