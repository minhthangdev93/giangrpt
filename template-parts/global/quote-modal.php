<?php
/**
 * Global quote request modal.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;
?>
<div id="rpt-quote-modal" class="rpt-quote-modal" hidden aria-hidden="true">
	<div class="rpt-quote-modal__backdrop" data-rpt-quote-close tabindex="-1"></div>

	<div
		class="rpt-quote-modal__dialog"
		role="dialog"
		aria-modal="true"
		aria-labelledby="rpt-quote-modal-title"
	>
		<button type="button" class="rpt-quote-modal__close" data-rpt-quote-close aria-label="<?php esc_attr_e( 'Đóng', 'generatepress_child' ); ?>">
			<span aria-hidden="true">&times;</span>
		</button>

		<div class="rpt-quote-modal__header">
			<h2 id="rpt-quote-modal-title" class="rpt-quote-modal__title"><?php esc_html_e( 'Yêu cầu báo giá', 'generatepress_child' ); ?></h2>
			<p class="rpt-quote-modal__subtitle"><?php esc_html_e( 'Vui lòng điền thông tin bên dưới. Bộ phận kinh doanh sẽ liên hệ lại sớm nhất.', 'generatepress_child' ); ?></p>
		</div>

		<div class="rpt-quote-modal__product" data-rpt-quote-product-summary hidden>
			<span class="rpt-quote-modal__product-label"><?php esc_html_e( 'Sản phẩm quan tâm', 'generatepress_child' ); ?></span>
			<a class="rpt-quote-modal__product-link" href="#" data-rpt-quote-product-link target="_blank" rel="noopener noreferrer"></a>
		</div>

		<form class="rpt-quote-modal__form" data-rpt-quote-form novalidate>
			<input type="hidden" name="action" value="rpt_submit_quote_request" />
			<input type="hidden" name="nonce" value="" data-rpt-quote-nonce />
			<input type="hidden" name="rpt_quote_product_id" value="" data-rpt-quote-product-id />
			<input type="hidden" name="rpt_quote_product_name" value="" data-rpt-quote-product-name />
			<input type="hidden" name="rpt_quote_product_url" value="" data-rpt-quote-product-url />

			<div class="rpt-quote-modal__field">
				<label class="rpt-label rpt-label--required" for="rpt-quote-name"><?php esc_html_e( 'Họ và tên', 'generatepress_child' ); ?></label>
				<input class="rpt-input" type="text" id="rpt-quote-name" name="rpt_quote_name" autocomplete="name" required />
			</div>

			<div class="rpt-quote-modal__field">
				<label class="rpt-label rpt-label--required" for="rpt-quote-phone"><?php esc_html_e( 'Số điện thoại hoặc Zalo', 'generatepress_child' ); ?></label>
				<input class="rpt-input" type="tel" id="rpt-quote-phone" name="rpt_quote_phone" autocomplete="tel" inputmode="tel" required />
			</div>

			<div class="rpt-quote-modal__field">
				<label class="rpt-label" for="rpt-quote-company"><?php esc_html_e( 'Tên công ty', 'generatepress_child' ); ?></label>
				<input class="rpt-input" type="text" id="rpt-quote-company" name="rpt_quote_company" autocomplete="organization" />
			</div>

			<div class="rpt-quote-modal__field">
				<label class="rpt-label" for="rpt-quote-quantity"><?php esc_html_e( 'Số lượng', 'generatepress_child' ); ?></label>
				<input class="rpt-input" type="text" id="rpt-quote-quantity" name="rpt_quote_quantity" inputmode="numeric" placeholder="<?php esc_attr_e( 'Ví dụ: 10 bộ', 'generatepress_child' ); ?>" />
			</div>

			<div class="rpt-quote-modal__field">
				<label class="rpt-label rpt-label--required" for="rpt-quote-message"><?php esc_html_e( 'Nội dung yêu cầu', 'generatepress_child' ); ?></label>
				<textarea class="rpt-textarea" id="rpt-quote-message" name="rpt_quote_message" rows="4" required placeholder="<?php esc_attr_e( 'Mô tả nhu cầu, cấu hình hoặc câu hỏi của quý khách...', 'generatepress_child' ); ?>"></textarea>
			</div>

			<div class="rpt-quote-modal__feedback" data-rpt-quote-feedback hidden role="status" aria-live="polite"></div>

			<div class="rpt-quote-modal__actions">
				<button type="submit" class="rpt-btn rpt-btn-green rpt-quote-modal__submit" data-rpt-quote-submit>
					<?php esc_html_e( 'Gửi yêu cầu báo giá', 'generatepress_child' ); ?>
				</button>
			</div>
		</form>
	</div>
</div>
