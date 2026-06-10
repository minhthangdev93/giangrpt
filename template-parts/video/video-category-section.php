<?php
/**
 * Video hub — single product category section.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$term = isset( $args['term'] ) ? $args['term'] : null;

if ( ! $term instanceof WP_Term ) {
	return;
}

$limit      = isset( $args['limit'] ) ? (int) $args['limit'] : RPT_VIDEOS_PER_SECTION;
$show_more  = ! empty( $args['show_more'] );
$show_title = ! isset( $args['show_title'] ) || ! empty( $args['show_title'] );
$videos     = rpt_get_videos_for_product_category( $term, $limit );

if ( empty( $videos ) ) {
	return;
}

$total_videos = rpt_count_videos_in_product_category( $term );
$more_link    = rpt_get_video_category_link( $term );
$has_more     = $show_more && $total_videos > count( $videos );
?>
<section class="rpt-video-category-section" id="<?php echo esc_attr( 'rpt-video-cat-' . $term->slug ); ?>">
	<?php if ( $show_title ) : ?>
		<div class="rpt-video-category-section__header">
			<h2 class="rpt-video-category-section__title"><?php echo esc_html( $term->name ); ?></h2>

			<?php if ( $has_more ) : ?>
				<a class="rpt-video-category-section__more" href="<?php echo esc_url( $more_link ); ?>">
					<?php esc_html_e( 'Xem thêm', 'generatepress_child' ); ?>
					<span class="rpt-video-category-section__more-icon" aria-hidden="true">&rsaquo;</span>
				</a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<div class="rpt-video-category-section__body">
		<div class="rpt-video-grid">
			<?php
			foreach ( $videos as $video ) {
				get_template_part(
					'template-parts/video/video',
					'card',
					array(
						'video'          => $video,
						'category_term'  => $term,
					)
				);
			}
			?>
		</div>
	</div>
</section>
