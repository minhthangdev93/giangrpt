<?php
/**
 * Homepage certificates.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$title = rpt_get_home_field( 'home_certificates_title', __( 'Giấy chứng nhận của chúng tôi', 'generatepress_child' ) );
$items = rpt_get_home_certificates();

if ( empty( $items ) ) {
	return;
}
?>
<section class="rpt-home-section rpt-home-certificates">
	<div class="rpt-container">
		<header class="rpt-home-section__header">
			<h2 class="rpt-home-section__title"><?php echo esc_html( $title ); ?></h2>
			<span class="rpt-home-section__accent" aria-hidden="true"></span>
		</header>

		<div class="rpt-home-certificates__grid">
			<?php foreach ( $items as $item ) : ?>
				<figure class="rpt-home-certificate">
					<button
						type="button"
						class="rpt-home-certificate__trigger"
						data-rpt-certificate-open
						data-rpt-certificate-src="<?php echo esc_url( $item['full_url'] ); ?>"
						data-rpt-certificate-title="<?php echo esc_attr( $item['title'] ); ?>"
						aria-label="<?php echo esc_attr( $item['title'] ? sprintf( __( 'Xem chứng nhận: %s', 'generatepress_child' ), $item['title'] ) : __( 'Xem giấy chứng nhận', 'generatepress_child' ) ); ?>"
					>
						<img
							class="rpt-home-certificate__image"
							src="<?php echo esc_url( $item['url'] ); ?>"
							alt="<?php echo esc_attr( $item['title'] ); ?>"
							loading="lazy"
							decoding="async"
						/>
					</button>
					<?php if ( $item['title'] ) : ?>
						<figcaption class="rpt-home-certificate__caption"><?php echo esc_html( $item['title'] ); ?></figcaption>
					<?php endif; ?>
				</figure>
			<?php endforeach; ?>
		</div>
	</div>
</section>
