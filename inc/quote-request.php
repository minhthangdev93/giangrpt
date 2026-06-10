<?php
/**
 * Quote request modal, storage, and AJAX handler.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

define( 'RPT_QUOTE_REQUEST_POST_TYPE', 'rpt_quote_request' );

/**
 * Register quote request post type for admin tracking.
 */
function rpt_register_quote_request_post_type() {
	register_post_type(
		RPT_QUOTE_REQUEST_POST_TYPE,
		array(
			'labels'              => array(
				'name'               => __( 'Yêu cầu báo giá', 'generatepress_child' ),
				'singular_name'      => __( 'Yêu cầu báo giá', 'generatepress_child' ),
				'menu_name'          => __( 'Yêu cầu báo giá', 'generatepress_child' ),
				'all_items'          => __( 'Tất cả yêu cầu', 'generatepress_child' ),
				'view_item'          => __( 'Xem yêu cầu', 'generatepress_child' ),
				'search_items'       => __( 'Tìm yêu cầu', 'generatepress_child' ),
				'not_found'          => __( 'Chưa có yêu cầu nào.', 'generatepress_child' ),
				'not_found_in_trash' => __( 'Không tìm thấy trong thùng rác.', 'generatepress_child' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_icon'           => 'dashicons-clipboard',
			'menu_position'       => 26,
			'supports'            => array( 'title' ),
			'capability_type'     => 'post',
			'capabilities'        => array(
				'create_posts' => 'do_not_allow',
			),
			'map_meta_cap'        => true,
			'exclude_from_search' => true,
		)
	);
}
add_action( 'init', 'rpt_register_quote_request_post_type' );

/**
 * Admin list columns for quote requests.
 *
 * @param array<string, string> $columns Columns.
 * @return array<string, string>
 */
function rpt_quote_request_admin_columns( $columns ) {
	$new = array();

	foreach ( $columns as $key => $label ) {
		$new[ $key ] = $label;

		if ( 'title' === $key ) {
			$new['rpt_quote_customer'] = __( 'Khách hàng', 'generatepress_child' );
			$new['rpt_quote_product']  = __( 'Sản phẩm', 'generatepress_child' );
			$new['rpt_quote_phone']    = __( 'Liên hệ', 'generatepress_child' );
		}
	}

	return $new;
}
add_filter( 'manage_' . RPT_QUOTE_REQUEST_POST_TYPE . '_posts_columns', 'rpt_quote_request_admin_columns' );

/**
 * Render admin list column values.
 *
 * @param string $column  Column key.
 * @param int    $post_id Post ID.
 */
function rpt_quote_request_admin_column_content( $column, $post_id ) {
	switch ( $column ) {
		case 'rpt_quote_customer':
			echo esc_html( get_post_meta( $post_id, '_rpt_quote_name', true ) );
			break;
		case 'rpt_quote_product':
			$name = get_post_meta( $post_id, '_rpt_quote_product_name', true );
			$url  = get_post_meta( $post_id, '_rpt_quote_product_url', true );

			if ( $name && $url ) {
				printf(
					'<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
					esc_url( $url ),
					esc_html( $name )
				);
			} elseif ( $name ) {
				echo esc_html( $name );
			} else {
				echo '—';
			}
			break;
		case 'rpt_quote_phone':
			echo esc_html( get_post_meta( $post_id, '_rpt_quote_phone', true ) );
			break;
	}
}
add_action( 'manage_' . RPT_QUOTE_REQUEST_POST_TYPE . '_posts_custom_column', 'rpt_quote_request_admin_column_content', 10, 2 );

/**
 * Meta box with full quote request details.
 */
function rpt_register_quote_request_meta_box() {
	add_meta_box(
		'rpt_quote_request_details',
		__( 'Chi tiết yêu cầu', 'generatepress_child' ),
		'rpt_render_quote_request_meta_box',
		RPT_QUOTE_REQUEST_POST_TYPE,
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'rpt_register_quote_request_meta_box' );

/**
 * @param WP_Post $post Post object.
 */
function rpt_render_quote_request_meta_box( $post ) {
	$fields = array(
		__( 'Họ và tên', 'generatepress_child' )         => get_post_meta( $post->ID, '_rpt_quote_name', true ),
		__( 'Số điện thoại / Zalo', 'generatepress_child' ) => get_post_meta( $post->ID, '_rpt_quote_phone', true ),
		__( 'Tên công ty', 'generatepress_child' )       => get_post_meta( $post->ID, '_rpt_quote_company', true ),
		__( 'Số lượng', 'generatepress_child' )          => get_post_meta( $post->ID, '_rpt_quote_quantity', true ),
		__( 'Tên sản phẩm', 'generatepress_child' )      => get_post_meta( $post->ID, '_rpt_quote_product_name', true ),
		__( 'URL sản phẩm', 'generatepress_child' )      => get_post_meta( $post->ID, '_rpt_quote_product_url', true ),
		__( 'Nội dung yêu cầu', 'generatepress_child' )  => get_post_meta( $post->ID, '_rpt_quote_message', true ),
	);

	echo '<table class="form-table" role="presentation"><tbody>';

	foreach ( $fields as $label => $value ) {
		echo '<tr><th scope="row">' . esc_html( $label ) . '</th><td>';

		if ( __( 'URL sản phẩm', 'generatepress_child' ) === $label && $value ) {
			printf(
				'<a href="%1$s" target="_blank" rel="noopener noreferrer">%1$s</a>',
				esc_url( $value )
			);
		} elseif ( __( 'Nội dung yêu cầu', 'generatepress_child' ) === $label ) {
			echo '<div style="white-space:pre-wrap;">' . esc_html( $value ? $value : '—' ) . '</div>';
		} else {
			echo esc_html( $value ? $value : '—' );
		}

		echo '</td></tr>';
	}

	echo '</tbody></table>';
}

/**
 * Success message after quote submission.
 *
 * @return string
 */
function rpt_get_quote_success_message() {
	return __( 'Cảm ơn quý khách! Yêu cầu báo giá của quý khách đã được tiếp nhận. Bộ phận kinh doanh sẽ liên hệ lại trong thời gian sớm nhất.', 'generatepress_child' );
}

/**
 * Save quote request and send admin email.
 *
 * @param array<string, string> $data Sanitized quote data.
 * @return int|WP_Error Post ID or error.
 */
function rpt_store_quote_request( $data ) {
	$title = sprintf(
		/* translators: 1: customer name, 2: product name or generic label */
		__( 'Báo giá — %1$s — %2$s', 'generatepress_child' ),
		$data['name'],
		$data['product_name'] ? $data['product_name'] : __( 'Không chỉ định SP', 'generatepress_child' )
	);

	$post_id = wp_insert_post(
		array(
			'post_type'   => RPT_QUOTE_REQUEST_POST_TYPE,
			'post_status' => 'publish',
			'post_title'  => $title,
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		return $post_id;
	}

	$meta_map = array(
		'_rpt_quote_name'         => $data['name'],
		'_rpt_quote_phone'        => $data['phone'],
		'_rpt_quote_company'      => $data['company'],
		'_rpt_quote_quantity'     => $data['quantity'],
		'_rpt_quote_message'      => $data['message'],
		'_rpt_quote_product_id'   => $data['product_id'],
		'_rpt_quote_product_name' => $data['product_name'],
		'_rpt_quote_product_url'  => $data['product_url'],
	);

	foreach ( $meta_map as $key => $value ) {
		update_post_meta( $post_id, $key, $value );
	}

	$body_lines = array(
		'Yêu cầu báo giá mới',
		'',
		'Họ và tên: ' . $data['name'],
		'Số điện thoại / Zalo: ' . $data['phone'],
		'Tên công ty: ' . ( $data['company'] ? $data['company'] : '—' ),
		'Số lượng: ' . ( $data['quantity'] ? $data['quantity'] : '—' ),
	);

	if ( $data['product_name'] ) {
		$body_lines[] = 'Sản phẩm: ' . $data['product_name'];
	}

	if ( $data['product_url'] ) {
		$body_lines[] = 'URL sản phẩm: ' . $data['product_url'];
	}

	$body_lines[] = '';
	$body_lines[] = 'Nội dung yêu cầu:';
	$body_lines[] = $data['message'];
	$body_lines[] = '';
	$body_lines[] = 'Xem trong quản trị: ' . admin_url( 'post.php?post=' . $post_id . '&action=edit' );

	$subject = sprintf(
		/* translators: %s: customer name */
		__( '[RPT Power] Yêu cầu báo giá — %s', 'generatepress_child' ),
		$data['name']
	);

	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
	);

	if ( ! empty( $data['phone'] ) ) {
		$headers[] = 'Reply-To: ' . $data['name'] . ' <noreply@' . wp_parse_url( home_url(), PHP_URL_HOST ) . '>';
	}

	$sent = wp_mail(
		rpt_get_inquiry_email(),
		$subject,
		implode( "\n", $body_lines ),
		$headers
	);

	if ( ! $sent ) {
		return new WP_Error( 'mail', __( 'Không thể gửi email.', 'generatepress_child' ) );
	}

	return $post_id;
}

/**
 * AJAX: submit quote request from modal.
 */
function rpt_handle_quote_request_ajax() {
	check_ajax_referer( 'rpt_submit_quote_request', 'nonce' );

	$name     = isset( $_POST['rpt_quote_name'] ) ? sanitize_text_field( wp_unslash( $_POST['rpt_quote_name'] ) ) : '';
	$phone    = isset( $_POST['rpt_quote_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['rpt_quote_phone'] ) ) : '';
	$company  = isset( $_POST['rpt_quote_company'] ) ? sanitize_text_field( wp_unslash( $_POST['rpt_quote_company'] ) ) : '';
	$quantity = isset( $_POST['rpt_quote_quantity'] ) ? sanitize_text_field( wp_unslash( $_POST['rpt_quote_quantity'] ) ) : '';
	$message  = isset( $_POST['rpt_quote_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['rpt_quote_message'] ) ) : '';
	$product_id   = isset( $_POST['rpt_quote_product_id'] ) ? absint( wp_unslash( $_POST['rpt_quote_product_id'] ) ) : 0;
	$product_name = isset( $_POST['rpt_quote_product_name'] ) ? sanitize_text_field( wp_unslash( $_POST['rpt_quote_product_name'] ) ) : '';
	$product_url  = isset( $_POST['rpt_quote_product_url'] ) ? esc_url_raw( wp_unslash( $_POST['rpt_quote_product_url'] ) ) : '';

	if ( '' === trim( $name ) ) {
		wp_send_json_error(
			array( 'message' => __( 'Vui lòng nhập họ và tên.', 'generatepress_child' ) ),
			400
		);
	}

	if ( '' === trim( $phone ) ) {
		wp_send_json_error(
			array( 'message' => __( 'Vui lòng nhập số điện thoại hoặc Zalo.', 'generatepress_child' ) ),
			400
		);
	}

	if ( '' === trim( $message ) ) {
		wp_send_json_error(
			array( 'message' => __( 'Vui lòng nhập nội dung yêu cầu.', 'generatepress_child' ) ),
			400
		);
	}

	if ( $product_id && function_exists( 'wc_get_product' ) ) {
		$product = wc_get_product( $product_id );

		if ( $product instanceof WC_Product ) {
			if ( ! $product_name ) {
				$product_name = $product->get_name();
			}

			if ( ! $product_url ) {
				$product_url = get_permalink( $product_id );
			}
		}
	}

	$result = rpt_store_quote_request(
		array(
			'name'         => $name,
			'phone'        => $phone,
			'company'      => $company,
			'quantity'     => $quantity,
			'message'      => $message,
			'product_id'   => $product_id ? (string) $product_id : '',
			'product_name' => $product_name,
			'product_url'  => $product_url,
		)
	);

	if ( is_wp_error( $result ) ) {
		wp_send_json_error(
			array( 'message' => __( 'Không thể gửi yêu cầu lúc này. Vui lòng thử lại sau.', 'generatepress_child' ) ),
			500
		);
	}

	wp_send_json_success(
		array(
			'message' => rpt_get_quote_success_message(),
		)
	);
}
add_action( 'wp_ajax_rpt_submit_quote_request', 'rpt_handle_quote_request_ajax' );
add_action( 'wp_ajax_nopriv_rpt_submit_quote_request', 'rpt_handle_quote_request_ajax' );

/**
 * HTML attributes for opening the quote modal from a product context.
 *
 * @param WC_Product|null $product Product.
 * @return string
 */
function rpt_get_quote_open_button_attrs( $product = null ) {
	$attrs = array(
		'data-rpt-quote-open' => 'data-rpt-quote-open',
	);

	if ( $product instanceof WC_Product ) {
		$attrs['data-rpt-quote-product-id']   = (string) $product->get_id();
		$attrs['data-rpt-quote-product-name'] = $product->get_name();
		$attrs['data-rpt-quote-product-url']  = get_permalink( $product->get_id() );
	}

	$parts = array();

	foreach ( $attrs as $key => $value ) {
		if ( 'data-rpt-quote-open' === $key ) {
			$parts[] = $key;
			continue;
		}

		$parts[] = sprintf( '%s="%s"', $key, esc_attr( $value ) );
	}

	return implode( ' ', $parts );
}

/**
 * Render a quote modal CTA button (footer, sidebar, etc.).
 *
 * @param array{label?: string, class?: string, product?: WC_Product|null} $args Button args.
 */
function rpt_render_quote_cta_button( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'label'      => rpt_get_shop_hub_cta_label(),
			'class'      => '',
			'product'    => null,
			'close_menu' => false,
		)
	);

	$classes = trim( 'rpt-quote-cta rpt-btn ' . $args['class'] );
	$attrs   = 'data-rpt-quote-open';

	if ( $args['close_menu'] ) {
		$attrs .= ' data-rpt-menu-close';
	}

	if ( $args['product'] instanceof WC_Product ) {
		printf(
			'<button type="button" class="%1$s" %2$s %3$s>%4$s</button>',
			esc_attr( $classes ),
			$attrs, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Fixed safe attributes.
			rpt_get_quote_open_button_attrs( $args['product'] ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper.
			esc_html( $args['label'] )
		);
		return;
	}

	printf(
		'<button type="button" class="%1$s" %2$s>%3$s</button>',
		esc_attr( $classes ),
		$attrs, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Fixed safe attributes.
		esc_html( $args['label'] )
	);
}

/**
 * Enqueue quote modal assets.
 */
function rpt_enqueue_quote_modal_assets() {
	if ( function_exists( 'rpt_needs_quote_modal' ) && ! rpt_needs_quote_modal() ) {
		return;
	}

	wp_enqueue_style(
		'rpt-quote-modal',
		get_stylesheet_directory_uri() . '/assets/css/quote-modal.css',
		array( 'rpt-components' ),
		rpt_get_asset_version( 'assets/css/quote-modal.css' )
	);

	wp_enqueue_script(
		'rpt-quote-modal',
		get_stylesheet_directory_uri() . '/assets/js/quote-modal.js',
		array( 'rpt-scroll-lock' ),
		rpt_get_asset_version( 'assets/js/quote-modal.js' ),
		rpt_get_theme_script_args()
	);

	wp_localize_script(
		'rpt-quote-modal',
		'rptQuoteModal',
		array(
			'ajaxUrl'        => admin_url( 'admin-ajax.php' ),
			'nonce'          => wp_create_nonce( 'rpt_submit_quote_request' ),
			'successMessage' => rpt_get_quote_success_message(),
			'errorMessage'   => __( 'Không thể gửi yêu cầu. Vui lòng kiểm tra lại thông tin.', 'generatepress_child' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'rpt_enqueue_quote_modal_assets', 32 );

/**
 * Render quote modal in footer.
 */
function rpt_render_quote_modal() {
	if ( function_exists( 'rpt_needs_quote_modal' ) && ! rpt_needs_quote_modal() ) {
		return;
	}

	get_template_part( 'template-parts/global/quote', 'modal' );
}
add_action( 'wp_footer', 'rpt_render_quote_modal' );
