<?php
/**
 * Product basic information — flexible ACF groups.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product instanceof WC_Product || ! function_exists( 'rpt_get_product_basic_info_groups' ) ) {
	return;
}

$groups = rpt_get_product_basic_info_groups( $product->get_id() );

if ( empty( $groups ) ) {
	return;
}
?>
<div class="rpt-product-basic-info">
	<?php foreach ( $groups as $group ) : ?>
		<div class="rpt-basic-info-group">
			<?php if ( '' !== $group['title'] ) : ?>
				<h3 class="rpt-basic-info-title"><?php echo esc_html( $group['title'] ); ?></h3>
			<?php endif; ?>

			<div class="rpt-basic-info-grid">
				<?php foreach ( $group['items'] as $item ) : ?>
					<?php
					$label_text = $item['label'];
					if ( ! preg_match( '/:\s*$/u', $label_text ) ) {
						$label_text .= ':';
					}
					?>
					<div class="rpt-basic-info-item">
						<span class="rpt-basic-info-label"><?php echo esc_html( $label_text ); ?></span>
						<span class="rpt-basic-info-value"><?php echo rpt_format_basic_info_value( $item['value'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endforeach; ?>
</div>
