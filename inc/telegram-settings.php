<?php
/**
 * Telegram notifications — admin settings and send helpers.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

define( 'RPT_TELEGRAM_OPTION', 'rpt_telegram_settings' );

/**
 * Default Telegram settings.
 *
 * @return array<string, mixed>
 */
function rpt_get_telegram_settings_defaults() {
	return array(
		'enabled'              => 0,
		'bot_token'            => '',
		'chat_id'              => '',
		'notify_quote_request' => 1,
		'notify_contact_form'  => 1,
	);
}

/**
 * Saved Telegram settings merged with defaults.
 *
 * @return array<string, mixed>
 */
function rpt_get_telegram_settings() {
	$saved = get_option( RPT_TELEGRAM_OPTION, array() );

	if ( ! is_array( $saved ) ) {
		$saved = array();
	}

	return wp_parse_args( $saved, rpt_get_telegram_settings_defaults() );
}

/**
 * Whether Telegram notifications are configured and enabled.
 *
 * @return bool
 */
function rpt_is_telegram_enabled() {
	$settings = rpt_get_telegram_settings();

	return ! empty( $settings['enabled'] )
		&& '' !== trim( (string) $settings['bot_token'] )
		&& '' !== trim( (string) $settings['chat_id'] );
}

/**
 * Register admin settings page.
 */
function rpt_register_telegram_settings_menu() {
	add_options_page(
		__( 'Cấu hình Telegram', 'generatepress_child' ),
		__( 'Telegram', 'generatepress_child' ),
		'manage_options',
		'rpt-telegram-settings',
		'rpt_render_telegram_settings_page'
	);
}
add_action( 'admin_menu', 'rpt_register_telegram_settings_menu' );

/**
 * Register Settings API fields.
 */
function rpt_register_telegram_settings() {
	register_setting(
		'rpt_telegram_settings_group',
		RPT_TELEGRAM_OPTION,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'rpt_sanitize_telegram_settings',
			'default'           => rpt_get_telegram_settings_defaults(),
		)
	);

	add_settings_section(
		'rpt_telegram_main',
		__( 'Kết nối Bot Telegram', 'generatepress_child' ),
		'rpt_render_telegram_settings_section_intro',
		'rpt-telegram-settings'
	);

	add_settings_field(
		'rpt_telegram_enabled',
		__( 'Bật thông báo', 'generatepress_child' ),
		'rpt_render_telegram_field_enabled',
		'rpt-telegram-settings',
		'rpt_telegram_main'
	);

	add_settings_field(
		'rpt_telegram_bot_token',
		__( 'Bot Token', 'generatepress_child' ),
		'rpt_render_telegram_field_bot_token',
		'rpt-telegram-settings',
		'rpt_telegram_main'
	);

	add_settings_field(
		'rpt_telegram_chat_id',
		__( 'Chat ID', 'generatepress_child' ),
		'rpt_render_telegram_field_chat_id',
		'rpt-telegram-settings',
		'rpt_telegram_main'
	);

	add_settings_section(
		'rpt_telegram_events',
		__( 'Sự kiện gửi thông báo', 'generatepress_child' ),
		'rpt_render_telegram_events_section_intro',
		'rpt-telegram-settings'
	);

	add_settings_field(
		'rpt_telegram_notify_quote',
		__( 'Yêu cầu báo giá', 'generatepress_child' ),
		'rpt_render_telegram_field_notify_quote',
		'rpt-telegram-settings',
		'rpt_telegram_events'
	);

	add_settings_field(
		'rpt_telegram_notify_contact',
		__( 'Form liên hệ', 'generatepress_child' ),
		'rpt_render_telegram_field_notify_contact',
		'rpt-telegram-settings',
		'rpt_telegram_events'
	);
}
add_action( 'admin_init', 'rpt_register_telegram_settings' );

/**
 * @param array<string, mixed> $input Raw input.
 * @return array<string, mixed>
 */
