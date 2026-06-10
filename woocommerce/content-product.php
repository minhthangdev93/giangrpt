<?php
/**
 * Product card in shop loop — RPT B2B catalog card.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}
?>
<li <?php wc_product_class( 'rpt-product-card-wrap', $product ); ?>>
	<?php
	get_template_part(
		'template-parts/product/product',
		'card',
		array(
			'product' => $product,
		)
	);
	?>
</li>
