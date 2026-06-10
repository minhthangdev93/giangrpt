<?php
/**
 * Front page layout.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		get_template_part( 'template-parts/home/home', 'page' );
	}
} else {
	get_template_part( 'template-parts/home/home', 'page' );
}

get_footer();
