<?php
/**
 * Single video detail layout.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$video_id   = get_the_ID();
$video_url  = rpt_get_video_url( $video_id );
$embed_html = $video_url ? rpt_get_video_embed_html( $video_url ) : '';
?>
<div class="rpt-video-page rpt-video-page--single">
	<div class="rpt-container rpt-video-page__container">
		<article class="rpt-video-single rpt-card">
			<div class="rpt-video-single__media">
				<?php if ( $embed_html ) : ?>
					<div class="rpt-video-single__embed"><?php echo $embed_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<?php elseif ( has_post_thumbnail() ) : ?>
					<div class="rpt-video-single__thumb">
						<?php the_post_thumbnail( 'large', array( 'class' => 'rpt-video-single__thumb-img' ) ); ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="rpt-video-single__content">
				<?php
				$category_term = rpt_get_video_primary_category( $video_id );
				if ( $category_term instanceof WP_Term ) :
					?>
					<div class="rpt-video-single__category">
						<a href="<?php echo esc_url( rpt_get_video_category_link( $category_term ) ); ?>">
							<?php echo esc_html( $category_term->name ); ?>
						</a>
					</div>
				<?php endif; ?>

				<h1 class="rpt-video-single__title"><?php the_title(); ?></h1>

				<div class="rpt-video-single__meta">
					<?php if ( rpt_get_video_publish_date( $video_id ) ) : ?>
						<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( rpt_get_video_publish_date( $video_id ) ); ?></time>
					<?php endif; ?>

					<?php if ( rpt_get_video_duration( $video_id ) ) : ?>
						<span class="rpt-video-single__duration"><?php echo esc_html( rpt_get_video_duration( $video_id ) ); ?></span>
					<?php endif; ?>
				</div>

				<?php if ( get_the_content() ) : ?>
					<div class="rpt-video-single__description">
						<?php the_content(); ?>
					</div>
				<?php elseif ( rpt_get_video_excerpt( $video_id ) ) : ?>
					<p class="rpt-video-single__description"><?php echo esc_html( rpt_get_video_excerpt( $video_id ) ); ?></p>
				<?php endif; ?>

				<div class="rpt-video-single__actions">
					<?php rpt_render_quote_cta_button(); ?>
					<a class="rpt-btn rpt-btn-secondary" href="<?php echo esc_url( rpt_get_videos_page_url() ); ?>">
						<?php esc_html_e( 'All Videos', 'generatepress_child' ); ?>
					</a>
				</div>
			</div>
		</article>
	</div>
</div>
