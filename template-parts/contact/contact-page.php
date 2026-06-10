<?php
/**
 * Contact page layout.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="rpt-contact-page">
	<div class="rpt-container rpt-contact-page__container">
		<?php rpt_render_contact_breadcrumb(); ?>

		<?php get_template_part( 'template-parts/contact/contact', 'hero' ); ?>
		<?php get_template_part( 'template-parts/contact/contact', 'rep' ); ?>
		<?php get_template_part( 'template-parts/contact/contact', 'form' ); ?>
		<?php get_template_part( 'template-parts/contact/contact', 'location' ); ?>
	</div>
</div>
