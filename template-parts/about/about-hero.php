<?php
/**
 * About page hero.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$title    = rpt_get_about_company_title();
$subtitle = rpt_get_about_company_subtitle();

if ( '' === $title ) {
	return;
}
?>
<section class="rpt-about-hero">
	<h1 class="rpt-about-hero__title"><?php echo esc_html( $title ); ?></h1>

	<?php if ( '' !== $subtitle ) : ?>
		<p class="rpt-about-hero__subtitle"><?php echo esc_html( $subtitle ); ?></p>
	<?php endif; ?>
</section>
