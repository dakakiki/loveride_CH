<?php
/**
 * Topbar: Save-the-Date text (Theme-Einstellungen → Topbar, dates from Event-Daten) and quick links (menu-topbar), top of the site header.
 *
 * @package loveride
 */

$lr_topbar_texts = array(
	'full'   => lr_topbar_text( 'full' ),
	'medium' => lr_topbar_text( 'medium' ),
	'short'  => lr_topbar_text( 'short' ),
);
$lr_topbar_menu  = has_nav_menu( 'menu-topbar' );

if ( ! $lr_topbar_texts['full'] && ! $lr_topbar_menu ) {
	return;
}
?>

<div class="topbar">
	<div class="topbar__inner">

		<?php if ( $lr_topbar_texts['full'] ) : ?>
			<p class="topbar__text">
				<?php foreach ( $lr_topbar_texts as $lr_variant => $lr_text ) : ?>
					<span class="topbar__text-<?= esc_attr( $lr_variant ); ?>"><?= esc_html( $lr_text ); ?></span>
				<?php endforeach; ?>
			</p>
		<?php endif; ?>

		<?php if ( $lr_topbar_menu ) : ?>
			<nav class="topbar__nav" aria-label="<?php esc_attr_e( 'Schnelllinks', 'loveride' ); ?>">
				<?php
				wp_nav_menu( array(
					'theme_location' => 'menu-topbar',
					'container'      => false,
					'menu_class'     => 'topbar__menu',
					'depth'          => 1,
					'fallback_cb'    => false,
				) );
				?>
			</nav>
		<?php endif; ?>

	</div>
</div>
