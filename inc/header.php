<?php
/**
 * Header helpers — navigation, search icons.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

require get_stylesheet_directory() . '/inc/class-rpt-mobile-nav-walker.php';

/**
 * Resolve primary menu location.
 *
 * @return string
 */
function rpt_get_primary_menu_location() {
	if ( has_nav_menu( 'rpt-primary' ) ) {
		return 'rpt-primary';
	}

	if ( has_nav_menu( 'primary' ) ) {
		return 'primary';
	}

	return 'rpt-primary';
}

/**
 * Add RPT classes to primary nav links.
 *
 * @param array    $atts   Link attributes.
 * @param WP_Post  $item   Menu item.
 * @param stdClass $args   Menu arguments.
 * @param int      $depth  Menu depth.
 * @return array
 */
function rpt_nav_menu_link_attributes( $atts, $item, $args, $depth ) {
	if ( empty( $args->rpt_nav_context ) ) {
		return $atts;
	}

	if ( 'mobile' === $args->rpt_nav_context ) {
		$link_class = ( 0 === (int) $depth ) ? 'rpt-mobile-nav__link' : 'rpt-mobile-nav__sub-link';
	} else {
		$link_class = ( 0 === (int) $depth ) ? 'rpt-primary-nav__link' : 'rpt-nav-dropdown__link';
	}

	if ( isset( $atts['class'] ) ) {
		$atts['class'] .= ' ' . $link_class;
	} else {
		$atts['class'] = $link_class;
	}

	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'rpt_nav_menu_link_attributes', 10, 4 );

/**
 * Add dropdown class to submenu lists.
 *
 * @param array    $classes CSS classes.
 * @param stdClass $args    Menu arguments.
 * @param int      $depth   Menu depth.
 * @return array
 */
function rpt_nav_menu_submenu_css_class( $classes, $args, $depth ) {
	if ( ! empty( $args->rpt_nav_context ) && 'desktop' === $args->rpt_nav_context && (int) $depth < 2 ) {
		$classes[] = 'rpt-nav-dropdown__list';
	}

	return $classes;
}
add_filter( 'nav_menu_submenu_css_class', 'rpt_nav_menu_submenu_css_class', 10, 3 );

/**
 * Fallback primary navigation (flat list, no hard-coded dropdown).
 */
function rpt_primary_menu_fallback() {
	$items = array(
		array(
			'label' => __( 'Trang chủ', 'generatepress_child' ),
			'url'   => home_url( '/' ),
		),
		array(
			'label' => __( 'Các sản phẩm', 'generatepress_child' ),
			'url'   => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ),
		),
		array(
			'label' => __( 'Về chúng tôi', 'generatepress_child' ),
			'url'   => rpt_get_page_url_by_slug( 'about-us', home_url( '/about-us/' ) ),
		),
		array(
			'label' => __( 'Liên hệ với chúng tôi', 'generatepress_child' ),
			'url'   => rpt_get_contact_url(),
		),
		array(
			'label' => __( 'Yêu cầu Đặt giá', 'generatepress_child' ),
			'url'   => rpt_get_inquiry_url(),
		),
	);

	/**
	 * Filter fallback primary navigation items.
	 *
	 * @param array<int, array<string, string>> $items Nav items.
	 */
	$items = apply_filters( 'rpt_primary_menu_fallback_items', $items );

	echo '<ul class="rpt-primary-nav">';

	foreach ( $items as $item ) {
		echo '<li class="menu-item">';
		printf(
			'<a class="rpt-primary-nav__link" href="%1$s">%2$s</a>',
			esc_url( $item['url'] ),
			esc_html( $item['label'] )
		);
		echo '</li>';
	}

	echo '</ul>';
}

/**
 * Fallback mobile navigation.
 */
function rpt_mobile_menu_fallback() {
	$items = apply_filters(
		'rpt_primary_menu_fallback_items',
		array(
			array(
				'label' => __( 'Trang chủ', 'generatepress_child' ),
				'url'   => home_url( '/' ),
			),
			array(
				'label' => __( 'Các sản phẩm', 'generatepress_child' ),
				'url'   => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ),
			),
			array(
				'label' => __( 'Về chúng tôi', 'generatepress_child' ),
				'url'   => rpt_get_page_url_by_slug( 'about-us', home_url( '/about-us/' ) ),
			),
			array(
				'label' => __( 'Liên hệ với chúng tôi', 'generatepress_child' ),
				'url'   => rpt_get_contact_url(),
			),
			array(
				'label' => __( 'Yêu cầu Đặt giá', 'generatepress_child' ),
				'url'   => rpt_get_inquiry_url(),
			),
		)
	);

	echo '<ul class="rpt-mobile-nav">';

	foreach ( $items as $item ) {
		echo '<li class="rpt-mobile-nav__item menu-item">';
		printf(
			'<a class="rpt-mobile-nav__link" href="%1$s">%2$s</a>',
			esc_url( $item['url'] ),
			esc_html( $item['label'] )
		);
		echo '</li>';
	}

	echo '</ul>';
}

/**
 * Inline search icon SVG.
 *
 * @return string
 */
function rpt_get_icon_search_svg() {
	return '<svg class="rpt-icon rpt-icon-search" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="M20 20L16.5 16.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
}

/**
 * Inline close icon SVG.
 *
 * @return string
 */
function rpt_get_icon_close_svg() {
	return '<svg class="rpt-icon rpt-icon-close" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 6L18 18M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
}

/**
 * Inline menu icon SVG.
 *
 * @return string
 */
function rpt_get_icon_menu_svg() {
	return '<svg class="rpt-icon rpt-icon-menu" width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7H20M4 12H20M4 17H20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
}

/**
 * Header quote CTA label.
 *
 * @return string
 */
function rpt_get_header_quote_cta_label() {
	/**
	 * Filter header quote CTA button label.
	 *
	 * @param string $label Button label.
	 */
	return apply_filters( 'rpt_header_quote_cta_label', __( 'Nhận báo giá', 'generatepress_child' ) );
}

/**
 * Render header quote CTA button.
 *
 * @param string $context `desktop` or `mobile`.
 */
function rpt_render_header_quote_cta( $context = 'desktop' ) {
	if ( 'mobile' === $context ) {
		rpt_render_quote_cta_button(
			array(
				'label'      => rpt_get_header_quote_cta_label(),
				'class'      => 'rpt-mobile-menu__quote-cta',
				'close_menu' => true,
			)
		);
		return;
	}

	printf(
		'<button type="button" class="rpt-header-quote-cta rpt-quote-cta rpt-btn" data-rpt-quote-open>%1$s</button>',
		esc_html( rpt_get_header_quote_cta_label() )
	);
}
