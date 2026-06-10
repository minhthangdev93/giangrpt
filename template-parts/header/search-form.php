<?php
/**
 * Header search form partial.
 *
 * @package GeneratePress_Child
 *
 * @var string $context desktop|mobile
 * @var string $id_suffix Unique suffix for input IDs.
 */

defined( 'ABSPATH' ) || exit;

$template_args = isset( $args ) && is_array( $args ) ? $args : array();
$context       = isset( $template_args['context'] ) ? $template_args['context'] : 'desktop';
$id_suffix     = isset( $template_args['id_suffix'] ) ? $template_args['id_suffix'] : 'desktop';
$input_id  = 'rpt-search-input-' . sanitize_html_class( $id_suffix );
$placeholder = ( 'mobile' === $context )
	? __( 'Tìm kiếm', 'generatepress_child' )
	: __( 'Tìm kiếm...', 'generatepress_child' );
?>
<form
	class="rpt-header-search__form"
	role="search"
	method="get"
	action="<?php echo esc_url( home_url( '/' ) ); ?>"
>
	<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>">
		<?php esc_html_e( 'Tìm kiếm', 'generatepress_child' ); ?>
	</label>
	<input
		id="<?php echo esc_attr( $input_id ); ?>"
		class="rpt-header-search__input"
		type="search"
		name="s"
		value="<?php echo esc_attr( get_search_query() ); ?>"
		placeholder="<?php echo esc_attr( $placeholder ); ?>"
		autocomplete="off"
	/>
	<button class="rpt-header-search__submit" type="submit" aria-label="<?php esc_attr_e( 'Tìm kiếm', 'generatepress_child' ); ?>">
		<?php echo rpt_get_icon_search_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</button>
</form>
