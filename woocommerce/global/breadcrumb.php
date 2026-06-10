<?php
/**
 * Shop breadcrumb — RPT unified markup.
 *
 * @package GeneratePress_Child
 * @see     woocommerce_breadcrumb()
 * @version 2.3.0
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $breadcrumb ) ) {
	return;
}

echo $wrap_before; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

foreach ( $breadcrumb as $key => $crumb ) {
	$is_last = ( count( $breadcrumb ) === $key + 1 );
	?>
	<li class="rpt-breadcrumb__item<?php echo $is_last ? ' rpt-breadcrumb__item--current' : ''; ?>"<?php echo $is_last ? ' aria-current="page"' : ''; ?>>
		<?php if ( ! empty( $crumb[1] ) && ! $is_last ) : ?>
			<a class="rpt-breadcrumb__link" href="<?php echo esc_url( $crumb[1] ); ?>"><?php echo esc_html( $crumb[0] ); ?></a>
		<?php else : ?>
			<span class="rpt-breadcrumb__current"><?php echo esc_html( $crumb[0] ); ?></span>
		<?php endif; ?>
	</li>
	<?php
}

echo $wrap_after; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
