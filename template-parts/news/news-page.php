<?php
/**
 * News archive layout.
 *
 * @package GeneratePress_Child
 *
 * @var array $args Template args.
 */

defined( 'ABSPATH' ) || exit;

$query       = isset( $args['query'] ) && $args['query'] instanceof WP_Query ? $args['query'] : $GLOBALS['wp_query'];
$page_title  = rpt_get_news_page_title();
$custom_query = isset( $args['query'] ) && $args['query'] instanceof WP_Query;
?>
<div class="rpt-news-page">
	<div class="rpt-container rpt-news-page__container">
		<?php rpt_render_news_breadcrumb(); ?>

		<section class="rpt-news-hero">
			<h1 class="rpt-news-hero__title"><?php echo esc_html( $page_title ); ?></h1>
			<span class="rpt-news-hero__accent" aria-hidden="true"></span>
		</section>

		<?php if ( $query->have_posts() ) : ?>
			<div class="rpt-news-grid">
				<?php
				while ( $query->have_posts() ) :
					$query->the_post();
					get_template_part( 'template-parts/news/news', 'card' );
				endwhile;
				?>
			</div>

			<?php rpt_render_news_pagination( $query ); ?>
		<?php else : ?>
			<div class="rpt-news-empty">
				<p><?php esc_html_e( 'Chưa có bài viết nào.', 'generatepress_child' ); ?></p>
			</div>
		<?php endif; ?>

		<?php
		if ( $custom_query ) {
			wp_reset_postdata();
		}
		?>
	</div>
</div>
