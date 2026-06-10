<?php
/**
 * Product technical specifications — ACF frontend section.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

global $product;

$product_id = ( $product instanceof WC_Product ) ? $product->get_id() : get_the_ID();

if ( ! $product_id || ! function_exists( 'rpt_product_has_technical_specs' ) || ! rpt_product_has_technical_specs( $product_id ) ) {
	return;
}

$spec_rows      = rpt_get_product_technical_spec_rows( $product_id );
$highlight_text = rpt_get_product_highlight_text( $product_id );
$spec_columns   = ! empty( $spec_rows ) ? rpt_split_technical_spec_columns( $spec_rows ) : array( array(), array() );

/**
 * Render one spec column.
 *
 * @param array<int, array{label: string, value: string}> $rows Column rows.
 */
$render_spec_column = static function ( array $rows ) {
	if ( empty( $rows ) ) {
		return;
	}
	?>
	<div class="rpt-spec-column">
		<?php foreach ( $rows as $row ) : ?>
			<div class="rpt-spec-row">
				<div class="rpt-spec-label"><?php echo esc_html( $row['label'] ); ?></div>
				<div class="rpt-spec-value"><?php echo rpt_format_spec_value( $row['value'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
};
?>
<section class="rpt-product-specs rpt-single-product-block" aria-labelledby="rpt-product-specs-heading">
	<div class="rpt-section-heading">
		<h2 id="rpt-product-specs-heading"><?php esc_html_e( 'Thông số kỹ thuật', 'generatepress_child' ); ?></h2>
		<span class="rpt-heading-line" aria-hidden="true"></span>
	</div>

	<div class="rpt-specs-table">
		<?php if ( ! empty( $spec_rows ) ) : ?>
			<div class="rpt-specs-grid">
				<?php
				$render_spec_column( $spec_columns[0] );
				$render_spec_column( $spec_columns[1] );
				?>
			</div>
		<?php endif; ?>

		<?php if ( '' !== $highlight_text ) : ?>
			<div class="rpt-spec-highlight">
				<div class="rpt-spec-label"><?php esc_html_e( 'Điểm nổi bật', 'generatepress_child' ); ?></div>
				<div class="rpt-spec-value"><?php echo rpt_format_spec_value( $highlight_text ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			</div>
		<?php endif; ?>
	</div>
</section>
