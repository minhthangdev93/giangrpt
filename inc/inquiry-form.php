<?php
/**
 * Contact page inquiry form submission.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

define( 'RPT_INQUIRY_MAX_FILES', 5 );
define( 'RPT_INQUIRY_MAX_FILE_SIZE', 10 * MB_IN_BYTES );

/**
 * Register inquiry form handlers.
 */
function rpt_register_inquiry_form_handlers() {
	add_action( 'admin_post_rpt_submit_inquiry', 'rpt_handle_inquiry_form_submit' );
	add_action( 'admin_post_nopriv_rpt_submit_inquiry', 'rpt_handle_inquiry_form_submit' );
}
add_action( 'init', 'rpt_register_inquiry_form_handlers' );

/**
 * Redirect back to contact page with status query arg.
 *
 * @param string $status success|error.
 * @param string $code   Optional error code.
 * @param string $referer Referer URL.
 */
function rpt_inquiry_redirect( $status, $code = '', $referer = '' ) {
	$redirect = $referer ? $referer : rpt_get_contact_url();

	$args = array( 'inquiry' => $status );

	if ( $code ) {
		$args['code'] = $code;
	}

	$target = add_query_arg( $args, $redirect ) . '#rpt-inquiry-form-actions';

	wp_safe_redirect( $target );
	exit;
}

/**
 * Handle inquiry form POST.
 */
function rpt_handle_inquiry_form_submit() {
	$referer = wp_get_referer();

	if ( ! $referer || ! wp_verify_nonce( isset( $_POST['rpt_inquiry_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['rpt_inquiry_nonce'] ) ) : '', 'rpt_submit_inquiry' ) ) {
		rpt_inquiry_redirect( 'error', 'nonce', $referer );
	}

	$email   = isset( $_POST['rpt_inquiry_email'] ) ? sanitize_email( wp_unslash( $_POST['rpt_inquiry_email'] ) ) : '';
	$phone   = isset( $_POST['rpt_inquiry_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['rpt_inquiry_phone'] ) ) : '';
	$name    = isset( $_POST['rpt_inquiry_name'] ) ? sanitize_text_field( wp_unslash( $_POST['rpt_inquiry_name'] ) ) : '';
	$company = isset( $_POST['rpt_inquiry_company'] ) ? sanitize_text_field( wp_unslash( $_POST['rpt_inquiry_company'] ) ) : '';
	$message = isset( $_POST['rpt_inquiry_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['rpt_inquiry_message'] ) ) : '';

	if ( ! is_email( $email ) ) {
		rpt_inquiry_redirect( 'error', 'email', $referer );
	}

	if ( '' === trim( $message ) ) {
		rpt_inquiry_redirect( 'error', 'message', $referer );
	}

	$attachments = rpt_process_inquiry_uploads();

	if ( is_wp_error( $attachments ) ) {
		rpt_inquiry_redirect( 'error', $attachments->get_error_code(), $referer );
	}

	$product_note = '';

	if ( ! empty( $_POST['rpt_inquiry_product'] ) ) {
		$product_slug = sanitize_title( wp_unslash( $_POST['rpt_inquiry_product'] ) );
		$product_note = $product_slug ? sprintf( "Sản phẩm quan tâm: %s\n", $product_slug ) : '';
	}

	$body_lines = array(
		'Yêu cầu báo giá mới từ trang Liên hệ',
		'',
		$product_note . 'E-mail: ' . $email,
		'Điện thoại / WhatsApp: ' . ( $phone ? $phone : '—' ),
		'Tên: ' . ( $name ? $name : '—' ),
		'Tên công ty: ' . ( $company ? $company : '—' ),
		'',
		'Nội dung:',
		$message,
	);

	$subject = sprintf(
		/* translators: %s: sender email */
		__( '[RPT Power] Yêu cầu báo giá — %s', 'generatepress_child' ),
		$email
	);

	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'Reply-To: ' . $email,
	);

	if ( function_exists( 'rpt_telegram_notify_contact_inquiry' ) ) {
		rpt_telegram_notify_contact_inquiry(
			array(
				'email'        => $email,
				'phone'        => $phone,
				'name'         => $name,
				'company'      => $company,
				'message'      => $message,
				'product_note' => $product_note,
			)
		);
	}

	$sent = wp_mail(
		rpt_get_inquiry_email(),
		$subject,
		implode( "\n", $body_lines ),
		$headers,
		is_array( $attachments ) ? $attachments : array()
	);

	if ( is_array( $attachments ) ) {
		foreach ( $attachments as $file ) {
			if ( is_string( $file ) && file_exists( $file ) ) {
				wp_delete_file( $file );
			}
		}
	}

	if ( ! $sent ) {
		rpt_inquiry_redirect( 'error', 'mail', $referer );
	}

	rpt_inquiry_redirect( 'success', '', $referer );
}

/**
 * Process uploaded inquiry files.
 *
 * @return array<int, string>|WP_Error
 */
function rpt_process_inquiry_uploads() {
	if ( empty( $_FILES['rpt_inquiry_files'] ) || ! is_array( $_FILES['rpt_inquiry_files'] ) ) {
		return array();
	}

	$files_field = $_FILES['rpt_inquiry_files']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

	if ( empty( $files_field['name'] ) || ! is_array( $files_field['name'] ) ) {
		return array();
	}

	$count = count( $files_field['name'] );

	if ( $count > RPT_INQUIRY_MAX_FILES ) {
		return new WP_Error( 'file_count', __( 'Too many files.', 'generatepress_child' ) );
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';

	$attachments = array();

	for ( $i = 0; $i < $count; $i++ ) {
		if ( empty( $files_field['name'][ $i ] ) ) {
			continue;
		}

		if ( ! empty( $files_field['error'][ $i ] ) && UPLOAD_ERR_NO_FILE !== (int) $files_field['error'][ $i ] ) {
			return new WP_Error( 'files', __( 'Upload error.', 'generatepress_child' ) );
		}

		if ( UPLOAD_ERR_NO_FILE === (int) $files_field['error'][ $i ] ) {
			continue;
		}

		if ( (int) $files_field['size'][ $i ] > RPT_INQUIRY_MAX_FILE_SIZE ) {
			return new WP_Error( 'file_size', __( 'File too large.', 'generatepress_child' ) );
		}

		$file = array(
			'name'     => $files_field['name'][ $i ],
			'type'     => $files_field['type'][ $i ],
			'tmp_name' => $files_field['tmp_name'][ $i ],
			'error'    => $files_field['error'][ $i ],
			'size'     => $files_field['size'][ $i ],
		);

		$upload = wp_handle_upload( $file, array( 'test_form' => false ) );

		if ( isset( $upload['error'] ) ) {
			return new WP_Error( 'files', $upload['error'] );
		}

		if ( ! empty( $upload['file'] ) ) {
			$attachments[] = $upload['file'];
		}
	}

	return $attachments;
}
