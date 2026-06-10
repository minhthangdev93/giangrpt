<?php
/**
 * Contact page hero title.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$title = rpt_get_contact_page_title();
?>
<section class="rpt-contact-hero">
	<h1 class="rpt-contact-hero__title"><?php echo esc_html( $title ); ?></h1>
	<span class="rpt-contact-hero__accent" aria-hidden="true"></span>
</section>
