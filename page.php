<?php
/**
 * Page: fallback header (without Hero / Page-Hero), editor content, ACF flexible layouts.
 *
 * @package loveride
 */

get_header();
?>

<main id="main">

	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<?php if ( ! lr_page_has_layout( array( 'layout_hero', 'layout_page_hero' ) ) ) : ?>
				<header class="page-header">
					<h1><?php the_title(); ?></h1>
				</header>
			<?php endif; ?>

			<?php if ( get_the_content() ) : ?>
				<div class="page-content">
					<div class="wysiwyg">
						<?php the_content(); ?>
					</div>
				</div>
			<?php endif; ?>

			<?php
			if ( function_exists( 'have_rows' ) && have_rows( 'layouts' ) ) {
				while ( have_rows( 'layouts' ) ) {
					the_row();
					lr_render_layout( get_row_layout() );
				}
			}
			?>

		</article>

	<?php endwhile; ?>

</main>

<?php get_footer(); ?>
