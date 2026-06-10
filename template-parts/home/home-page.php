<?php
/**
 * Homepage layout.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="rpt-home-page">
	<?php get_template_part( 'template-parts/home/hero', 'slider' ); ?>
	<?php get_template_part( 'template-parts/home/home', 'categories' ); ?>
	<?php get_template_part( 'template-parts/home/home', 'bestsellers' ); ?>
	<?php get_template_part( 'template-parts/home/home', 'achievements' ); ?>
	<?php get_template_part( 'template-parts/home/home', 'about' ); ?>
	<?php get_template_part( 'template-parts/home/home', 'certificates' ); ?>
	<?php get_template_part( 'template-parts/home/home', 'news' ); ?>
</div>
