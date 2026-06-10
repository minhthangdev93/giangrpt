<?php
/**
 * WooCommerce catalog mode — disable cart, checkout, and customer registration.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether catalog-only mode is active.
 *
 * @return bool
 */
function rpt_is_catalog_mode_enabled() {
	/**
	 * Filter catalog-only mode (no cart, checkout, or registration).
	 *
	 * @param bool $enabled Default true.
	 */
	return (bool) apply_filters( 'rpt_catalog_mode_enabled', true );
}

/**
 * Redirect target when cart or checkout is accessed.
 *
 * @return string
 */
function rpt_get_disabled_woo_page_redirect_url() {
	if ( function_exists( 'wc_get_page_permalink' ) ) {
		$shop = wc_get_page_permalink( 'shop' );

		if ( $shop ) {
			return $shop;
		}
	}

	return home_url( '/' );
}

/**
 * Bootstrap catalog mode hooks.
 */
function rpt_setup_woocommerce_catalog_mode() {
	if ( ! class_exists( 'WooCommerce' ) || ! rpt_is_catalog_mode_enabled() ) {
		return;
	}

	add_filter( 'woocommerce_is_purchasable', '__return_false' );
	add_filter( 'woocommerce_cart_redirect_after_error', 'rpt_get_disabled_woo_page_redirect_url' );
	add_filter( 'woocommerce_get_cart_url', 'rpt_get_disabled_woo_page_redirect_url' );
	add_filter( 'woocommerce_get_checkout_url', 'rpt_get_disabled_woo_page_redirect_url' );

	add_filter( 'woocommerce_enable_myaccount_registration', '__return_false' );
	add_filter( 'woocommerce_enable_signup_and_login_from_checkout', '__return_false' );
	add_filter( 'woocommerce_checkout_registration_enabled', '__return_false' );
	add_filter( 'woocommerce_checkout_registration_required', '__return_false' );

	add_filter( 'woocommerce_account_menu_items', 'rpt_remove_woocommerce_account_menu_items' );

	add_action( 'template_redirect', 'rpt_redirect_disabled_woocommerce_pages', 1 );
	add_action( 'wp_loaded', 'rpt_empty_woocommerce_cart_in_catalog_mode', 20 );
	add_action( 'wp_enqueue_scripts', 'rpt_dequeue_woocommerce_cart_assets', 100 );

	add_filter( 'woocommerce_add_to_cart_validation', 'rpt_block_add_to_cart_in_catalog_mode', 99, 1 );
	add_filter( 'option_users_can_register', 'rpt_disable_public_user_registration' );
	add_filter( 'register_url', 'rpt_disable_register_page_url' );
}
add_action( 'init', 'rpt_setup_woocommerce_catalog_mode', 20 );

/**
 * Block adding products to cart.
 *
 * @param bool $passed Validation result.
 * @return bool
 */
function rpt_block_add_to_cart_in_catalog_mode( $passed ) {
	if ( ! rpt_is_catalog_mode_enabled() ) {
		return $passed;
	}

	return false;
}

/**
 * Disable WordPress public registration.
 *
 * @param mixed $value Option value.
 * @return mixed
 */
function rpt_disable_public_user_registration( $value ) {
	if ( ! rpt_is_catalog_mode_enabled() || is_admin() ) {
		return $value;
	}

	return '0';
}

/**
 * Point register links to login page.
 *
 * @param string $url Register URL.
 * @return string
 */
function rpt_disable_register_page_url( $url ) {
	if ( ! rpt_is_catalog_mode_enabled() ) {
		return $url;
	}

	return wp_login_url();
}

/**
 * Redirect cart and checkout pages.
 */
function rpt_redirect_disabled_woocommerce_pages() {
	if ( is_admin() || wp_doing_ajax() ) {
		return;
	}

	if ( ! function_exists( 'is_cart' ) ) {
		return;
	}

	if ( is_cart() || is_checkout() ) {
		wp_safe_redirect( rpt_get_disabled_woo_page_redirect_url() );
		exit;
	}
}

/**
 * Keep cart empty in catalog mode.
 */
function rpt_empty_woocommerce_cart_in_catalog_mode() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return;
	}

	if ( WC()->cart->is_empty() ) {
		return;
	}

	WC()->cart->empty_cart( false );
}

/**
 * Remove cart-related scripts.
 */
function rpt_dequeue_woocommerce_cart_assets() {
	wp_dequeue_script( 'wc-cart-fragments' );
	wp_dequeue_script( 'wc-add-to-cart' );
}

/**
 * Remove order-related account menu items.
 *
 * @param array<string, string> $items Account menu items.
 * @return array<string, string>
 */
function rpt_remove_woocommerce_account_menu_items( $items ) {
	unset( $items['downloads'] );

	return $items;
}
