<?php
/**
 * Product image gallery with thumbnails and optional video slide.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product instanceof WC_Product ) {
	return;
}

$images       = function_exists( 'rpt_get_product_gallery_items' ) ? rpt_get_product_gallery_items( $product ) : array();
$video_data   = function_exists( 'rpt_get_product_video_lightbox_data' ) ? rpt_get_product_video_lightbox_data( $product ) : null;
$has_nav      = count( $images ) > 1;
$max_thumbs   = (int) apply_filters( 'rpt_product_gallery_max_thumbs', 5 );
$max_thumbs   = max( 1, min( 5, $max_thumbs ) );
$thumb_images = array_slice( $images, 0, $max_thumbs );
$gallery_json = wp_json_encode( $images );

if ( empty( $images ) ) {
	return;
}
?>
<div
	class="rpt-product-gallery"
	data-rpt-product-gallery
	data-rpt-gallery-images="<?php echo esc_attr( $gallery_json ); ?>"
>
	<div class="rpt-product-gallery__stage">
		<div class="rpt-product-gallery__viewport">
			<div class="rpt-product-gallery__main">
				<img
					class="rpt-product-gallery__main-img"
					data-rpt-gallery-main
					src="<?php echo esc_url( $images[0]['full'] ); ?>"
					alt="<?php echo esc_attr( $images[0]['alt'] ); ?>"
					loading="eager"
					decoding="async"
				/>

				<?php if ( is_array( $video_data ) && ! empty( $video_data['src'] ) && ! empty( $video_data['type'] ) ) : ?>
					<button
						type="button"
						class="rpt-product-gallery__play"
						data-rpt-gallery-play
						data-rpt-video-play
						data-rpt-video-type="<?php echo esc_attr( $video_data['type'] ); ?>"
						data-rpt-video-src="<?php echo esc_attr( $video_data['src'] ); ?>"
						data-rpt-video-title="<?php echo esc_attr( $video_data['title'] ); ?>"
						<?php if ( ! empty( $video_data['poster'] ) ) : ?>
							data-rpt-video-poster="<?php echo esc_url( $video_data['poster'] ); ?>"
						<?php endif; ?>
						aria-label="<?php esc_attr_e( 'Xem video sản phẩm', 'generatepress_child' ); ?>"
						<?php echo ( isset( $images[0]['type'] ) && 'video' === $images[0]['type'] ) ? '' : ' hidden'; ?>
					>
						<span class="rpt-play-icon rpt-play-icon--gallery" aria-hidden="true"></span>
					</button>
				<?php endif; ?>
			</div>

			<?php if ( $has_nav ) : ?>
				<button class="rpt-product-gallery__nav rpt-product-gallery__nav--prev" type="button" data-rpt-gallery-prev aria-label="<?php esc_attr_e( 'Ảnh trước', 'generatepress_child' ); ?>">
					<span aria-hidden="true">&lsaquo;</span>
				</button>
				<button class="rpt-product-gallery__nav rpt-product-gallery__nav--next" type="button" data-rpt-gallery-next aria-label="<?php esc_attr_e( 'Ảnh sau', 'generatepress_child' ); ?>">
					<span aria-hidden="true">&rsaquo;</span>
				</button>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( $has_nav ) : ?>
		<div class="rpt-product-gallery__thumbs" role="tablist" aria-label="<?php esc_attr_e( 'Ảnh sản phẩm', 'generatepress_child' ); ?>">
			<?php foreach ( $thumb_images as $index => $image ) : ?>
				<button
					type="button"
					class="rpt-product-gallery__thumb<?php echo 0 === $index ? ' is-active' : ''; ?><?php echo ( isset( $image['type'] ) && 'video' === $image['type'] ) ? ' is-video' : ''; ?>"
					data-rpt-gallery-thumb
					data-index="<?php echo esc_attr( (string) $index ); ?>"
					role="tab"
					aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>"
					aria-label="<?php echo esc_attr( sprintf( __( 'Ảnh %d', 'generatepress_child' ), $index + 1 ) ); ?>"
				>
					<img src="<?php echo esc_url( $image['thumb'] ); ?>" alt="" width="80" height="80" loading="lazy" decoding="async" />
					<?php if ( isset( $image['type'] ) && 'video' === $image['type'] ) : ?>
						<span class="rpt-product-gallery__thumb-play" aria-hidden="true"></span>
					<?php endif; ?>
				</button>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
