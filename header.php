<?php
/**
 * Header: document head, skip link, site header with logo, main navigation and CTAs.
 *
 * @package loveride
 */

$lr_logo         = lr_get_option( 'logo' );
$lr_btn_tickets  = lr_get_option( 'button_tickets' );
$lr_btn_spenden  = lr_get_option( 'button_spenden' );
$lr_code_head    = lr_get_option( 'code_head', '' );
$lr_code_body    = lr_get_option( 'code_body', '' );
$lr_site_name    = get_bloginfo( 'name' );
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

<header class="header">

	<div class="header__inner">

		<div class="header__logo">
			<a href="<?= esc_url( home_url( '/' ) ); ?>" title="<?= esc_attr( $lr_site_name ); ?>">
				<?php if ( ! empty( $lr_logo ) ) : ?>
					<?= lr_acf_image( $lr_logo, 'medium', array(
						'loading'       => 'eager',
						'fetchpriority' => 'high',
						'alt'           => $lr_site_name,
					) ); ?>
				<?php else : ?>
					<?= esc_html( $lr_site_name ); ?>
				<?php endif; ?>
			</a>
		</div>

		<nav class="nav-main" aria-label="<?php esc_attr_e( 'Hauptnavigation', 'loveride' ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'menu-main',
				'container'      => false,
				'menu_class'     => 'nav-main__menu',
				'depth'          => 2,
				'fallback_cb'    => false,
			) );
			?>
		</nav>

		<?php if ( ! empty( $lr_btn_tickets['url'] ) || ! empty( $lr_btn_spenden['url'] ) ) : ?>
			<div class="header__actions">
				<?php foreach ( array( $lr_btn_tickets, $lr_btn_spenden ) as $lr_button ) : ?>
					<?php if ( ! empty( $lr_button['url'] ) ) : ?>
						<a
							class="btn--primary btn--small"
							href="<?= esc_url( $lr_button['url'] ); ?>"
							target="<?= esc_attr( ( $lr_button['target'] ?? '' ) ?: '_self' ); ?>"
							<?= ( $lr_button['target'] ?? '' ) === '_blank' ? 'rel="noopener"' : ''; ?>
						><?= esc_html( $lr_button['title'] ); ?></a>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

	</div>

</header>
