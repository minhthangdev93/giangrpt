<?php
/**
 * Video hub page layout — sections by WooCommerce product category.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$current_term = rpt_get_current_video_category_term();
?>
<div class="rpt-video-page">
	<div class="rpt-container rpt-video-page__container">
		<?php rpt_render_video_breadcrumb(); ?>

		<div class="rpt-video-page__panel rpt-video-page__title-block">
			<h1 class="rpt-video-page__title"><?php esc_html_e( 'Videos của chúng tôi', 'generatepress_child' ); ?></h1>
		</div>

		<div class="rpt-video-layout">
			<?php get_template_part( 'template-parts/video/video', 'sidebar' ); ?>

			<main class="rpt-video-main">
				<div class="rpt-video-sections">
				<?php if ( $current_term instanceof WP_Term ) : ?>
					<?php
					get_template_part(
						'template-parts/video/video-category',
						'section',
						array(
							'term'       => $current_term,
							'limit'      => -1,
							'show_more'  => false,
							'show_title' => true,
						)
					);
					?>
				<?php else : ?>
					<?php
					$categories = rpt_get_video_hub_product_categories();

					if ( empty( $categories ) ) :
						?>
						<p class="rpt-video-sections__empty"><?php esc_html_e( 'Chưa có video nào.', 'generatepress_child' ); ?></p>
						<?php
					else :
						foreach ( $categories as $term ) {
							get_template_part(
								'template-parts/video/video-category',
								'section',
								array(
									'term'       => $term,
									'limit'      => RPT_VIDEOS_PER_SECTION,
									'show_more'  => true,
									'show_title' => true,
								)
							);
						}
					endif;
					?>
				<?php endif; ?>
				</div>
			</main>
		</div>
	</div>
</div>
