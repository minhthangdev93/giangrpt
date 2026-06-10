<?php
/**
 * Mobile navigation walker — WordPress submenu accordion.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Walker for RPT mobile header menu (admin-defined submenus).
 */
class RPT_Mobile_Nav_Walker extends Walker_Nav_Menu {

	/**
	 * Open submenu wrapper.
	 *
	 * @param string   $output Output.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Arguments.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		if ( 0 === $depth ) {
			$output .= "\n{$indent}<div class=\"rpt-mobile-nav__sub\" hidden>\n";
			$output .= "{$indent}\t<ul class=\"rpt-mobile-nav__sub-list\">\n";
			return;
		}

		$output .= "\n{$indent}<ul class=\"sub-menu\">\n";
	}

	/**
	 * Close submenu wrapper.
	 *
	 * @param string   $output Output.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Arguments.
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		if ( 0 === $depth ) {
			$output .= "{$indent}\t</ul>\n";
			$output .= "{$indent}</div>\n";
			return;
		}

		$output .= "{$indent}</ul>\n";
	}

	/**
	 * Render menu item.
	 *
	 * @param string   $output Output.
	 * @param WP_Post  $item   Menu item.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Arguments.
	 * @param int      $id     Item ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		if ( 0 === $depth ) {
			$classes[] = 'rpt-mobile-nav__item';
		}

		$class_names = implode( ' ', array_map( 'sanitize_html_class', array_filter( $classes ) ) );
		$output     .= $indent . '<li class="' . esc_attr( $class_names ) . '">';

		$has_children = in_array( 'menu-item-has-children', $classes, true );

		if ( 0 === $depth && $has_children ) {
			$output .= '<button type="button" class="rpt-mobile-nav__toggle" aria-expanded="false">';
			$output .= esc_html( $item->title );
			$output .= '<span class="rpt-mobile-nav__chevron" aria-hidden="true"></span>';
			$output .= '</button>';
			return;
		}

		$atts           = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
		$atts['href']   = ! empty( $item->url ) ? $item->url : '';
		$atts['class']  = ( 0 === $depth ) ? 'rpt-mobile-nav__link' : 'rpt-mobile-nav__sub-link';

		$attributes = '';

		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$attributes .= ' ' . $attr . '="' . esc_attr( $value ) . '"';
			}
		}

		$output .= '<a' . $attributes . '>';
		$output .= esc_html( $item->title );
		$output .= '</a>';
	}

	/**
	 * Close menu item.
	 *
	 * @param string   $output Output.
	 * @param WP_Post  $item   Menu item.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Arguments.
	 */
	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		$output .= "</li>\n";
	}
}
