<?php
/**
 * Product hub sidebar — categories + quick contact card.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$categories_open = ! empty( $args['categories_open'] );
$categories      = rpt_get_shop_sidebar_categories();
$contact_rows    = rpt_get_product_hub_contact_rows();
$social_links    = rpt_get_product_hub_social_links();
?>
<div class="rpt-product-hub-widget rpt-product-hub-widget--categories<?php echo $categories_open ? ' is-open' : ''; ?>" data-rpt-hub-accordion>
	<button class="rpt-product-hub-widget__toggle" type="button" aria-expanded="<?php echo $categories_open ? 'true' : 'false'; ?>" data-rpt-hub-accordion-trigger>
		<span class="rpt-product-hub-widget__toggle-label"><?php esc_html_e( 'Tất cả danh mục', 'generatepress_child' ); ?></span>
		<span class="rpt-product-hub-widget__toggle-icon" aria-hidden="true"></span>
	</button>

	<div class="rpt-product-hub-widget__body" data-rpt-hub-accordion-panel>
		<div class="rpt-product-sidebar-categories">
			<h3 class="rpt-product-sidebar-categories__title"><?php esc_html_e( 'Tất cả danh mục', 'generatepress_child' ); ?></h3>

			<?php if ( ! empty( $categories ) ) : ?>
				<?php foreach ( $categories as $term ) : ?>
					<?php
					$term_link = rpt_get_product_category_link( $term );
					$is_active = rpt_is_shop_sidebar_category_active( $term );
					?>
					<a
						class="rpt-sidebar-category-item<?php echo $is_active ? ' is-active' : ''; ?>"
						href="<?php echo esc_url( $term_link ); ?>"
						<?php echo $is_active ? ' aria-current="page"' : ''; ?>
					>
						<span class="rpt-sidebar-category-name"><?php echo esc_html( $term->name ); ?></span>
						<span class="rpt-sidebar-category-count"><?php echo esc_html( (string) rpt_get_shop_category_product_count( $term ) ); ?></span>
					</a>
				<?php endforeach; ?>
			<?php else : ?>
				<p class="rpt-product-hub-widget__empty"><?php esc_html_e( 'Chưa có danh mục sản phẩm.', 'generatepress_child' ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</div>

<div class="rpt-product-hub-widget rpt-product-hub-widget--contact">
	<h2 class="rpt-product-hub-widget__title rpt-product-hub-widget__title--contact"><?php echo esc_html( rpt_get_shop_contact_rep_name() ); ?></h2>

	<?php if ( ! empty( $contact_rows ) ) : ?>
		<dl class="rpt-product-hub-contact">
			<?php foreach ( $contact_rows as $row ) : ?>
				<div class="rpt-product-hub-contact__item">
					<dt class="rpt-product-hub-contact__label"><?php echo esc_html( $row['label'] ); ?>:</dt>
					<dd class="rpt-product-hub-contact__value">
						<?php if ( ! empty( $row['url'] ) ) : ?>
							<a href="<?php echo esc_url( $row['url'] ); ?>"><?php echo esc_html( $row['value'] ); ?></a>
						<?php else : ?>
							<?php echo esc_html( $row['value'] ); ?>
						<?php endif; ?>
					</dd>
				</div>
			<?php endforeach; ?>
		</dl>
	<?php endif; ?>

	<?php if ( ! empty( $social_links ) ) : ?>
		<div class="rpt-product-hub-social">
			<?php foreach ( $social_links as $link ) : ?>
				<a
					class="rpt-product-hub-social__link"
					href="<?php echo esc_url( $link['url'] ); ?>"
					aria-label="<?php echo esc_attr( $link['label'] ); ?>"
				>
					<?php echo rpt_get_product_hub_social_icon_svg( $link['key'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<?php
	rpt_render_quote_cta_button(
		array(
			'class' => 'rpt-product-hub-widget__cta',
		)
	);
	?>
</div>
