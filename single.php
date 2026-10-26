<?php
/**
 * Single: dispatches to template_parts/single/{post_type}.php, fallback default.php.
 *
 * @package loveride
 */

get_header();
?>

<main id="main">

	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>

		<?php
		$lr_type = sanitize_key( (string) get_post_type() );

		if ( ! $lr_type || ! locate_template( "template_parts/single/{$lr_type}.php" ) ) {
			$lr_type = 'default';
		}

		get_template_part( 'template_parts/single/' . $lr_type );
		?>

	<?php endwhile; ?>

</main>

<?php get_footer(); ?>
