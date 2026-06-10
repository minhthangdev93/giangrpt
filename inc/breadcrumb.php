<?php
/**
 * Shared breadcrumb component.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render unified breadcrumb trail.
 *
 * @param array<int, array{label: string, url?: string}> $items Breadcrumb items.
 * @param array{echo?: bool}                             $args  Render args.
 * @return string
 */
function rpt_render_breadcrumb( $items, $args = array() ) {
	$echo = ! isset( $args['echo'] ) || $args['echo'];

	if ( empty( $items ) || ! is_array( $items ) ) {
		return '';
	}

	ob_start();
	?>
	<div class="rpt-breadcrumb-wrap">
		<nav class="rpt-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'generatepress_child' ); ?>">
			<ol class="rpt-breadcrumb__list">
				<?php
				$total = count( $items );

				foreach ( $items as $index => $item ) {
					if ( ! is_array( $item ) || empty( $item['label'] ) ) {
						continue;
					}

					$is_last = ( $index === $total - 1 );
					$label   = (string) $item['label'];
					$url     = isset( $item['url'] ) ? (string) $item['url'] : '';
					?>
					<li class="rpt-breadcrumb__item<?php echo $is_last ? ' rpt-breadcrumb__item--current' : ''; ?>"<?php echo $is_last ? ' aria-current="page"' : ''; ?>>
						<?php if ( ! $is_last && '' !== $url ) : ?>
							<a class="rpt-breadcrumb__link" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
						<?php else : ?>
							<span class="rpt-breadcrumb__current"><?php echo esc_html( $label ); ?></span>
						<?php endif; ?>
					</li>
					<?php
				}
				?>
			</ol>
		</nav>
	</div>
	<?php
	$html = ob_get_clean();

	if ( $echo ) {
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return '';
	}

	return $html;
}

/**
 * WooCommerce breadcrumb defaults aligned with RPT breadcrumb design.
 *
 * @param array<string, mixed> $defaults Breadcrumb defaults.
 * @return array<string, mixed>
 */
function rpt_woocommerce_breadcrumb_defaults( $defaults ) {
	$defaults['home']        = __( 'Trang chủ', 'generatepress_child' );
	$defaults['delimiter']   = '';
	$defaults['wrap_before'] = '<nav class="rpt-breadcrumb rpt-breadcrumb--woo" aria-label="' . esc_attr__( 'Breadcrumb', 'generatepress_child' ) . '"><ol class="rpt-breadcrumb__list">';
	$defaults['wrap_after']  = '</ol></nav>';
	$defaults['before']      = '';
	$defaults['after']       = '';

	return $defaults;
}
add_filter( 'woocommerce_breadcrumb_defaults', 'rpt_woocommerce_breadcrumb_defaults' );

/**
 * Output WooCommerce breadcrumb inside shared RPT wrapper.
 *
 * @param array<string, mixed> $args Breadcrumb args.
 */
function rpt_woocommerce_breadcrumb( $args = array() ) {
	if ( ! function_exists( 'woocommerce_breadcrumb' ) ) {
		return;
	}

	echo '<div class="rpt-breadcrumb-wrap">';
	woocommerce_breadcrumb( $args );
	echo '</div>';
}

/**
 * Render video hub breadcrumb.
 */
function rpt_render_video_breadcrumb() {
	rpt_render_breadcrumb(
		array(
			array(
				'label' => __( 'Trang chủ', 'generatepress_child' ),
				'url'   => home_url( '/' ),
			),
			array(
				'label' => __( 'Videos của chúng tôi', 'generatepress_child' ),
			),
		)
	);
}
