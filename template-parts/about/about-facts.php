<?php
/**
 * About page company facts grid.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$facts = rpt_get_about_company_facts();

if ( empty( $facts ) ) {
	return;
}
?>
<section class="rpt-about-facts">
	<div class="rpt-facts-grid">
		<?php foreach ( $facts as $fact ) : ?>
			<div class="rpt-fact-item">
				<span class="rpt-fact-label"><?php echo esc_html( $fact['label'] ); ?></span>
				<strong class="rpt-fact-value"><?php echo esc_html( $fact['value'] ); ?></strong>
			</div>
		<?php endforeach; ?>
	</div>
</section>
