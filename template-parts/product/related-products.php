<?php
/**
 * Related products section.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product instanceof WC_Product || ! function_exists( 'rpt_get_single_related_products' ) ) {
	return;
}

$related_products = rpt_get_single_related_products( $product, 4 );

if ( empty( $related_products ) ) {
	return;
}
?>
<section class="rpt-related-products rpt-single-product-block" aria-labelledby="rpt-related-products-heading">
	<div class="rpt-section-heading">
		<h2 id="rpt-related-products-heading"><?php esc_html_e( 'Sản phẩm liên quan', 'generatepress_child' ); ?></h2>
		<span class="rpt-heading-line" aria-hidden="true"></span>
	</div>

	<div class="rpt-product-grid rpt-related-products__grid">
		<?php
		foreach ( $related_products as $related_product ) {
			get_template_part(
				'template-parts/product/product',
				'card',
				array(
					'product' => $related_product,
				)
			);
		}
		?>
	</div>
</section>
