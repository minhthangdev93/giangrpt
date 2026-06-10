<?php
/**
 * Homepage product categories row.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$title = rpt_get_home_field(
	'home_categories_title',
	__( 'Lưu trữ năng lượng dân cư & C&I Energy Storage nhà sản xuất', 'generatepress_child' )
);
$items = rpt_get_home_category_items();

if ( empty( $items ) ) {
	return;
}
?>
<section class="rpt-home-section rpt-home-categories">
	<div class="rpt-container">
		<header class="rpt-home-section__header">
			<h2 class="rpt-home-section__title"><?php echo esc_html( $title ); ?></h2>
			<span class="rpt-home-section__accent" aria-hidden="true"></span>
		</header>

		<div class="rpt-home-categories__grid">
			<?php foreach ( $items as $item ) : ?>
				<a class="rpt-home-category" href="<?php echo esc_url( $item['url'] ); ?>">
					<span class="rpt-home-category__icon">
						<img src="<?php echo esc_url( $item['image'] ); ?>" alt="" loading="lazy" decoding="async" />
					</span>
					<span class="rpt-home-category__label"><?php echo esc_html( $item['label'] ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
