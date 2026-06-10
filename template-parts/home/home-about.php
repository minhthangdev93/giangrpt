<?php
/**
 * Homepage about section.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$title   = rpt_get_home_field( 'home_about_title', __( 'Về chúng tôi', 'generatepress_child' ) );
$image   = rpt_get_home_image_url( rpt_get_home_field( 'home_about_image', null ) );
$tagline = rpt_get_home_field( 'home_about_tagline', '' );

if ( '' === trim( $tagline ) ) {
	$tagline = __( 'Phục vụ bằng cả trái tim, tạo ra giá trị', 'generatepress_child' );
}

$company = rpt_get_site_info( 'company_name' );
$url     = rpt_get_about_page_url();
?>
<section class="rpt-home-section rpt-home-about">
	<div class="rpt-container">
		<header class="rpt-home-section__header">
			<h2 class="rpt-home-section__title"><?php echo esc_html( $title ); ?></h2>
			<span class="rpt-home-section__accent" aria-hidden="true"></span>
		</header>

		<a class="rpt-home-about__card" href="<?php echo esc_url( $url ); ?>">
			<?php if ( $image ) : ?>
				<img class="rpt-home-about__image" src="<?php echo esc_url( $image ); ?>" alt="" loading="lazy" decoding="async" />
			<?php else : ?>
				<div class="rpt-home-about__image rpt-home-about__image--fallback" aria-hidden="true"></div>
			<?php endif; ?>

			<div class="rpt-home-about__overlay">
				<?php if ( $company ) : ?>
					<h3 class="rpt-home-about__company"><?php echo esc_html( $company ); ?></h3>
				<?php endif; ?>

				<?php if ( $tagline ) : ?>
					<p class="rpt-home-about__tagline"><?php echo esc_html( $tagline ); ?></p>
				<?php endif; ?>
			</div>
		</a>
	</div>
</section>
