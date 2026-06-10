<?php
/**
 * Single video detail.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) {
	the_post();
	get_template_part( 'template-parts/video/video', 'single' );
}

get_footer();
