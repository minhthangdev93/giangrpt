<?php
/**
 * Single post layout.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="rpt-single-post">
	<div class="rpt-container rpt-single-post__container">
		<?php rpt_render_single_post_breadcrumb(); ?>

		<?php get_template_part( 'template-parts/news/single-post', 'hero' ); ?>

		<div class="rpt-single-post__content-card">
			<div class="rpt-single-post__entry entry-content">
				<?php
				the_content();

				wp_link_pages(
					array(
						'before' => '<div class="rpt-single-post__page-links"><span class="rpt-single-post__page-links-label">' . esc_html__( 'Trang:', 'generatepress_child' ) . '</span>',
						'after'  => '</div>',
					)
				);
				?>
			</div>
		</div>

		<?php get_template_part( 'template-parts/news/single-post', 'related' ); ?>
	</div>
</div>
