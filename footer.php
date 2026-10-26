<?php
/**
 * Footer: copyright and legal menu (menu-legal).
 *
 * @package loveride
 */

$lr_copyright = lr_get_option( 'copyright', get_bloginfo( 'name' ) );
?>

<footer class="footer">

	<div class="footer__bottom">

		<p>&copy; <?= esc_html( wp_date( 'Y' ) ); ?> <?= esc_html( $lr_copyright ); ?></p>

		<nav aria-label="<?php esc_attr_e( 'Rechtliches', 'loveride' ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'menu-legal',
				'container'      => false,
				'menu_class'     => 'footer__legal',
				'depth'          => 1,
				'fallback_cb'    => false,
			) );
			?>
		</nav>

	</div>

</footer>

<?php wp_footer(); ?>

</body>
</html>
