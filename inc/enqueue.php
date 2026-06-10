<?php
/**
 * Enqueue styles and scripts.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue Google Fonts and RPT design system stylesheets.
 */
function rpt_enqueue_design_system() {
	$assets_uri = get_stylesheet_directory_uri() . '/assets/css/';
	$assets_dir = 'assets/css/';

	wp_enqueue_style(
		'rpt-fonts',
		get_stylesheet_directory_uri() . '/assets/css/fonts.css',
		array(),
		rpt_get_asset_version( 'assets/css/fonts.css' )
	);

	$styles = array(
		'rpt-variables'     => 'variables.css',
		'rpt-base'          => 'base.css',
		'rpt-components'    => 'components.css',
		'rpt-responsive'    => 'responsive.css',
		'rpt-layout-header' => 'layout-header.css',
		'rpt-layout-footer' => 'layout-footer.css',
	);

	if ( function_exists( 'rpt_needs_woocommerce_styles' ) && rpt_needs_woocommerce_styles() ) {
		$styles['rpt-woocommerce'] = 'woocommerce.css';
	}

	$deps = array( 'rpt-fonts' );

	foreach ( $styles as $handle => $file ) {
		wp_enqueue_style(
			$handle,
			$assets_uri . $file,
			$deps,
			rpt_get_asset_version( $assets_dir . $file )
		);
		$deps = array( $handle );
	}

	$load_catalog_assets = function_exists( 'rpt_uses_custom_catalog_layout' ) && rpt_uses_custom_catalog_layout();

	if ( $load_catalog_assets ) {
		wp_enqueue_style(
			'rpt-product-hub',
			$assets_uri . 'product-hub.css',
			array( 'rpt-layout-footer' ),
			rpt_get_asset_version( 'assets/css/product-hub.css' )
		);

		wp_enqueue_script(
			'rpt-product-hub',
			get_stylesheet_directory_uri() . '/assets/js/product-hub.js',
			array(),
			rpt_get_asset_version( 'assets/js/product-hub.js' ),
			rpt_get_theme_script_args()
		);
	}

	if ( function_exists( 'rpt_is_product_category_archive' ) && rpt_is_product_category_archive() ) {
		wp_enqueue_style(
			'rpt-product-category',
			$assets_uri . 'product-category.css',
			array( 'rpt-product-hub' ),
			rpt_get_asset_version( 'assets/css/product-category.css' )
		);
	}

	if ( function_exists( 'rpt_uses_video_layout' ) && rpt_uses_video_layout() ) {
		wp_enqueue_style(
			'rpt-video',
			$assets_uri . 'video.css',
			array( 'rpt-layout-footer' ),
			rpt_get_asset_version( 'assets/css/video.css' )
		);
	}

	if ( function_exists( 'rpt_is_about_page' ) && rpt_is_about_page() ) {
		wp_enqueue_style(
			'rpt-about',
			$assets_uri . 'about.css',
			array( 'rpt-layout-footer' ),
			rpt_get_asset_version( 'assets/css/about.css' )
		);

		wp_enqueue_script(
			'rpt-about-tabs',
			get_stylesheet_directory_uri() . '/assets/js/about-tabs.js',
			array(),
			rpt_get_asset_version( 'assets/js/about-tabs.js' ),
			rpt_get_theme_script_args()
		);
	}

	if ( function_exists( 'rpt_is_contact_page' ) && rpt_is_contact_page() ) {
		wp_enqueue_style(
			'rpt-contact',
			$assets_uri . 'contact.css',
			array( 'rpt-layout-footer' ),
			rpt_get_asset_version( 'assets/css/contact.css' )
		);
	}

	if ( function_exists( 'rpt_is_home_page' ) && rpt_is_home_page() ) {
		wp_enqueue_style(
			'rpt-home',
			$assets_uri . 'home.css',
			array( 'rpt-layout-footer' ),
			rpt_get_asset_version( 'assets/css/home.css' )
		);

		wp_enqueue_style(
			'rpt-news',
			$assets_uri . 'news.css',
			array( 'rpt-home' ),
			rpt_get_asset_version( 'assets/css/news.css' )
		);

		wp_enqueue_script(
			'rpt-home-carousel',
			get_stylesheet_directory_uri() . '/assets/js/home-carousel.js',
			array(),
			rpt_get_asset_version( 'assets/js/home-carousel.js' ),
			rpt_get_theme_script_args()
		);
	}

	if ( function_exists( 'rpt_uses_news_layout' ) && rpt_uses_news_layout() ) {
		wp_enqueue_style(
			'rpt-news',
			$assets_uri . 'news.css',
			array( 'rpt-layout-footer' ),
			rpt_get_asset_version( 'assets/css/news.css' )
		);
	}

	if ( function_exists( 'rpt_is_single_product_page' ) && rpt_is_single_product_page() ) {
		if ( ! $load_catalog_assets ) {
			wp_enqueue_style(
				'rpt-product-hub',
				$assets_uri . 'product-hub.css',
				array( 'rpt-layout-footer' ),
				rpt_get_asset_version( 'assets/css/product-hub.css' )
			);
		}

		wp_enqueue_style(
			'rpt-single-product',
			$assets_uri . 'single-product.css',
			array( 'rpt-product-hub' ),
			rpt_get_asset_version( 'assets/css/single-product.css' )
		);

		wp_enqueue_style(
			'rpt-product-specs',
			$assets_uri . 'product-specs.css',
			array( 'rpt-single-product' ),
			rpt_get_asset_version( 'assets/css/product-specs.css' )
		);

		wp_enqueue_script(
			'rpt-single-product-gallery',
			get_stylesheet_directory_uri() . '/assets/js/single-product-gallery.js',
			array(),
			rpt_get_asset_version( 'assets/js/single-product-gallery.js' ),
			rpt_get_theme_script_args()
		);
	}
}
add_action( 'wp_enqueue_scripts', 'rpt_enqueue_design_system', 20 );

/**
 * Enqueue child theme style.css after design system.
 */
function rpt_enqueue_child_style() {
	wp_enqueue_style(
		'generatepress-child',
		get_stylesheet_uri(),
		array( 'rpt-layout-footer' ),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'rpt_enqueue_child_style', 25 );

/**
 * Enqueue header interactions.
 */
function rpt_enqueue_header_scripts() {
	wp_enqueue_script(
		'rpt-scroll-lock',
		get_stylesheet_directory_uri() . '/assets/js/scroll-lock.js',
		array(),
		rpt_get_asset_version( 'assets/js/scroll-lock.js' ),
		rpt_get_theme_script_args()
	);

	wp_enqueue_script(
		'rpt-header',
		get_stylesheet_directory_uri() . '/assets/js/header.js',
		array( 'rpt-scroll-lock' ),
		rpt_get_asset_version( 'assets/js/header.js' ),
		rpt_get_theme_script_args()
	);
}
add_action( 'wp_enqueue_scripts', 'rpt_enqueue_header_scripts', 30 );
