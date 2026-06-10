<?php
/**
 * Contact inquiry form.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$title       = rpt_get_contact_form_title();
$intro       = rpt_get_contact_form_intro();
$notice      = rpt_get_inquiry_flash_notice();
$product     = isset( $_GET['product'] ) ? sanitize_title( wp_unslash( $_GET['product'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$product_id  = isset( $_GET['product_id'] ) ? absint( $_GET['product_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
?>
<section class="rpt-contact-form-section" id="rpt-inquiry-form">
	<div class="rpt-contact-form-section__card">
		<h2 class="rpt-contact-form-section__title"><?php echo esc_html( $title ); ?></h2>

		<?php if ( '' !== $intro ) : ?>
			<p class="rpt-contact-form-section__intro"><?php echo esc_html( $intro ); ?></p>
		<?php endif; ?>

		<form
			class="rpt-inquiry-form rpt-contact-form"
			method="post"
			action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
			enctype="multipart/form-data"
			novalidate
		>
			<input type="hidden" name="action" value="rpt_submit_inquiry" />
			<?php wp_nonce_field( 'rpt_submit_inquiry', 'rpt_inquiry_nonce' ); ?>

			<?php if ( $product ) : ?>
				<input type="hidden" name="rpt_inquiry_product" value="<?php echo esc_attr( $product ); ?>" />
			<?php endif; ?>

			<?php if ( $product_id ) : ?>
				<input type="hidden" name="rpt_inquiry_product_id" value="<?php echo esc_attr( (string) $product_id ); ?>" />
			<?php endif; ?>

			<div class="rpt-inquiry-form__row rpt-inquiry-form__row--2col">
				<div class="rpt-field">
					<label class="rpt-label rpt-label--required" for="rpt-inquiry-email"><?php esc_html_e( 'E-mail', 'generatepress_child' ); ?></label>
					<input
						class="rpt-input"
						type="email"
						id="rpt-inquiry-email"
						name="rpt_inquiry_email"
						required
						autocomplete="email"
						placeholder="<?php esc_attr_e( 'your@email.com', 'generatepress_child' ); ?>"
					/>
				</div>

				<div class="rpt-field">
					<label class="rpt-label" for="rpt-inquiry-phone"><?php esc_html_e( 'Điện thoại / WhatsApp', 'generatepress_child' ); ?></label>
					<input
						class="rpt-input"
						type="text"
						id="rpt-inquiry-phone"
						name="rpt_inquiry_phone"
						autocomplete="tel"
						placeholder="<?php esc_attr_e( '+84 ...', 'generatepress_child' ); ?>"
					/>
				</div>
			</div>

			<div class="rpt-inquiry-form__row rpt-inquiry-form__row--2col">
				<div class="rpt-field">
					<label class="rpt-label" for="rpt-inquiry-name"><?php esc_html_e( 'Tên', 'generatepress_child' ); ?></label>
					<input
						class="rpt-input"
						type="text"
						id="rpt-inquiry-name"
						name="rpt_inquiry_name"
						autocomplete="name"
						placeholder="<?php esc_attr_e( 'Họ và tên', 'generatepress_child' ); ?>"
					/>
				</div>

				<div class="rpt-field">
					<label class="rpt-label" for="rpt-inquiry-company"><?php esc_html_e( 'Tên công ty', 'generatepress_child' ); ?></label>
					<input
						class="rpt-input"
						type="text"
						id="rpt-inquiry-company"
						name="rpt_inquiry_company"
						autocomplete="organization"
						placeholder="<?php esc_attr_e( 'Tên công ty của bạn', 'generatepress_child' ); ?>"
					/>
				</div>
			</div>

			<div class="rpt-field">
				<label class="rpt-label rpt-label--required" for="rpt-inquiry-message"><?php esc_html_e( 'Nội dung yêu cầu', 'generatepress_child' ); ?></label>
				<textarea
					class="rpt-textarea rpt-contact-form__message"
					id="rpt-inquiry-message"
					name="rpt_inquiry_message"
					required
					rows="8"
					placeholder="<?php esc_attr_e( 'Mô tả yêu cầu báo giá hoặc câu hỏi của bạn...', 'generatepress_child' ); ?>"
				></textarea>
			</div>

			<div class="rpt-field">
				<label class="rpt-label" for="rpt-inquiry-files"><?php esc_html_e( 'Đính kèm tệp', 'generatepress_child' ); ?></label>
				<input
					class="rpt-contact-form__file"
					type="file"
					id="rpt-inquiry-files"
					name="rpt_inquiry_files[]"
					multiple
					accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg,.zip"
				/>
				<p class="rpt-contact-form__file-hint">
					<?php esc_html_e( 'Bạn có thể tải lên tối đa 5 tệp, mỗi tệp không quá 10MB.', 'generatepress_child' ); ?>
				</p>
			</div>

			<div class="rpt-inquiry-form__actions" id="rpt-inquiry-form-actions">
				<?php if ( $notice ) : ?>
					<div class="rpt-contact-notice rpt-contact-notice--<?php echo esc_attr( $notice['status'] ); ?>" role="alert">
						<?php echo esc_html( $notice['message'] ); ?>
					</div>
				<?php endif; ?>

				<button class="rpt-btn rpt-btn-green rpt-contact-form__submit" type="submit">
					<?php esc_html_e( 'Yêu cầu báo giá', 'generatepress_child' ); ?>
				</button>
			</div>
		</form>
	</div>
</section>
