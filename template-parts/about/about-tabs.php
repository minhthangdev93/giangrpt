<?php
/**
 * About page content tabs.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$tabs = rpt_get_about_tabs();

if ( empty( $tabs ) ) {
	return;
}

$hide_nav = count( $tabs ) < 2;
?>
<section class="rpt-about-tabs" data-rpt-about-tabs>
	<?php if ( ! $hide_nav ) : ?>
		<div class="rpt-tabs-nav" role="tablist" aria-label="<?php esc_attr_e( 'Nội dung giới thiệu', 'generatepress_child' ); ?>">
			<?php foreach ( $tabs as $index => $tab ) : ?>
				<button
					type="button"
					class="rpt-tabs-nav__button<?php echo 0 === $index ? ' is-active' : ''; ?>"
					id="<?php echo esc_attr( 'rpt-about-tab-' . $index ); ?>"
					role="tab"
					aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>"
					aria-controls="<?php echo esc_attr( 'rpt-about-panel-' . $index ); ?>"
					data-rpt-tab-button
					data-tab-index="<?php echo esc_attr( (string) $index ); ?>"
				>
					<?php echo esc_html( $tab['title'] ); ?>
				</button>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<div class="rpt-tabs-content">
		<?php foreach ( $tabs as $index => $tab ) : ?>
			<div
				class="rpt-tab-panel<?php echo 0 === $index ? ' is-active' : ''; ?>"
				id="<?php echo esc_attr( 'rpt-about-panel-' . $index ); ?>"
				role="tabpanel"
				<?php echo ! $hide_nav ? 'aria-labelledby="' . esc_attr( 'rpt-about-tab-' . $index ) . '"' : ''; ?>
				<?php echo 0 === $index ? '' : ' hidden'; ?>
				data-rpt-tab-panel
				data-tab-index="<?php echo esc_attr( (string) $index ); ?>"
			>
				<div class="rpt-tab-panel__content">
					<?php echo wp_kses_post( $tab['content'] ); ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>
