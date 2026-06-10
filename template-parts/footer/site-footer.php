<?php
/**
 * Site footer — Industrial Energy Framework.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$product_categories = rpt_get_footer_product_categories();
?>
<footer class="rpt-site-footer site-footer" id="colophon" role="contentinfo">
	<div class="rpt-site-footer__overlay" aria-hidden="true"></div>

	<div class="rpt-container rpt-site-footer__main">
		<div class="rpt-site-footer__grid">
			<div class="rpt-site-footer__col rpt-site-footer__col--brand">
				<div class="rpt-site-footer__logo">
					<?php if ( has_custom_logo() ) : ?>
						<?php the_custom_logo(); ?>
					<?php else : ?>
						<a class="rpt-site-footer__title" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
							<?php echo esc_html( rpt_get_site_info( 'company_name' ) ); ?>
						</a>
					<?php endif; ?>
				</div>

				<?php if ( rpt_get_site_info( 'company_description' ) ) : ?>
					<p class="rpt-site-footer__description">
						<?php echo esc_html( rpt_get_site_info( 'company_description' ) ); ?>
					</p>
				<?php endif; ?>
			</div>

			<div class="rpt-site-footer__col rpt-site-footer__col--links">
				<h2 class="rpt-site-footer__heading"><?php esc_html_e( 'Liên kết nhanh', 'generatepress_child' ); ?></h2>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'rpt-footer-quick-links',
						'container'      => false,
						'menu_class'     => 'rpt-footer-links',
						'depth'          => 1,
						'fallback_cb'    => 'rpt_footer_quick_links_fallback',
					)
				);
				?>
			</div>

			<div class="rpt-site-footer__col rpt-site-footer__col--categories">
				<h2 class="rpt-site-footer__heading"><?php esc_html_e( 'Danh mục sản phẩm', 'generatepress_child' ); ?></h2>
				<?php if ( ! empty( $product_categories ) ) : ?>
					<ul class="rpt-footer-links">
						<?php foreach ( $product_categories as $term ) : ?>
							<li>
								<a href="<?php echo esc_url( get_term_link( $term ) ); ?>">
									<?php echo esc_html( $term->name ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php else : ?>
					<p class="rpt-site-footer__empty"><?php esc_html_e( 'Chưa có danh mục sản phẩm.', 'generatepress_child' ); ?></p>
				<?php endif; ?>
			</div>

			<div class="rpt-site-footer__col rpt-site-footer__col--contact">
				<h2 class="rpt-site-footer__heading"><?php esc_html_e( 'Liên hệ với chúng tôi', 'generatepress_child' ); ?></h2>

				<dl class="rpt-footer-contact">
					<?php foreach ( rpt_get_footer_contact_rows() as $row ) : ?>
						<?php
						$icon = ! empty( $row['key'] ) ? rpt_get_footer_contact_icon( $row['key'] ) : '';
						?>
						<div class="rpt-footer-contact__item">
							<?php if ( $icon ) : ?>
								<span class="rpt-footer-contact__icon">
									<?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG markup from theme helper. ?>
								</span>
							<?php endif; ?>
							<div class="rpt-footer-contact__content">
								<dt class="rpt-footer-contact__label"><?php echo esc_html( $row['label'] ); ?>:</dt>
								<dd class="rpt-footer-contact__value">
									<?php if ( ! empty( $row['url'] ) ) : ?>
										<a href="<?php echo esc_url( $row['url'] ); ?>"><?php echo esc_html( $row['value'] ); ?></a>
									<?php else : ?>
										<?php echo esc_html( $row['value'] ); ?>
									<?php endif; ?>
								</dd>
							</div>
						</div>
					<?php endforeach; ?>
				</dl>

				<?php
				rpt_render_quote_cta_button(
					array(
						'class' => 'rpt-site-footer__cta',
						'label' => __( 'Yêu cầu ngay bây giờ', 'generatepress_child' ),
					)
				);
				?>
			</div>
		</div>
	</div>

	<div class="rpt-site-footer__bottom">
		<div class="rpt-container">
			<p class="rpt-site-footer__copyright"><?php echo esc_html( rpt_get_footer_copyright() ); ?></p>
		</div>
	</div>
</footer>
