<?php
/**
 * Contact representative card.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$name         = rpt_get_contact_rep_name();
$avatar_url   = rpt_get_contact_rep_avatar_url();
$contact_rows = rpt_get_contact_rep_rows();
?>
<section class="rpt-contact-rep">
	<div class="rpt-contact-rep__card">
		<div class="rpt-contact-rep__avatar" aria-hidden="true">
			<?php if ( $avatar_url ) : ?>
				<img
					class="rpt-contact-rep__avatar-img"
					src="<?php echo esc_url( $avatar_url ); ?>"
					alt=""
					width="72"
					height="72"
					loading="lazy"
					decoding="async"
				/>
			<?php else : ?>
				<svg class="rpt-contact-rep__avatar-placeholder" viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
					<circle cx="36" cy="36" r="36" fill="#E8EDF3"/>
					<circle cx="36" cy="28" r="12" fill="#B8C5D6"/>
					<path d="M14 62c4-12 14-18 22-18s18 6 22 18" fill="#B8C5D6"/>
				</svg>
			<?php endif; ?>
		</div>

		<div class="rpt-contact-rep__body">
			<h2 class="rpt-contact-rep__name"><?php echo esc_html( $name ); ?></h2>

			<?php if ( ! empty( $contact_rows ) ) : ?>
				<dl class="rpt-contact-rep__list">
					<?php foreach ( $contact_rows as $row ) : ?>
						<div class="rpt-contact-rep__item">
							<dt class="rpt-contact-rep__label"><?php echo esc_html( $row['label'] ); ?>:</dt>
							<dd class="rpt-contact-rep__value">
								<?php if ( ! empty( $row['url'] ) ) : ?>
									<a href="<?php echo esc_url( $row['url'] ); ?>"><?php echo esc_html( $row['value'] ); ?></a>
								<?php else : ?>
									<?php echo esc_html( $row['value'] ); ?>
								<?php endif; ?>
							</dd>
						</div>
					<?php endforeach; ?>
				</dl>
			<?php endif; ?>
		</div>
	</div>
</section>
