<?php
/**
 * Blog posts index — same layout as news page template.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

get_header();

get_template_part( 'template-parts/news/news', 'page' );

get_footer();
