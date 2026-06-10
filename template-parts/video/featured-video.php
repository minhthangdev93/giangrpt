<?php
/**
 * Featured video hero card.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$video = isset( $args['video'] ) ? $args['video'] : null;

if ( ! $video instanceof WP_Post ) {
	return;
}

$permalink  = get_permalink( $video );
$media_html = rpt_get_video_featured_media_html( $video );
?>
<section class="rpt-video-featured rpt-card">
	<div class="rpt-video-featured__media">
		<?php echo $media_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>

	<div class="rpt-video-featured__content">
		<h1 class="rpt-video-featured__title"><?php echo esc_html( get_the_title( $video ) ); ?></h1>

		<?php if ( rpt_get_video_publish_date( $video->ID ) ) : ?>
			<div class="rpt-video-featured__date">
				<?php echo esc_html( rpt_get_video_publish_date( $video->ID ) ); ?>
			</div>
		<?php endif; ?>

		<?php if ( rpt_get_video_excerpt( $video->ID ) ) : ?>
			<p class="rpt-video-featured__description"><?php echo esc_html( rpt_get_video_excerpt( $video->ID ) ); ?></p>
		<?php endif; ?>

		<div class="rpt-video-featured__actions">
			<a class="rpt-btn rpt-btn-outline-green" href="<?php echo esc_url( $permalink ); ?>">
				<?php esc_html_e( 'Watch the Full Video', 'generatepress_child' ); ?>
			</a>
			<?php rpt_render_quote_cta_button(); ?>
		</div>
	</div>
</section>
