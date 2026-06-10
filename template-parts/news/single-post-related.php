<?php
/**
 * Related news posts — horizontal scroll.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$related_query = rpt_get_related_news_posts();

if ( ! $related_query->have_posts() ) {
	return;
}
?>
<section class="rpt-related-news" aria-labelledby="rpt-related-news-title">
	<div class="rpt-related-news__header">
		<h2 class="rpt-related-news__title" id="rpt-related-news-title">
			<?php esc_html_e( 'Bài viết liên quan', 'generatepress_child' ); ?>
		</h2>
		<span class="rpt-related-news__accent" aria-hidden="true"></span>
	</div>

	<div class="rpt-related-news__scroller" tabindex="0">
		<div class="rpt-related-news__track">
			<?php
			while ( $related_query->have_posts() ) :
				$related_query->the_post();
				get_template_part( 'template-parts/news/news', 'card' );
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
