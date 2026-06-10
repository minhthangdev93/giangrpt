<?php
/**
 * Frontend performance — mobile/desktop optimizations compatible with LiteSpeed Cache.
 *
 * Uses standard WordPress enqueue, defer, and resource hints so LiteSpeed can
 * minify/combine/cache without theme conflicts. Does not set cache headers or
 * DONOTCACHEPAGE flags.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether theme performance optimizations are enabled.
 *
 * @return bool
 */
function rpt_is_performance_enabled() {
	/**
	 * Filter theme performance optimizations.
	 *
	 * @param bool $enabled Default true.
	 */
	return (bool) apply_filters( 'rpt_performance_enabled', true );
}

/**
 * Register responsive image sizes used across the theme.
 */
function rpt_register_image_sizes() {
	add_image_size( 'rpt-hero-banner', 1920, 0, false );
	add_image_size( 'rpt-hero-banner-tablet', 1200, 0, false );
	add_image_size( 'rpt-card-thumb', 480, 480, true );
}
add_action( 'after_setup_theme', 'rpt_register_image_sizes', 20 );

/**
 * Whether WooCommerce theme styles are needed on the current request.
 *
 * @return bool
 */
function rpt_needs_woocommerce_styles() {
	if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'is_woocommerce' ) ) {
		return false;
	}

	return is_woocommerce();
}

/**
 * Whether WooCommerce plugin frontend assets should load.
 *
 * @return bool
 */
function rpt_should_load_woocommerce_plugin_assets() {
	return rpt_needs_woocommerce_styles();
}

/**
 * Whether quote modal assets and markup are needed.
 *
 * @return bool
 */
function rpt_needs_quote_modal() {
	if ( is_admin() ) {
		return false;
	}

	/**
	 * Filter whether quote modal assets should load.
	 *
	 * @param bool $needs Default true — header CTA on all public pages.
	 */
	return (bool) apply_filters( 'rpt_needs_quote_modal', true );
}

/**
 * Bootstrap performance hooks.
 */
function rpt_setup_performance() {
	if ( ! rpt_is_performance_enabled() || is_admin() ) {
		return;
	}

	add_action( 'wp_enqueue_scripts', 'rpt_dequeue_frontend_bloat', 100 );
	add_action( 'wp_enqueue_scripts', 'rpt_dequeue_woocommerce_plugin_assets', 101 );
	add_filter( 'script_loader_tag', 'rpt_defer_theme_scripts', 10, 3 );
	add_filter( 'wp_preload_resources', 'rpt_preload_home_hero_lcp_image' );
	add_filter( 'wp_preload_resources', 'rpt_preload_local_fonts' );
}
add_action( 'init', 'rpt_setup_performance', 5 );

/**
 * Preload self-hosted body font files (critical for text LCP).
 *
 * @param array<int, array<string, mixed>> $preload_resources Preload resources.
 * @return array<int, array<string, mixed>>
 */
function rpt_preload_local_fonts( $preload_resources ) {
	$fonts_uri = get_stylesheet_directory_uri() . '/assets/fonts/';
	$files     = array(
		'hanken-grotesk-vietnamese.woff2',
		'hanken-grotesk-latin.woff2',
	);

	foreach ( $files as $file ) {
		$preload_resources[] = array(
			'href'          => $fonts_uri . $file,
			'as'            => 'font',
			'type'          => 'font/woff2',
			'crossorigin'   => 'anonymous',
			'fetchpriority' => 'high',
		);
	}

	return $preload_resources;
}

/**
 * Preload the homepage hero LCP image.
 *
 * @param array<int, array<string, mixed>> $preload_resources Preload resources.
 * @return array<int, array<string, mixed>>
 */
function rpt_preload_home_hero_lcp_image( $preload_resources ) {
	if ( ! function_exists( 'rpt_is_home_page' ) || ! rpt_is_home_page() ) {
		return $preload_resources;
	}

	if ( ! function_exists( 'rpt_get_home_hero_slides' ) ) {
		return $preload_resources;
	}

	$slides = rpt_get_home_hero_slides();

	if ( empty( $slides[0]['attachment_id'] ) ) {
		return $preload_resources;
	}

	$url = wp_get_attachment_image_url( (int) $slides[0]['attachment_id'], 'rpt-hero-banner' );

	if ( ! $url ) {
		return $preload_resources;
	}

	$preload_resources[] = array(
		'href'          => $url,
		'as'            => 'image',
		'fetchpriority' => 'high',
		'type'          => wp_check_filetype( $url )['type'],
	);

	return $preload_resources;
}

/**
 * Defer non-critical theme scripts (LiteSpeed-compatible).
 *
 * @param string $tag    Script tag.
 * @param string $handle Script handle.
 * @param string $src    Script URL.
 * @return string
 */
function rpt_defer_theme_scripts( $tag, $handle, $src ) {
	if ( 0 !== strpos( $handle, 'rpt-' ) ) {
		return $tag;
	}

	if ( false !== strpos( $tag, ' defer' ) || false !== strpos( $tag, ' async' ) ) {
		return $tag;
	}

	return str_replace( '<script ', '<script defer ', $tag );
}

/**
 * Remove scripts/styles not needed on the public frontend.
 */
function rpt_dequeue_frontend_bloat() {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'wc-blocks-style' );
	wp_dequeue_style( 'wc-blocks-vendors-style' );
	wp_dequeue_style( 'classic-theme-styles' );

	if ( ! is_user_logged_in() ) {
		wp_dequeue_style( 'dashicons' );
	}

	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	wp_deregister_script( 'wp-embed' );

	if ( ! is_user_logged_in() ) {
		wp_deregister_script( 'heartbeat' );
	}
}

/**
 * Load WooCommerce plugin assets only on shop/product views.
 */
function rpt_dequeue_woocommerce_plugin_assets() {
	if ( rpt_should_load_woocommerce_plugin_assets() ) {
		return;
	}

	$styles = array(
		'woocommerce-general',
		'woocommerce-layout',
		'woocommerce-smallscreen',
		'woocommerce-inline',
		'wc-blocks-style',
		'wc-blocks-vendors-style',
	);

	foreach ( $styles as $handle ) {
		wp_dequeue_style( $handle );
		wp_deregister_style( $handle );
	}

	$scripts = array(
		'woocommerce',
		'wc-add-to-cart',
		'wc-cart-fragments',
		'wc-single-product',
		'wc-add-to-cart-variation',
		'sourcebuster-js',
		'wc-order-attribution',
	);

	foreach ( $scripts as $handle ) {
		wp_dequeue_script( $handle );
		wp_deregister_script( $handle );
	}
}

/**
 * Default args for theme scripts (footer + defer).
 *
 * @return array{in_footer: bool, strategy: string}
 */
function rpt_get_theme_script_args() {
	return array(
		'in_footer' => true,
		'strategy'  => 'defer',
	);
}
