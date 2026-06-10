<?php
/**
 * Certificate image lightbox shell.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;
?>
<div id="rpt-certificate-lightbox" class="rpt-certificate-lightbox" hidden aria-hidden="true">
	<div class="rpt-certificate-lightbox__backdrop" data-rpt-certificate-close tabindex="-1"></div>
	<div
		class="rpt-certificate-lightbox__dialog"
		role="dialog"
		aria-modal="true"
		aria-label="<?php esc_attr_e( 'Xem giấy chứng nhận', 'generatepress_child' ); ?>"
	>
		<button type="button" class="rpt-certificate-lightbox__close" data-rpt-certificate-close aria-label="<?php esc_attr_e( 'Đóng', 'generatepress_child' ); ?>">
			<span aria-hidden="true">&times;</span>
		</button>
		<figure class="rpt-certificate-lightbox__figure">
			<img class="rpt-certificate-lightbox__image" src="" alt="" />
			<figcaption class="rpt-certificate-lightbox__caption"></figcaption>
		</figure>
	</div>
</div>
