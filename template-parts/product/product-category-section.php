<?php
/**
 * Product hub — single category section.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$term = isset( $args['term'] ) ? $args['term'] : null;

if ( ! $term instanceof WP_Term ) {
	return;
}

$products  = rpt_get_shop_category_products( $term, 8 );
$term_link = function_exists( 'rpt_get_product_category_link' ) ? rpt_get_product_category_link( $term ) : get_term_link( $term );

if ( is_wp_error( $term_link ) || empty( $products ) ) {
	return;
}
?>
<section class="rpt-product-hub-category" id="<?php echo esc_attr( 'rpt-shop-cat-' . $term->slug ); ?>">
	<div class="rpt-product-hub__panel rpt-product-hub-category__header-panel">
		<div class="rpt-product-hub-category__header">
			<h2 class="rpt-product-hub-category__title"><?php echo esc_html( $term->name ); ?></h2>
			<a class="rpt-product-hub-category__more" href="<?php echo esc_url( $term_link ); ?>">
				<?php esc_html_e( 'Xem thêm', 'generatepress_child' ); ?>
				<span class="rpt-product-hub-category__more-icon" aria-hidden="true">&rsaquo;</span>
			</a>
		</div>
	</div>

	<div class="rpt-product-hub__panel rpt-product-hub-category__products-panel">
		<div class="rpt-product-grid">
			<?php
			foreach ( $products as $hub_product ) {
				get_template_part(
					'template-parts/product/product',
					'card',
					array(
						'product' => $hub_product,
					)
				);
			}
			?>
		</div>
	</div>
</section>
