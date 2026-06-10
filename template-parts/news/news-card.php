<?php
/**
 * News card.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$post_id      = get_the_ID();
$date_parts   = rpt_get_news_date_parts( $post_id );
$date_display = rpt_get_news_date_display( $post_id );
$permalink    = get_permalink();
$title        = get_the_title();
$excerpt      = get_the_excerpt();
$thumb_id     = get_post_thumbnail_id( $post_id );
?>
<article <?php post_class( 'rpt-news-card' ); ?>>
	<a class="rpt-news-card__media" href="<?php echo esc_url( $permalink ); ?>">
		<?php if ( $thumb_id ) : ?>
			<?php
			echo wp_get_attachment_image(
				$thumb_id,
				'medium_large',
				false,
				array(
					'class'    => 'rpt-news-card__image',
					'loading'  => 'lazy',
					'decoding' => 'async',
					'alt'      => $title,
				)
			);
			?>
		<?php else : ?>
			<div class="rpt-news-card__placeholder" aria-hidden="true"></div>
		<?php endif; ?>

		<?php if ( $date_parts['day'] && $date_parts['month'] ) : ?>
			<div class="rpt-news-card__date-badge" aria-hidden="true">
				<span class="rpt-news-card__date-day"><?php echo esc_html( $date_parts['day'] ); ?></span>
				<span class="rpt-news-card__date-month"><?php echo esc_html( $date_parts['month'] ); ?></span>
			</div>
		<?php endif; ?>
	</a>

	<div class="rpt-news-card__body">
		<?php if ( $date_display ) : ?>
			<time class="rpt-news-card__date" datetime="<?php echo esc_attr( get_the_date( 'c', $post_id ) ); ?>">
				<?php echo esc_html( $date_display ); ?>
			</time>
		<?php endif; ?>

		<h2 class="rpt-news-card__title">
			<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
		</h2>

		<?php if ( $excerpt ) : ?>
			<p class="rpt-news-card__excerpt"><?php echo esc_html( wp_strip_all_tags( $excerpt ) ); ?></p>
		<?php endif; ?>

		<a class="rpt-news-card__more" href="<?php echo esc_url( $permalink ); ?>">
			<?php esc_html_e( 'Xem thêm', 'generatepress_child' ); ?>
			<span class="rpt-news-card__more-icon" aria-hidden="true">&rsaquo;</span>
		</a>
	</div>
</article>
