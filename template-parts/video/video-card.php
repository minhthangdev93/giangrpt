<?php
/**
 * Video card for hub grids.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$raw_item      = isset( $args['video'] ) ? $args['video'] : null;
$section_term  = isset( $args['category_term'] ) && $args['category_term'] instanceof WP_Term ? $args['category_term'] : null;
$hub_item      = function_exists( 'rpt_normalize_hub_video_item' ) ? rpt_normalize_hub_video_item( $raw_item ) : null;

if ( ! $hub_item ) {
	return;
}

$permalink     = '';
$title         = '';
$duration      = '';
$date          = '';
$date_iso      = '';
$category_term = $section_term;
$video_data    = null;
$thumb_html    = '';

if ( 'rpt_video' === $hub_item['type'] && $hub_item['object'] instanceof WP_Post ) {
	$video = $hub_item['object'];

	$permalink = get_permalink( $video );
	$title     = get_the_title( $video );
	$duration  = rpt_get_video_duration( $video->ID );
	$date      = rpt_get_video_publish_date( $video->ID );
	$date_iso  = get_the_date( 'c', $video );

	if ( ! $category_term instanceof WP_Term ) {
		$category_term = rpt_get_video_primary_category( $video->ID );
	}

	if ( has_post_thumbnail( $video ) ) {
		$thumb_html = get_the_post_thumbnail(
			$video,
			'medium_large',
			array(
				'class'    => 'rpt-video-card__img',
				'alt'      => esc_attr( $title ),
				'loading'  => 'lazy',
				'decoding' => 'async',
			)
		);
	}

	if ( function_exists( 'rpt_get_rpt_video_lightbox_data' ) ) {
		$video_data = rpt_get_rpt_video_lightbox_data( $video );
	}
} elseif ( 'product' === $hub_item['type'] && is_a( $hub_item['object'], 'WC_Product' ) ) {
	$product = $hub_item['object'];

	$permalink  = get_permalink( $product->get_id() );
	$title      = $product->get_name();
	$date       = get_the_date( '', $product->get_id() );
	$date_iso   = get_the_date( 'c', $product->get_id() );
	$video_data = function_exists( 'rpt_get_product_video_lightbox_data' )
		? rpt_get_product_video_lightbox_data( $product )
		: null;

	if ( ! $category_term instanceof WP_Term && function_exists( 'rpt_get_product_video_category_terms' ) ) {
		$product_terms = rpt_get_product_video_category_terms( $product->get_id() );
		$category_term = ! empty( $product_terms[0] ) ? $product_terms[0] : null;
	}

	$poster = function_exists( 'rpt_get_product_video_poster' ) ? rpt_get_product_video_poster( $product ) : null;

	if ( is_array( $poster ) && ! empty( $poster['ID'] ) ) {
		$thumb_html = wp_get_attachment_image(
			(int) $poster['ID'],
			'medium_large',
			false,
			array(
				'class'    => 'rpt-video-card__img',
				'alt'      => esc_attr( $title ),
				'loading'  => 'lazy',
				'decoding' => 'async',
			)
		);
	} else {
		$thumb_html = $product->get_image(
			'medium_large',
			array(
				'class' => 'rpt-video-card__img',
				'alt'   => esc_attr( $title ),
			)
		);
	}
}

if ( '' === $title ) {
	return;
}

$category_name = $category_term instanceof WP_Term ? $category_term->name : '';
$category_url  = $category_term instanceof WP_Term ? rpt_get_video_category_link( $category_term ) : '';
$has_lightbox  = is_array( $video_data ) && ! empty( $video_data['src'] ) && ! empty( $video_data['type'] );
?>
<article class="rpt-video-card<?php echo $has_lightbox ? ' rpt-video-card--has-lightbox' : ''; ?>">
	<div class="rpt-video-card__thumb-wrap">
		<?php if ( $has_lightbox ) : ?>
			<button
				type="button"
				class="rpt-video-card__thumb rpt-video-card__thumb--play"
				data-rpt-video-play
				data-rpt-video-type="<?php echo esc_attr( $video_data['type'] ); ?>"
				data-rpt-video-src="<?php echo esc_attr( $video_data['src'] ); ?>"
				data-rpt-video-title="<?php echo esc_attr( $video_data['title'] ); ?>"
				<?php if ( ! empty( $video_data['poster'] ) ) : ?>
					data-rpt-video-poster="<?php echo esc_url( $video_data['poster'] ); ?>"
				<?php endif; ?>
				aria-label="<?php echo esc_attr( sprintf( __( 'Xem video: %s', 'generatepress_child' ), $title ) ); ?>"
			>
				<?php
				if ( $thumb_html ) {
					echo wp_kses_post( $thumb_html );
				} else {
					echo '<span class="rpt-video-card__placeholder" aria-hidden="true"></span>';
				}
				?>
				<span class="rpt-video-card__play" aria-hidden="true"><span class="rpt-play-icon"></span></span>
			</button>
		<?php else : ?>
			<a class="rpt-video-card__thumb" href="<?php echo esc_url( $permalink ); ?>">
				<?php
				if ( $thumb_html ) {
					echo wp_kses_post( $thumb_html );
				} else {
					echo '<span class="rpt-video-card__placeholder" aria-hidden="true"></span>';
				}
				?>
				<span class="rpt-video-card__play" aria-hidden="true"><span class="rpt-play-icon"></span></span>
			</a>
		<?php endif; ?>

		<?php if ( '' !== $duration ) : ?>
			<span class="rpt-video-card__duration"><?php echo esc_html( $duration ); ?></span>
		<?php endif; ?>
	</div>

	<div class="rpt-video-card__body">
		<h3 class="rpt-video-card__title">
			<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
		</h3>

		<?php if ( '' !== $category_name ) : ?>
			<div class="rpt-video-card__category">
				<?php if ( '' !== $category_url ) : ?>
					<a href="<?php echo esc_url( $category_url ); ?>"><?php echo esc_html( $category_name ); ?></a>
				<?php else : ?>
					<?php echo esc_html( $category_name ); ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( '' !== $date ) : ?>
			<div class="rpt-video-card__date">
				<span class="rpt-video-card__date-icon" aria-hidden="true"></span>
				<time datetime="<?php echo esc_attr( $date_iso ); ?>"><?php echo esc_html( $date ); ?></time>
			</div>
		<?php endif; ?>
	</div>
</article>
