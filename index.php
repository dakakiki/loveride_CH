<?php
/**
 * Index: fallback list for any query without a more specific template.
 *
 * @package loveride
 */

get_header();

if ( is_home() ) {
	$lr_title = get_option( 'page_for_posts' ) ? get_the_title( get_option( 'page_for_posts' ) ) : get_bloginfo( 'name' );
} elseif ( is_search() ) {
	$lr_title = sprintf( __( 'Suche: %s', 'loveride' ), get_search_query() );
} else {
	$lr_title = get_the_archive_title();
}
?>

<main id="main">

	<header class="page-header">
		<h1><?= esc_html( wp_strip_all_tags( $lr_title ) ); ?></h1>
	</header>

	<div class="page-content">

		<?php if ( have_posts() ) : ?>

			<div class="wysiwyg">
				<ul>
					<?php while ( have_posts() ) : ?>
						<?php the_post(); ?>
						<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
					<?php endwhile; ?>
				</ul>
			</div>

			<?php the_posts_pagination(); ?>

		<?php else : ?>

			<p><?php esc_html_e( 'Keine Einträge gefunden.', 'loveride' ); ?></p>

		<?php endif; ?>

	</div>

</main>

<?php get_footer(); ?>
