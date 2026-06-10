<?php
/**
 * B2B product category archive layout.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$term = get_queried_object();

if ( ! $term instanceof WP_Term || 'product_cat' !== $term->taxonomy ) {
	return;
}
?>
<div class="rpt-product-category-page">
	<div class="rpt-container rpt-product-category-page__container">
		<?php rpt_woocommerce_breadcrumb(); ?>

		<header class="rpt-category-header">
			<h1 class="rpt-category-title"><?php echo esc_html( $term->name ); ?></h1>
			<div class="rpt-title-underline" aria-hidden="true"></div>
		</header>

		<div class="rpt-product-hub__layout">
			<aside class="rpt-product-hub__sidebar rpt-product-sidebar" aria-label="<?php esc_attr_e( 'Danh mục sản phẩm', 'generatepress_child' ); ?>">
				<?php
				get_template_part(
					'template-parts/product/product',
					'sidebar',
					array(
						'categories_open' => true,
					)
				);
				?>
			</aside>

			<main class="rpt-product-hub__content rpt-category-products">
				<?php woocommerce_output_all_notices(); ?>

				<?php if ( have_posts() ) : ?>
					<div class="rpt-product-grid">
						<?php
						while ( have_posts() ) {
							the_post();
							$product = wc_get_product( get_the_ID() );

							get_template_part(
								'template-parts/product/product',
								'card',
								array(
									'product'      => $product,
									'show_excerpt' => true,
								)
							);
						}
						?>
					</div>

					<div class="rpt-category-pagination">
						<?php rpt_render_category_archive_pagination(); ?>
					</div>
				<?php else : ?>
					<p class="rpt-category-products__empty"><?php esc_html_e( 'Không có sản phẩm trong danh mục này.', 'generatepress_child' ); ?></p>
				<?php endif; ?>
			</main>
		</div>
	</div>
</div>
