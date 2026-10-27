<?php
/**
 * Header: document head, skip link and the fixed site header (Topbar, Navbar with mega panels, mobile menu).
 *
 * @package loveride
 */

$lr_code_head = lr_get_option( 'code_head', '' );
$lr_code_body = lr_get_option( 'code_body', '' );
$lr_menu_tree = lr_menu_tree( 'menu-main' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<script>
		(function () {
			var root = document.documentElement;
			root.classList.add('js');
			window.setTimeout(function () {
				if (!window.__animationsReady) {
					root.classList.remove('js');
				}
			}, 3000);
		})();
	</script>

	<?php wp_head(); ?>

	<?= $lr_code_head; ?>
</head>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<?= $lr_code_body; ?>

<a class="skip-link" href="#main"><?php esc_html_e( 'Zum Inhalt springen', 'loveride' ); ?></a>

<header class="header" data-header>

	<?php get_template_part( 'template_parts/global/topbar' ); ?>

	<?php get_template_part( 'template_parts/global/navbar', null, array( 'tree' => $lr_menu_tree ) ); ?>

	<?php get_template_part( 'template_parts/global/mobile_menu', null, array( 'tree' => $lr_menu_tree ) ); ?>

</header>
