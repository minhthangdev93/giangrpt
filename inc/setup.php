<?php
/**
 * Theme setup — supports, menus, defaults, helpers.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

require get_stylesheet_directory() . '/inc/site-config.php';
require get_stylesheet_directory() . '/inc/footer.php';
require get_stylesheet_directory() . '/inc/header.php';
require get_stylesheet_directory() . '/inc/breadcrumb.php';

/**
 * Register theme supports and menus.
 */
function rpt_theme_setup() {
	load_child_theme_textdomain( 'generatepress_child', get_stylesheet_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 400,
			'single_image_width'    => 600,
			'product_grid'          => array(
				'default_rows'    => 3,
				'min_rows'        => 1,
				'max_rows'        => 10,
				'default_columns' => 4,
				'min_columns'     => 1,
				'max_columns'     => 4,
			),
		)
	);

	register_nav_menus(
		array(
			'rpt-primary'            => __( 'RPT Primary Navigation', 'generatepress_child' ),
			'rpt-footer-quick-links' => __( 'Footer Quick Links', 'generatepress_child' ),
		)
	);
}
add_action( 'after_setup_theme', 'rpt_theme_setup' );

/**
 * Align GeneratePress container width with design system.
 *
 * @param array $defaults GeneratePress defaults.
 * @return array
 */
function rpt_generate_option_defaults( $defaults ) {
	$defaults['container_width'] = 1200;
	return $defaults;
}
add_filter( 'generate_option_defaults', 'rpt_generate_option_defaults' );

/**
 * Global inquiry / quote page URL.
 *
 * @return string
 */
function rpt_get_inquiry_url() {
	/**
	 * Filter the global Request Quote destination URL.
	 *
	 * @param string $url Default inquiry page URL.
	 */
	return apply_filters( 'rpt_inquiry_url', home_url( '/request-quote/' ) );
}

/**
 * Inquiry URL for a specific product.
 *
 * @param WC_Product|int|null $product Product object or ID.
 * @return string
 */
function rpt_get_product_inquiry_url( $product = null ) {
	if ( is_numeric( $product ) && function_exists( 'wc_get_product' ) ) {
		$product = wc_get_product( $product );
	}

	$url = rpt_get_inquiry_url();

	if ( class_exists( 'WC_Product' ) && $product instanceof WC_Product ) {
		$url = add_query_arg(
			array(
				'product'    => $product->get_slug(),
				'product_id' => $product->get_id(),
			),
			$url
		);
	}

	/**
	 * Filter the per-product inquiry URL.
	 *
	 * @param string     $url     Inquiry URL.
	 * @param WC_Product $product Product object.
	 */
	return apply_filters( 'rpt_product_inquiry_url', $url, $product );
}

/**
 * Load inc modules.
 *
 * @return void
 */
function rpt_load_includes() {
	$includes = array(
		'customizer-site-info.php',
		'post-type-video.php',
		'acf-product-specs.php',
		'acf-product-basic-info.php',
		'acf-product-video.php',
		'acf-video.php',
		'video.php',
		'about.php',
		'acf-about.php',
		'contact.php',
		'acf-contact.php',
		'inquiry-form.php',
		'performance.php',
		'quote-request.php',
		'telegram-settings.php',
		'news.php',
		'post-duplicate.php',
		'home.php',
		'acf-home.php',
		'single-post.php',
		'woocommerce-permalinks.php',
		'single-product.php',
		'enqueue.php',
		'hooks.php',
		'woocommerce.php',
		'woocommerce-catalog-mode.php',
	);

	foreach ( $includes as $file ) {
		$path = get_stylesheet_directory() . '/inc/' . $file;
		if ( file_exists( $path ) ) {
			require $path;
		}
	}
}
add_action( 'after_setup_theme', 'rpt_load_includes', 15 );

/**
 * Cache-busting version for theme assets.
 *
 * @param string $relative_path Path relative to child theme root.
 * @return string
 */
function rpt_get_asset_version( $relative_path ) {
	$file_path = get_stylesheet_directory() . '/' . ltrim( $relative_path, '/' );

	if ( file_exists( $file_path ) ) {
		return (string) filemtime( $file_path );
	}

	return wp_get_theme()->get( 'Version' );
}

/**
 * Temporary admin notice to verify cPanel Git deploy on the new host.
 */
function rpt_deploy_test_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	echo '<div class="notice notice-success"><p><strong>RPT deploy OK</strong> — host mới <code>rpt-power.vn</code> đã nhận theme từ GitHub (2026-08-18).</p></div>';
}
add_action( 'admin_notices', 'rpt_deploy_test_admin_notice' );