function rpt_sanitize_telegram_settings( $input ) {
	$defaults = rpt_get_telegram_settings_defaults();
	$current  = rpt_get_telegram_settings();
	$output   = $defaults;

	if ( ! is_array( $input ) ) {
		return $output;
	}

	$output['enabled']              = empty( $input['enabled'] ) ? 0 : 1;
	$output['notify_quote_request'] = empty( $input['notify_quote_request'] ) ? 0 : 1;
	$output['notify_contact_form']  = empty( $input['notify_contact_form'] ) ? 0 : 1;

	$token = isset( $input['bot_token'] ) ? trim( (string) $input['bot_token'] ) : '';

	if ( '' !== $token ) {
		$output['bot_token'] = preg_replace( '/[^0-9:A-Za-z_-]/', '', $token );
	} else {
		$output['bot_token'] = $current['bot_token'];
	}

	$chat_id = isset( $input['chat_id'] ) ? trim( (string) $input['chat_id'] ) : '';

	if ( '' !== $chat_id ) {
		$output['chat_id'] = preg_replace( '/[^0-9-]/', '', $chat_id );
	} else {
		$output['chat_id'] = $current['chat_id'];
	}

	return $output;
}

/**
 * Settings section intro.
 */
function rpt_render_telegram_settings_section_intro() {
	echo '<p>' . esc_html__( 'Nhận thông báo tức thì khi khách gửi yêu cầu báo giá hoặc form liên hệ. Tạo bot qua @BotFather trên Telegram, thêm bot vào nhóm/kênh và lấy Chat ID.', 'generatepress_child' ) . '</p>';
}

/**
 * Events section intro.
 */
function rpt_render_telegram_events_section_intro() {
	echo '<p>' . esc_html__( 'Chọn loại form sẽ gửi tin nhắn Telegram (song song với email).', 'generatepress_child' ) . '</p>';
}

/**
 * @return array<string, mixed>
 */
function rpt_get_telegram_settings_for_form() {
	return rpt_get_telegram_settings();
}

/**
 * Enabled checkbox.
 */
function rpt_render_telegram_field_enabled() {
	$settings = rpt_get_telegram_settings_for_form();
	printf(
		'<label><input type="checkbox" name="%1$s[enabled]" value="1" %2$s /> %3$s</label>',
		esc_attr( RPT_TELEGRAM_OPTION ),
		checked( ! empty( $settings['enabled'] ), true, false ),
		esc_html__( 'Gửi thông báo qua Telegram', 'generatepress_child' )
	);
}

/**
 * Bot token field.
 */
function rpt_render_telegram_field_bot_token() {
	$settings = rpt_get_telegram_settings_for_form();
	$has_token = '' !== trim( (string) $settings['bot_token'] );

	printf(
		'<input type="password" class="regular-text" name="%1$s[bot_token]" value="" placeholder="%2$s" autocomplete="new-password" />',
		esc_attr( RPT_TELEGRAM_OPTION ),
		$has_token ? esc_attr__( '•••••••• (đã lưu — để trống nếu không đổi)', 'generatepress_child' ) : esc_attr__( '123456789:ABCdefGHIjklMNOpqrsTUVwxyz', 'generatepress_child' )
	);

	if ( $has_token ) {
		echo '<p class="description">' . esc_html__( 'Bot token đã được lưu. Nhập token mới chỉ khi muốn thay đổi.', 'generatepress_child' ) . '</p>';
	}
}

/**
 * Chat ID field.
 */
function rpt_render_telegram_field_chat_id() {
	$settings = rpt_get_telegram_settings_for_form();

	printf(
		'<input type="text" class="regular-text" name="%1$s[chat_id]" value="%2$s" placeholder="-1001234567890" />',
		esc_attr( RPT_TELEGRAM_OPTION ),
		esc_attr( (string) $settings['chat_id'] )
	);

	echo '<p class="description">' . esc_html__( 'Chat ID cá nhân, nhóm hoặc kênh (nhóm thường bắt đầu bằng dấu -).', 'generatepress_child' ) . '</p>';
}

/**
 * Quote notification checkbox.
 */
function rpt_render_telegram_field_notify_quote() {
	$settings = rpt_get_telegram_settings_for_form();

	printf(
		'<label><input type="checkbox" name="%1$s[notify_quote_request]" value="1" %2$s /> %3$s</label>',
		esc_attr( RPT_TELEGRAM_OPTION ),
		checked( ! empty( $settings['notify_quote_request'] ), true, false ),
		esc_html__( 'Popup / modal yêu cầu báo giá', 'generatepress_child' )
	);
}

