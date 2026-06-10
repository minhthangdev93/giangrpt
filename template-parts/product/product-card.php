<?php
/**
 * B2B catalog product card.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$product = isset( $args['product'] ) ? $args['product'] : null;

if ( ! $product instanceof WC_Product && function_exists( 'wc_get_product' ) ) {
	global $product;
}

if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}

$permalink  = get_permalink( $product->get_id() );
$video_data = function_exists( 'rpt_get_product_video_lightbox_data' ) ? rpt_get_product_video_lightbox_data( $product ) : null;
?>
<div class="rpt-product-card">
	<div class="rpt-product-card__image-wrap">
		<a class="rpt-product-card__image-link" href="<?php echo esc_url( $permalink ); ?>">
			<div class="rpt-product-card__image">
				<?php
				echo wp_kses_post(
					$product->get_image(
						'woocommerce_thumbnail',
						array(
							'class' => 'rpt-product-card__img',
							'alt'   => $product->get_name(),
						)
					)
				);
				?>
			</div>
		</a>

		<?php if ( is_array( $video_data ) && ! empty( $video_data['src'] ) && ! empty( $video_data['type'] ) ) : ?>
			<button
				type="button"
				class="rpt-product-card__play"
				data-rpt-video-play
				data-rpt-video-type="<?php echo esc_attr( $video_data['type'] ); ?>"
				data-rpt-video-src="<?php echo esc_attr( $video_data['src'] ); ?>"
				data-rpt-video-title="<?php echo esc_attr( $video_data['title'] ); ?>"
				<?php if ( ! empty( $video_data['poster'] ) ) : ?>
					data-rpt-video-poster="<?php echo esc_url( $video_data['poster'] ); ?>"
				<?php endif; ?>
				aria-label="<?php esc_attr_e( 'Xem video sản phẩm', 'generatepress_child' ); ?>"
			>
				<span class="rpt-play-icon" aria-hidden="true"></span>
			</button>
		<?php endif; ?>
	</div>

	<div class="rpt-product-card__content">
		<a class="rpt-product-card__title" href="<?php echo esc_url( $permalink ); ?>">
			<?php echo esc_html( $product->get_name() ); ?>
		</a>

		<?php
		if ( ! empty( $args['show_excerpt'] ) ) {
			$excerpt = $product->get_short_description();

			if ( $excerpt ) {
				echo '<p class="rpt-product-card__excerpt">' . esc_html( wp_trim_words( wp_strip_all_tags( $excerpt ), 24, '…' ) ) . '</p>';
			}
		}
		?>

		<button
			type="button"
			class="rpt-product-card__button rpt-btn rpt-btn-green"
			<?php echo rpt_get_quote_open_button_attrs( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
		>
			<?php echo esc_html( rpt_get_product_card_cta_label() ); ?>
		</button>
	</div>
</div>
