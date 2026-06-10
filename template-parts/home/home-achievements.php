<?php
/**
 * Homepage achievements stats.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$title = rpt_get_home_field( 'home_achievements_title', __( 'Thành tựu của chúng tôi', 'generatepress_child' ) );
$items = rpt_get_home_achievements();

if ( empty( $items ) ) {
	return;
}
?>
<section class="rpt-home-section rpt-home-achievements">
	<div class="rpt-container">
		<header class="rpt-home-section__header">
			<h2 class="rpt-home-section__title"><?php echo esc_html( $title ); ?></h2>
			<span class="rpt-home-section__accent" aria-hidden="true"></span>
		</header>

		<div class="rpt-home-achievements__grid">
			<?php foreach ( $items as $item ) : ?>
				<div class="rpt-home-achievement">
					<span class="rpt-home-achievement__icon rpt-home-achievement__icon--<?php echo esc_attr( $item['icon'] ); ?>" aria-hidden="true"></span>
					<div class="rpt-home-achievement__value"><?php echo esc_html( $item['value'] ); ?></div>
					<div class="rpt-home-achievement__label"><?php echo esc_html( $item['label'] ); ?></div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
