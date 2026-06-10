<?php
/**
 * Single product hero — gallery + summary.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product instanceof WC_Product ) {
	return;
}
?>
<section class="rpt-single-product-hero">
	<div class="rpt-single-product-hero__gallery">
		<?php get_template_part( 'template-parts/product/product', 'gallery' ); ?>
	</div>

	<div class="rpt-product-summary">
		<h1 class="rpt-product-summary__title"><?php echo esc_html( $product->get_name() ); ?></h1>

		<?php get_template_part( 'template-parts/product/product', 'basic-info' ); ?>

		<button
			type="button"
			class="rpt-btn rpt-btn-green rpt-product-main-cta"
			id="product-inquiry"
			<?php echo rpt_get_quote_open_button_attrs( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
		>
			<?php echo esc_html( rpt_get_shop_hub_cta_label() ); ?>
		</button>
	</div>
</section>
