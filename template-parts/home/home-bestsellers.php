<?php
/**
 * Homepage best sellers slider.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$title       = rpt_get_home_field( 'home_bestsellers_title', __( 'Bán Chạy', 'generatepress_child' ) );
$more_label  = rpt_get_home_field( 'home_bestsellers_more_label', __( 'Xem thêm', 'generatepress_child' ) );
$more_url    = rpt_get_home_bestsellers_more_url();
$products    = rpt_get_home_bestseller_products();

if ( empty( $products ) ) {
	return;
}

$has_multiple = count( $products ) > 1;
?>
<section class="rpt-home-section rpt-home-bestsellers">
	<div class="rpt-container">
		<header class="rpt-home-bestsellers__header">
			<div class="rpt-home-bestsellers__heading">
				<h2 class="rpt-home-section__title"><?php echo esc_html( $title ); ?></h2>
				<span class="rpt-home-section__accent" aria-hidden="true"></span>
			</div>

			<?php if ( $more_url ) : ?>
				<a class="rpt-home-bestsellers__more" href="<?php echo esc_url( $more_url ); ?>">
					<?php echo esc_html( $more_label ); ?>
					<span class="rpt-home-bestsellers__more-icon" aria-hidden="true">&rsaquo;</span>
				</a>
			<?php endif; ?>
		</header>

		<div class="rpt-home-bestsellers__slider" data-rpt-home-carousel>
			<?php if ( $has_multiple ) : ?>
				<button
					type="button"
					class="rpt-home-bestsellers__arrow rpt-home-bestsellers__arrow--prev"
					data-rpt-carousel-prev
					aria-label="<?php esc_attr_e( 'Xem sản phẩm trước', 'generatepress_child' ); ?>"
				>
					<span aria-hidden="true"></span>
				</button>

				<button
					type="button"
					class="rpt-home-bestsellers__arrow rpt-home-bestsellers__arrow--next"
					data-rpt-carousel-next
					aria-label="<?php esc_attr_e( 'Xem sản phẩm tiếp theo', 'generatepress_child' ); ?>"
				>
					<span aria-hidden="true"></span>
				</button>
			<?php endif; ?>

			<div class="rpt-home-bestsellers__viewport" data-rpt-carousel-viewport tabindex="0">
				<div class="rpt-home-bestsellers__track" data-rpt-carousel-track>
					<?php
					foreach ( $products as $product ) {
						echo '<div class="rpt-home-bestsellers__slide" data-rpt-carousel-slide>';
						get_template_part(
							'template-parts/home/home',
							'product-card',
							array(
								'product'   => $product,
								'cta_label' => __( 'Nhận giá tốt nhất', 'generatepress_child' ),
							)
						);
						echo '</div>';
					}
					?>
				</div>
			</div>
		</div>
	</div>
</section>
