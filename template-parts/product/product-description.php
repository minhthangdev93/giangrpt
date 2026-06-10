<?php
/**
 * Product long description section.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product instanceof WC_Product ) {
	return;
}

$description = $product->get_description();

if ( ! $description ) {
	return;
}
?>
<section class="rpt-product-description rpt-single-product-block" aria-labelledby="rpt-product-description-heading">
	<div class="rpt-description-header">
		<span class="rpt-description-header__icon" aria-hidden="true">
			<svg class="rpt-description-header__icon-svg" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<line x1="4" y1="5" x2="4" y2="19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
				<path d="M14 4.5 20 8 14 11.5 8 8 14 4.5Z" fill="currentColor" />
				<path d="M8 12.5 14 16 20 12.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
				<path d="M8 16.5 14 20 20 16.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
			</svg>
		</span>
		<h2 id="rpt-product-description-heading"><?php esc_html_e( 'Mô tả sản phẩm', 'generatepress_child' ); ?></h2>
	</div>

	<div class="rpt-description-content">
		<?php echo apply_filters( 'the_content', $description ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
</section>
