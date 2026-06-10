<?php
/**
 * Contact location and map.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$title       = rpt_get_contact_location_title();
$company     = rpt_get_site_info( 'company_name' );
$address     = rpt_get_site_info( 'address' );
$map_embed   = rpt_get_contact_map_embed();
?>
<section class="rpt-contact-location">
	<div class="rpt-contact-location__header">
		<h2 class="rpt-contact-location__title"><?php echo esc_html( $title ); ?></h2>
		<span class="rpt-contact-location__accent" aria-hidden="true"></span>
	</div>

	<div class="rpt-contact-location__card">
		<?php if ( $company ) : ?>
			<h3 class="rpt-contact-location__company"><?php echo esc_html( $company ); ?></h3>
		<?php endif; ?>

		<?php if ( $address ) : ?>
			<p class="rpt-contact-location__address">
				<strong><?php esc_html_e( 'Địa chỉ:', 'generatepress_child' ); ?></strong>
				<?php echo esc_html( $address ); ?>
			</p>
		<?php endif; ?>

		<?php if ( $map_embed ) : ?>
			<div class="rpt-contact-location__map">
				<?php
				echo wp_kses(
					$map_embed,
					array(
						'iframe' => array(
							'src'             => true,
							'width'           => true,
							'height'          => true,
							'style'           => true,
							'allowfullscreen' => true,
							'loading'         => true,
							'referrerpolicy'  => true,
							'title'           => true,
							'frameborder'     => true,
						),
					)
				);
				?>
			</div>
		<?php endif; ?>
	</div>
</section>
