<?php
/**
 * Homepage hero slider — image banners with optional links.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$slides = rpt_get_home_hero_slides();

if ( empty( $slides ) ) {
	return;
}

$has_multiple = count( $slides ) > 1;
?>
<section class="rpt-hero-slider" data-rpt-hero-slider data-rpt-hero-autoplay="4000" aria-label="<?php esc_attr_e( 'Banner trang chủ', 'generatepress_child' ); ?>">
	<div class="rpt-hero-slider__viewport">
		<div class="rpt-hero-slider__track">
			<?php foreach ( $slides as $index => $slide ) : ?>
				<div class="rpt-hero-slider__slide<?php echo 0 === $index ? ' is-active' : ''; ?>" data-rpt-hero-slide>
					<?php if ( ! empty( $slide['link'] ) ) : ?>
						<a
							class="rpt-hero-slider__link"
							href="<?php echo esc_url( $slide['link'] ); ?>"
							<?php echo '_blank' === $slide['target'] ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>
						>
							<?php rpt_render_home_hero_slide_image( $slide, $index ); ?>
						</a>
					<?php else : ?>
						<?php rpt_render_home_hero_slide_image( $slide, $index ); ?>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>

	<?php if ( $has_multiple ) : ?>
		<button
			type="button"
			class="rpt-hero-slider__arrow rpt-hero-slider__arrow--prev"
			data-rpt-hero-prev
			aria-label="<?php esc_attr_e( 'Slide trước', 'generatepress_child' ); ?>"
		>
			<span aria-hidden="true"></span>
		</button>

		<button
			type="button"
			class="rpt-hero-slider__arrow rpt-hero-slider__arrow--next"
			data-rpt-hero-next
			aria-label="<?php esc_attr_e( 'Slide tiếp theo', 'generatepress_child' ); ?>"
		>
			<span aria-hidden="true"></span>
		</button>

		<div class="rpt-hero-slider__dots" role="tablist" aria-label="<?php esc_attr_e( 'Chọn slide', 'generatepress_child' ); ?>">
			<?php foreach ( $slides as $index => $slide ) : ?>
				<button
					type="button"
					class="rpt-hero-slider__dot<?php echo 0 === $index ? ' is-active' : ''; ?>"
					data-rpt-hero-dot="<?php echo esc_attr( (string) $index ); ?>"
					aria-label="<?php echo esc_attr( sprintf( __( 'Slide %d', 'generatepress_child' ), $index + 1 ) ); ?>"
					<?php echo 0 === $index ? 'aria-current="true"' : ''; ?>
				></button>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>
<?php if ( $has_multiple ) : ?>
	<script src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/js/hero-slider.js?ver=' . rawurlencode( (string) rpt_get_asset_version( 'assets/js/hero-slider.js' ) ) ); ?>"></script>
<?php endif; ?>
