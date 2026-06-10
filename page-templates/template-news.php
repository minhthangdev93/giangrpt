<?php
/**
 * Template Name: Tin tức
 * Template Post Type: page
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

get_header();

$news_query = rpt_get_news_page_query();

get_template_part(
	'template-parts/news/news',
	'page',
	array(
		'query' => $news_query,
	)
);

get_footer();
