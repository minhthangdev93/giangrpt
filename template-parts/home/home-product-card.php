<?php
/**
 * Homepage product card with hot sale badge.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$product = isset( $args['product'] ) ? $args['product'] : null;

if ( ! $product instanceof WC_Product || ! $product->is_visible() ) {
	return;
}

$permalink  = get_permalink( $product->get_id() );
$video_data = function_exists( 'rpt_get_product_video_lightbox_data' ) ? rpt_get_product_video_lightbox_data( $product ) : null;
$excerpt    = $product->get_short_description();
$cta_label  = ! empty( $args['cta_label'] ) ? $args['cta_label'] : rpt_get_product_card_cta_label();
?>
<article class="rpt-home-product-card">
	<div class="rpt-home-product-card__image-wrap">
		<span class="rpt-home-product-card__badge"><?php esc_html_e( 'Hot Sale!', 'generatepress_child' ); ?></span>

		<a class="rpt-home-product-card__image-link" href="<?php echo esc_url( $permalink ); ?>">
			<?php
			echo wp_kses_post(
				$product->get_image(
					'woocommerce_thumbnail',
					array(
						'class' => 'rpt-home-product-card__image',
						'alt'   => $product->get_name(),
					)
				)
			);
			?>
		</a>

		<?php if ( is_array( $video_data ) && ! empty( $video_data['src'] ) ) : ?>
			<span class="rpt-home-product-card__video-badge">VIDEO</span>
		<?php endif; ?>
	</div>

	<div class="rpt-home-product-card__body">
		<h3 class="rpt-home-product-card__title">
			<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
		</h3>

		<?php if ( $excerpt ) : ?>
			<p class="rpt-home-product-card__excerpt"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $excerpt ), 22, '…' ) ); ?></p>
		<?php endif; ?>

		<button
			type="button"
			class="rpt-home-product-card__cta"
			<?php echo rpt_get_quote_open_button_attrs( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
		>
			<?php echo esc_html( $cta_label ); ?>
		</button>
	</div>
</article>
