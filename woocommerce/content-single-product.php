<?php
/**
 * Single product — B2B catalog layout.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	return;
}
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'rpt-single-product', $product ); ?>>
	<div class="rpt-single-product-page">
		<div class="rpt-container rpt-single-product-page__container">
			<?php rpt_woocommerce_breadcrumb(); ?>

			<?php get_template_part( 'template-parts/product/single-product', 'hero' ); ?>

			<div class="rpt-single-product-layout">
				<main class="rpt-single-product-main">
					<?php
					get_template_part( 'template-parts/product/product', 'specs' );
					get_template_part( 'template-parts/product/product', 'description' );
					?>
				</main>

				<aside class="rpt-single-product-sidebar" aria-label="<?php esc_attr_e( 'Liên hệ nhanh', 'generatepress_child' ); ?>">
					<?php get_template_part( 'template-parts/product/product', 'contact-card' ); ?>
				</aside>
			</div>

			<?php get_template_part( 'template-parts/product/related', 'products' ); ?>
		</div>
	</div>
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>
