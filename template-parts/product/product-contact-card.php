<?php
/**
 * Single product sidebar contact card.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

global $product;

$contact_rows = function_exists( 'rpt_get_product_contact_rows' ) ? rpt_get_product_contact_rows() : array();
$social_links = function_exists( 'rpt_get_product_hub_social_links' ) ? rpt_get_product_hub_social_links() : array();
$cta_product  = $product instanceof WC_Product ? $product : null;
?>
<div class="rpt-product-hub-widget rpt-product-hub-widget--contact rpt-product-contact-card">
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
			'class'   => 'rpt-product-hub-widget__cta',
			'product' => $cta_product,
		)
	);
	?>
</div>
