<?php
/**
 * Popular videos section.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$videos = isset( $args['videos'] ) ? (array) $args['videos'] : array();

if ( empty( $videos ) ) {
	$exclude_ids = isset( $args['exclude_ids'] ) ? (array) $args['exclude_ids'] : array();
	$videos      = rpt_get_popular_videos( 8, null, $exclude_ids );
}

if ( empty( $videos ) ) {
	return;
}
?>
<section class="rpt-video-section rpt-popular-videos" aria-labelledby="rpt-popular-videos-heading">
	<div class="rpt-section-heading">
		<h2 id="rpt-popular-videos-heading"><?php esc_html_e( 'Popular Videos', 'generatepress_child' ); ?></h2>
		<span class="rpt-heading-line" aria-hidden="true"></span>
	</div>

	<div class="rpt-video-grid">
		<?php
		foreach ( $videos as $video ) {
			get_template_part(
				'template-parts/video/video',
				'card',
				array(
					'video' => $video,
				)
			);
		}
		?>
	</div>
</section>
