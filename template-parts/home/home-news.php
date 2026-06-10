<?php
/**
 * Homepage latest news slider.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$title        = rpt_get_home_field( 'home_news_title', __( 'Tin tức mới nhất', 'generatepress_child' ) );
$button_label = rpt_get_home_field( 'home_news_button_label', __( 'Xem thêm tin tức', 'generatepress_child' ) );
$news_query   = rpt_get_home_latest_news_query();

if ( ! $news_query->have_posts() ) {
	return;
}

$has_multiple = $news_query->post_count > 1;
?>
<section class="rpt-home-section rpt-home-news">
	<div class="rpt-container">
		<header class="rpt-home-section__header">
			<h2 class="rpt-home-section__title"><?php echo esc_html( $title ); ?></h2>
			<span class="rpt-home-section__accent" aria-hidden="true"></span>
		</header>

		<div class="rpt-home-news__slider" data-rpt-home-carousel>
			<?php if ( $has_multiple ) : ?>
				<button
					type="button"
					class="rpt-home-news__arrow rpt-home-news__arrow--prev"
					data-rpt-carousel-prev
					aria-label="<?php esc_attr_e( 'Xem bài trước', 'generatepress_child' ); ?>"
				>
					<span aria-hidden="true"></span>
				</button>

				<button
					type="button"
					class="rpt-home-news__arrow rpt-home-news__arrow--next"
					data-rpt-carousel-next
					aria-label="<?php esc_attr_e( 'Xem bài tiếp theo', 'generatepress_child' ); ?>"
				>
					<span aria-hidden="true"></span>
				</button>
			<?php endif; ?>

			<div class="rpt-home-news__viewport" data-rpt-carousel-viewport tabindex="0">
				<div class="rpt-home-news__track" data-rpt-carousel-track>
					<?php
					while ( $news_query->have_posts() ) :
						$news_query->the_post();
						echo '<div class="rpt-home-news__slide" data-rpt-carousel-slide>';
						get_template_part( 'template-parts/news/news', 'card' );
						echo '</div>';
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</div>
		</div>

		<div class="rpt-home-news__actions">
			<a class="rpt-btn rpt-btn-green rpt-home-news__button" href="<?php echo esc_url( rpt_get_news_archive_url() ); ?>">
				<?php echo esc_html( $button_label ); ?>
			</a>
		</div>
	</div>
</section>
