<?php
/**
 * Latest videos section.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$exclude_ids = isset( $args['exclude_ids'] ) ? (array) $args['exclude_ids'] : array();
$videos      = rpt_get_latest_videos( 12, null, $exclude_ids );

if ( empty( $videos ) ) {
	return;
}
?>
<section class="rpt-video-section rpt-latest-videos" aria-labelledby="rpt-latest-videos-heading">
	<div class="rpt-section-heading">
		<h2 id="rpt-latest-videos-heading"><?php esc_html_e( 'Latest Videos', 'generatepress_child' ); ?></h2>
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