/**
 * Contact form notification checkbox.
 */
function rpt_render_telegram_field_notify_contact() {
	$settings = rpt_get_telegram_settings_for_form();

	printf(
		'<label><input type="checkbox" name="%1$s[notify_contact_form]" value="1" %2$s /> %3$s</label>',
		esc_attr( RPT_TELEGRAM_OPTION ),
		checked( ! empty( $settings['notify_contact_form'] ), true, false ),
		esc_html__( 'Form trang Liên hệ', 'generatepress_child' )
	);
}

/**
 * Render settings page.
 */
function rpt_render_telegram_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$test_result = isset( $_GET['rpt_telegram_test'] ) ? sanitize_key( wp_unslash( $_GET['rpt_telegram_test'] ) ) : '';
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Cấu hình Telegram', 'generatepress_child' ); ?></h1>

		<?php if ( 'ok' === $test_result ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Đã gửi tin nhắn thử thành công. Kiểm tra Telegram.', 'generatepress_child' ); ?></p></div>
		<?php elseif ( 'fail' === $test_result ) : ?>
			<div class="notice notice-error is-dismissible"><p><?php esc_html_e( 'Gửi thử thất bại. Kiểm tra Bot Token, Chat ID và quyền của bot trong nhóm.', 'generatepress_child' ); ?></p></div>
		<?php endif; ?>

		<form action="options.php" method="post">
			<?php
			settings_fields( 'rpt_telegram_settings_group' );
			do_settings_sections( 'rpt-telegram-settings' );
			submit_button( __( 'Lưu cấu hình', 'generatepress_child' ) );
			?>
		</form>

		<hr />

		<h2><?php esc_html_e( 'Kiểm tra kết nối', 'generatepress_child' ); ?></h2>
		<p><?php esc_html_e( 'Lưu cấu hình trước, sau đó gửi tin nhắn thử.', 'generatepress_child' ); ?></p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php wp_nonce_field( 'rpt_telegram_test', 'rpt_telegram_test_nonce' ); ?>
			<input type="hidden" name="action" value="rpt_telegram_test" />
			<?php submit_button( __( 'Gửi tin nhắn thử', 'generatepress_child' ), 'secondary', 'submit', false ); ?>
		</form>

		<h2><?php esc_html_e( 'Hướng dẫn nhanh', 'generatepress_child' ); ?></h2>
		<ol>
			<li><?php esc_html_e( 'Mở Telegram → tìm @BotFather → /newbot → lấy Bot Token.', 'generatepress_child' ); ?></li>
			<li><?php esc_html_e( 'Thêm bot vào nhóm nhận thông báo (hoặc chat riêng với bot).', 'generatepress_child' ); ?></li>
			<li><?php esc_html_e( 'Lấy Chat ID: gửi tin cho bot hoặc dùng @userinfobot / getUpdates API.', 'generatepress_child' ); ?></li>
			<li><?php esc_html_e( 'Dán Token + Chat ID → Lưu → Gửi tin nhắn thử.', 'generatepress_child' ); ?></li>
		</ol>
	</div>
	<?php
}

/**
 * Handle test message from admin.
 */
function rpt_handle_telegram_test() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Bạn không có quyền thực hiện thao tác này.', 'generatepress_child' ) );
	}

	check_admin_referer( 'rpt_telegram_test', 'rpt_telegram_test_nonce' );

	$site_name = get_bloginfo( 'name' );
	$message   = sprintf(
		"✅ %s\n\n%s\n%s: %s",
		__( 'Kết nối Telegram thành công!', 'generatepress_child' ),
		__( 'Website', 'generatepress_child' ),
		$site_name,
		home_url( '/' )
	);

	$sent = rpt_telegram_send_message( $message, true );

	$redirect = add_query_arg(
		'rpt_telegram_test',
		$sent ? 'ok' : 'fail',
		admin_url( 'options-general.php?page=rpt-telegram-settings' )
	);

	wp_safe_redirect( $redirect );
	exit;
}
add_action( 'admin_post_rpt_telegram_test', 'rpt_handle_telegram_test' );

/**
 * Send a plain-text message via Telegram Bot API.
 *
 * @param string $text        Message body.
 * @param bool   $force_send  Skip event toggles (for admin test).
 * @return bool
 */
