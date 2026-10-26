<?php
/**
 * 404: page not found.
 *
 * @package loveride
 */

get_header();
?>

<main id="main" class="error-404">

	<header class="page-header">
		<p class="tagline"><?php esc_html_e( 'Fehler 404', 'loveride' ); ?></p>
		<h1><?php esc_html_e( 'Seite nicht gefunden', 'loveride' ); ?></h1>
		<p class="error-404__text text-medium"><?php esc_html_e( 'Die gesuchte Seite gibt es nicht oder nicht mehr.', 'loveride' ); ?></p>
		<div class="buttons">
			<a class="btn--primary" href="<?= esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Zur Startseite', 'loveride' ); ?></a>
		</div>
	</header>

</main>

<?php get_footer(); ?>
