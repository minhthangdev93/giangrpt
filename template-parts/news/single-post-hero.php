<?php
/**
 * Single post hero — featured image with title overlay.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$post_id    = get_the_ID();
$title      = get_the_title();
$date       = rpt_get_single_post_date_display( $post_id );
$thumb_id   = get_post_thumbnail_id( $post_id );
$timestamp  = get_post_timestamp( $post_id );
$datetime   = $timestamp ? wp_date( 'c', $timestamp ) : '';
?>
<section class="rpt-single-post-hero">
	<div class="rpt-single-post-hero__media<?php echo $thumb_id ? '' : ' rpt-single-post-hero__media--fallback'; ?>">
		<?php if ( $thumb_id ) : ?>
			<?php
			echo wp_get_attachment_image(
				$thumb_id,
				'full',
				false,
				array(
					'class'    => 'rpt-single-post-hero__image',
					'loading'  => 'eager',
					'decoding' => 'async',
					'alt'      => $title,
				)
			);
			?>
		<?php endif; ?>

		<div class="rpt-single-post-hero__shade" aria-hidden="true"></div>

		<div class="rpt-single-post-hero__overlay">
			<?php if ( $date ) : ?>
				<time class="rpt-single-post-hero__date" <?php echo $datetime ? 'datetime="' . esc_attr( $datetime ) . '"' : ''; ?>>
					<?php echo esc_html( $date ); ?>
				</time>
			<?php endif; ?>

			<h1 class="rpt-single-post-hero__title"><?php echo esc_html( $title ); ?></h1>
		</div>
	</div>
</section>
