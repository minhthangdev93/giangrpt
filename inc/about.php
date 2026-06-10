<?php
/**
 * About page helpers and layout detection.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

define( 'RPT_ABOUT_PAGE_TEMPLATE', 'page-templates/template-about.php' );

/**
 * Whether current page uses the about template.
 *
 * @return bool
 */
function rpt_is_about_page() {
	return is_page_template( RPT_ABOUT_PAGE_TEMPLATE );
}

/**
 * About page ACF field with optional fallback.
 *
 * @param string $field   Field name.
 * @param mixed  $default Default value.
 * @return mixed
 */
function rpt_get_about_field( $field, $default = '' ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}

	$post_id = get_queried_object_id();
	$value   = get_field( $field, $post_id );

	if ( null === $value || false === $value || '' === $value ) {
		return $default;
	}

	return $value;
}

/**
 * Hero title for about page.
 *
 * @return string
 */
function rpt_get_about_company_title() {
	$title = rpt_get_about_field( 'about_company_title', '' );

	if ( is_string( $title ) && '' !== trim( $title ) ) {
		return trim( $title );
	}

	return get_the_title();
}

/**
 * Hero subtitle for about page.
 *
 * @return string
 */
function rpt_get_about_company_subtitle() {
	$subtitle = rpt_get_about_field( 'about_company_subtitle', '' );

	return is_string( $subtitle ) ? trim( $subtitle ) : '';
}

/**
 * Sanitized about tabs from repeater.
 *
 * @return array<int, array{title: string, content: string}>
 */
function rpt_get_about_tabs() {
	$rows = rpt_get_about_field( 'about_tabs', array() );

	if ( ! is_array( $rows ) || empty( $rows ) ) {
		return array();
	}

	$tabs = array();

	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$title   = isset( $row['tab_title'] ) ? trim( (string) $row['tab_title'] ) : '';
		$content = isset( $row['tab_content'] ) ? (string) $row['tab_content'] : '';

		if ( '' === $title || '' === trim( wp_strip_all_tags( $content ) ) ) {
			continue;
		}

		$tabs[] = array(
			'title'   => $title,
			'content' => $content,
		);
	}

	return $tabs;
}

/**
 * Sanitized company facts from repeater.
 *
 * @return array<int, array{label: string, value: string}>
 */
function rpt_get_about_company_facts() {
	$rows = rpt_get_about_field( 'about_company_facts', array() );

	if ( ! is_array( $rows ) || empty( $rows ) ) {
		return array();
	}

	$facts = array();

	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$label = isset( $row['fact_label'] ) ? trim( (string) $row['fact_label'] ) : '';
		$value = isset( $row['fact_value'] ) ? trim( (string) $row['fact_value'] ) : '';

		if ( '' === $label || '' === $value ) {
			continue;
		}

		$facts[] = array(
			'label' => $label,
			'value' => $value,
		);
	}

	return $facts;
}

/**
 * Render about page breadcrumb.
 */
function rpt_render_about_breadcrumb() {
	rpt_render_breadcrumb(
		array(
			array(
				'label' => __( 'Trang chủ', 'generatepress_child' ),
				'url'   => home_url( '/' ),
			),
			array(
				'label' => rpt_get_about_company_title(),
			),
		)
	);
}

/**
 * Force full-width layout on about page.
 *
 * @param string $layout Sidebar layout slug.
 * @return string
 */
function rpt_about_sidebar_layout( $layout ) {
	if ( rpt_is_about_page() ) {
		return 'no-sidebar';
	}

	return $layout;
}
add_filter( 'generate_sidebar_layout', 'rpt_about_sidebar_layout' );

/**
 * Body classes for about page.
 *
 * @param array $classes Body classes.
 * @return array
 */
function rpt_about_body_class( $classes ) {
	if ( ! rpt_is_about_page() ) {
		return $classes;
	}

	$classes[] = 'rpt-about-page-body';
	$classes[] = 'full-width-content';
	$classes[] = 'no-sidebar';

	$remove = array( 'right-sidebar', 'left-sidebar', 'both-sidebars', 'both-left', 'both-right' );

	return array_values( array_diff( $classes, $remove ) );
}
add_filter( 'body_class', 'rpt_about_body_class', 20 );
