<?php
/**
 * Site header — Industrial Energy Framework.
 *
 * Menu + dropdown: RPT Primary Navigation (WordPress admin).
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$menu_location = rpt_get_primary_menu_location();
$menu_args     = array(
	'theme_location' => $menu_location,
	'container'      => false,
	'depth'          => 2,
	'fallback_cb'    => 'rpt_primary_menu_fallback',
	'rpt_nav_context' => 'desktop',
);
?>
<header class="rpt-site-header site-header" id="masthead" role="banner">
	<div class="rpt-container rpt-site-header__inner">
		<div class="rpt-site-header__brand">
			<?php if ( has_custom_logo() ) : ?>
				<div class="rpt-site-header__logo site-logo">
					<?php the_custom_logo(); ?>
				</div>
			<?php else : ?>
				<a class="rpt-site-header__title main-title" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<?php echo esc_html( rpt_get_site_info( 'company_name' ) ); ?>
				</a>
			<?php endif; ?>
		</div>

		<div class="rpt-site-header__menu-row">
			<div class="rpt-site-header__nav-group">
				<nav class="rpt-site-header__nav rpt-site-header__nav--desktop" aria-label="<?php esc_attr_e( 'Menu chính', 'generatepress_child' ); ?>">
					<?php
					wp_nav_menu(
						array_merge(
							$menu_args,
							array(
								'menu_class' => 'rpt-primary-nav',
							)
						)
					);
					?>
				</nav>

				<?php rpt_render_header_quote_cta( 'desktop' ); ?>
			</div>

			<div class="rpt-site-header__search rpt-header-search rpt-header-search--desktop" data-rpt-desktop-search>
				<?php
				get_template_part(
					'template-parts/header/search',
					'form',
					array(
						'context'   => 'desktop',
						'id_suffix' => 'desktop',
					)
				);
				?>
			</div>
		</div>

		<div class="rpt-site-header__mobile-actions">
			<button
				class="rpt-icon-btn"
				type="button"
				data-rpt-search-open
				aria-label="<?php esc_attr_e( 'Mở tìm kiếm', 'generatepress_child' ); ?>"
				aria-controls="rpt-search-overlay"
				aria-expanded="false"
			>
				<?php echo rpt_get_icon_search_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>

			<button
				class="rpt-icon-btn"
				type="button"
				data-rpt-menu-open
				aria-label="<?php esc_attr_e( 'Mở menu', 'generatepress_child' ); ?>"
				aria-controls="rpt-mobile-menu"
				aria-expanded="false"
			>
				<?php echo rpt_get_icon_menu_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>
		</div>
	</div>
</header>

<div class="rpt-search-overlay" id="rpt-search-overlay" aria-hidden="true" hidden>
	<div class="rpt-search-overlay__backdrop" data-rpt-search-close></div>
	<div class="rpt-search-overlay__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Tìm kiếm', 'generatepress_child' ); ?>">
		<button class="rpt-search-overlay__close rpt-icon-btn" type="button" data-rpt-search-close aria-label="<?php esc_attr_e( 'Đóng tìm kiếm', 'generatepress_child' ); ?>">
			<?php echo rpt_get_icon_close_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</button>

		<div class="rpt-header-search rpt-header-search--mobile">
			<?php
			get_template_part(
				'template-parts/header/search',
				'form',
				array(
					'context'   => 'mobile',
					'id_suffix' => 'mobile',
				)
			);
			?>
		</div>
	</div>
</div>

<div class="rpt-mobile-menu" id="rpt-mobile-menu" aria-hidden="true" hidden>
	<div class="rpt-mobile-menu__backdrop" data-rpt-menu-close></div>
	<div class="rpt-mobile-menu__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Menu di động', 'generatepress_child' ); ?>">
		<div class="rpt-mobile-menu__top">
			<span class="rpt-mobile-menu__title"><?php esc_html_e( 'Menu', 'generatepress_child' ); ?></span>
			<button class="rpt-mobile-menu__close rpt-icon-btn" type="button" data-rpt-menu-close aria-label="<?php esc_attr_e( 'Đóng menu', 'generatepress_child' ); ?>">
				<?php echo rpt_get_icon_close_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>
		</div>

		<nav class="rpt-mobile-menu__nav" aria-label="<?php esc_attr_e( 'Menu di động', 'generatepress_child' ); ?>">
			<?php
			wp_nav_menu(
				array_merge(
					$menu_args,
					array(
						'menu_class'      => 'rpt-mobile-nav',
						'fallback_cb'     => 'rpt_mobile_menu_fallback',
						'walker'          => new RPT_Mobile_Nav_Walker(),
						'rpt_nav_context' => 'mobile',
					)
				)
			);
			?>
		</nav>

		<div class="rpt-mobile-menu__cta">
			<?php
			rpt_render_quote_cta_button(
				array(
					'label'      => rpt_get_header_quote_cta_label(),
					'class'      => 'rpt-mobile-menu__quote-cta',
					'close_menu' => true,
				)
			);
			?>
		</div>
	</div>
</div>
