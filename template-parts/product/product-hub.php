<?php
/**
 * B2B product hub — shop page layout.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$categories = rpt_get_shop_sidebar_categories();
?>
<div class="rpt-product-hub">
	<div class="rpt-container rpt-product-hub__container">
		<?php rpt_woocommerce_breadcrumb(); ?>

		<div class="rpt-product-hub__panel rpt-product-hub__title-block">
			<h1 class="rpt-product-hub__title"><?php esc_html_e( 'Sản phẩm của chúng tôi', 'generatepress_child' ); ?></h1>
		</div>

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

			<main class="rpt-product-hub__content">
				<?php woocommerce_output_all_notices(); ?>

				<div class="rpt-product-hub__sections">
					<?php if ( ! empty( $categories ) ) : ?>
						<?php
						foreach ( $categories as $term ) {
							get_template_part(
								'template-parts/product/product',
								'category-section',
								array(
									'term' => $term,
								)
							);
						}
						?>
					<?php else : ?>
						<p class="rpt-product-hub__empty"><?php esc_html_e( 'Chưa có danh mục sản phẩm.', 'generatepress_child' ); ?></p>
					<?php endif; ?>
				</div>
			</main>
		</div>
	</div>
</div>
