<?php
/**
 * The template for displaying single posts.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="inside-article">
		<?php get_template_part( 'template-parts/news/single', 'post' ); ?>
	</div>
</article>
