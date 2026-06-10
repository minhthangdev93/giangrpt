<?php
/**
 * Video hub sidebar — category list.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$categories     = rpt_get_video_hub_product_categories();
$all_videos_url = rpt_get_videos_page_url();
?>
<aside class="rpt-video-sidebar" aria-label="<?php esc_attr_e( 'Khu vực video', 'generatepress_child' ); ?>">
	<div class="rpt-video-sidebar__widget">
		<div class="rpt-video-sidebar__title"><?php esc_html_e( 'Khu vực video', 'generatepress_child' ); ?></div>

		<nav class="rpt-video-sidebar__nav" aria-label="<?php esc_attr_e( 'Danh mục video', 'generatepress_child' ); ?>">
			<a
				class="rpt-video-sidebar__item<?php echo rpt_is_all_videos_active() ? ' is-active' : ''; ?>"
				href="<?php echo esc_url( $all_videos_url ); ?>"
				<?php echo rpt_is_all_videos_active() ? ' aria-current="page"' : ''; ?>
			>
				<span class="rpt-video-sidebar__label"><?php esc_html_e( 'All Videos', 'generatepress_child' ); ?></span>
				<span class="rpt-video-sidebar__chevron" aria-hidden="true"></span>
			</a>

			<?php foreach ( $categories as $term ) : ?>
				<a
					class="rpt-video-sidebar__item<?php echo rpt_is_video_category_active( $term ) ? ' is-active' : ''; ?>"
					href="<?php echo esc_url( rpt_get_video_category_link( $term ) ); ?>"
					<?php echo rpt_is_video_category_active( $term ) ? ' aria-current="page"' : ''; ?>
				>
					<span class="rpt-video-sidebar__label"><?php echo esc_html( $term->name ); ?></span>
					<span class="rpt-video-sidebar__chevron" aria-hidden="true"></span>
				</a>
			<?php endforeach; ?>
		</nav>
	</div>
</aside>