function rpt_telegram_send_message( $text, $force_send = false ) {
	if ( ! $force_send && ! rpt_is_telegram_enabled() ) {
		return false;
	}

	$settings = rpt_get_telegram_settings();
	$token    = trim( (string) $settings['bot_token'] );
	$chat_id  = trim( (string) $settings['chat_id'] );

	if ( '' === $token || '' === $chat_id ) {
		return false;
	}

	$text = trim( (string) $text );

	if ( '' === $text ) {
		return false;
	}

	if ( function_exists( 'mb_substr' ) ) {
		$text = mb_substr( $text, 0, 4000 );
	} else {
		$text = substr( $text, 0, 4000 );
	}

	$response = wp_remote_post(
		'https://api.telegram.org/bot' . $token . '/sendMessage',
		array(
			'timeout' => 15,
			'body'    => array(
				'chat_id'                  => $chat_id,
				'text'                     => $text,
				'disable_web_page_preview' => 'true',
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		return false;
	}

	$code = (int) wp_remote_retrieve_response_code( $response );

	if ( $code < 200 || $code >= 300 ) {
		return false;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	return is_array( $body ) && ! empty( $body['ok'] );
}

/**
 * Notify Telegram about a quote request.
 *
 * @param array<string, string> $data    Quote data.
 * @param int                   $post_id Saved post ID.
 */
function rpt_telegram_notify_quote_request( $data, $post_id = 0 ) {
	$settings = rpt_get_telegram_settings();

	if ( empty( $settings['enabled'] ) || empty( $settings['notify_quote_request'] ) ) {
		return;
	}

	$lines = array(
		'📋 ' . __( 'Yêu cầu báo giá mới', 'generatepress_child' ),
		'',
		__( 'Họ và tên', 'generatepress_child' ) . ': ' . $data['name'],
		__( 'Điện thoại / Zalo', 'generatepress_child' ) . ': ' . $data['phone'],
	);

	if ( ! empty( $data['company'] ) ) {
		$lines[] = __( 'Công ty', 'generatepress_child' ) . ': ' . $data['company'];
	}

	if ( ! empty( $data['quantity'] ) ) {
		$lines[] = __( 'Số lượng', 'generatepress_child' ) . ': ' . $data['quantity'];
	}

	if ( ! empty( $data['product_name'] ) ) {
		$lines[] = __( 'Sản phẩm', 'generatepress_child' ) . ': ' . $data['product_name'];
	}

	if ( ! empty( $data['product_url'] ) ) {
		$lines[] = __( 'Link SP', 'generatepress_child' ) . ': ' . $data['product_url'];
	}

	$lines[] = '';
	$lines[] = __( 'Nội dung', 'generatepress_child' ) . ':';
	$lines[] = $data['message'];

	if ( $post_id ) {
		$lines[] = '';
		$lines[] = admin_url( 'post.php?post=' . (int) $post_id . '&action=edit' );
	}

	rpt_telegram_send_message( implode( "\n", $lines ) );
}

/**
 * Notify Telegram about a contact page inquiry.
 *
 * @param array<string, string> $data Inquiry data.
 */
function rpt_telegram_notify_contact_inquiry( $data ) {
	$settings = rpt_get_telegram_settings();

	if ( empty( $settings['enabled'] ) || empty( $settings['notify_contact_form'] ) ) {
		return;
	}

	$lines = array(
		'📩 ' . __( 'Form liên hệ mới', 'generatepress_child' ),
		'',
		'E-mail: ' . $data['email'],
		__( 'Điện thoại', 'generatepress_child' ) . ': ' . ( $data['phone'] ? $data['phone'] : '—' ),
		__( 'Họ và tên', 'generatepress_child' ) . ': ' . ( $data['name'] ? $data['name'] : '—' ),
		__( 'Công ty', 'generatepress_child' ) . ': ' . ( $data['company'] ? $data['company'] : '—' ),
	);

	if ( ! empty( $data['product_note'] ) ) {
		$lines[] = trim( $data['product_note'] );
	}

	$lines[] = '';
	$lines[] = __( 'Nội dung', 'generatepress_child' ) . ':';
	$lines[] = $data['message'];

	rpt_telegram_send_message( implode( "\n", $lines ) );
}
