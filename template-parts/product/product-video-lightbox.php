<?php
/**
 * Product video lightbox shell.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;
?>
<div id="rpt-product-video-lightbox" class="rpt-video-lightbox" hidden aria-hidden="true">
	<div class="rpt-video-lightbox__backdrop" data-rpt-video-close tabindex="-1"></div>
	<div
		class="rpt-video-lightbox__dialog"
		role="dialog"
		aria-modal="true"
		aria-label="<?php esc_attr_e( 'Xem video sản phẩm', 'generatepress_child' ); ?>"
	>
		<button type="button" class="rpt-video-lightbox__close" data-rpt-video-close aria-label="<?php esc_attr_e( 'Đóng', 'generatepress_child' ); ?>">
			<span aria-hidden="true">&times;</span>
		</button>
		<div class="rpt-video-lightbox__content"></div>
	</div>
</div>
