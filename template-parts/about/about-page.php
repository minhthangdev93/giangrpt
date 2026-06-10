<?php
/**
 * About page layout.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="rpt-about-page">
	<div class="rpt-container rpt-about-page__container">
		<?php rpt_render_about_breadcrumb(); ?>

		<?php get_template_part( 'template-parts/about/about', 'hero' ); ?>
		<?php get_template_part( 'template-parts/about/about', 'tabs' ); ?>
		<?php get_template_part( 'template-parts/about/about', 'facts' ); ?>
	</div>
</div>
