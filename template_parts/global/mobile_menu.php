<?php
/**
 * Mobile menu: accordion of the main menu groups, quick links (menu-topbar) and header CTAs, below the nav breakpoint.
 *
 * @package loveride
 */

$lr_tree    = $args['tree'] ?? array();
$lr_quick   = lr_menu_items( 'menu-topbar' );
$lr_buttons = lr_header_buttons();

if ( ! $lr_tree ) {
	return;
}
?>

<div class="mobile-menu" id="mobile-menu" hidden>

	<nav class="mobile-menu__scroll" aria-label="<?php esc_attr_e( 'Hauptnavigation', 'loveride' ); ?>">

		<ul class="mobile-menu__list">
			<?php foreach ( $lr_tree as $lr_group ) : ?>
				<li class="mobile-menu__group<?= $lr_group['active'] ? ' is-active' : ''; ?>">
					<?php if ( $lr_group['children'] ) : ?>
						<div class="mobile-menu__head">
							<a class="mobile-menu__title" <?= lr_link_attrs( $lr_group ); ?>><?= esc_html( $lr_group['title'] ); ?></a>
							<button
								type="button"
								class="mobile-menu__toggle"
								aria-expanded="false"
								aria-controls="mobile-sub-<?= (int) $lr_group['id']; ?>"
								aria-label="<?= esc_attr( sprintf( __( 'Untermenü %s', 'loveride' ), $lr_group['title'] ) ); ?>"
								data-accordion-toggle
							><?= lr_ui_icon( 'keyboard_arrow_down' ); ?></button>
						</div>
						<ul class="mobile-menu__sub" id="mobile-sub-<?= (int) $lr_group['id']; ?>" hidden>
							<?php foreach ( $lr_group['children'] as $lr_item ) : ?>
								<li><a<?= $lr_item['current'] ? ' class="is-current"' : ''; ?> <?= lr_link_attrs( $lr_item ); ?>><?= esc_html( $lr_item['title'] ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					<?php else : ?>
						<div class="mobile-menu__head">
							<a class="mobile-menu__title" <?= lr_link_attrs( $lr_group ); ?>><?= esc_html( $lr_group['title'] ); ?></a>
						</div>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php if ( $lr_quick ) : ?>
			<ul class="mobile-menu__quick">
				<?php foreach ( $lr_quick as $lr_item ) : ?>
					<li><a <?= lr_link_attrs( array( 'url' => $lr_item->url, 'target' => $lr_item->target ) ); ?>><?= esc_html( $lr_item->title ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

	</nav>

	<?php if ( $lr_buttons ) : ?>
		<div class="mobile-menu__buttons">
			<?php if ( ! empty( $lr_buttons['tickets'] ) ) : ?>
				<a class="btn--primary" <?= lr_link_attrs( $lr_buttons['tickets'] ); ?>><?= esc_html( $lr_buttons['tickets']['title'] ); ?></a>
			<?php endif; ?>
			<?php if ( ! empty( $lr_buttons['spenden'] ) ) : ?>
				<a class="btn--dark" <?= lr_link_attrs( $lr_buttons['spenden'] ); ?>><?= esc_html( $lr_buttons['spenden']['title'] ); ?></a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

</div>
