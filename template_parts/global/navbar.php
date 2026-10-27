<?php
/**
 * Navbar: logo, main navigation (mega menu toggles), header CTAs and mobile menu toggle.
 *
 * @package loveride
 */

$lr_tree    = $args['tree'] ?? array();
$lr_buttons = lr_header_buttons();
?>

<div class="navbar">
	<div class="navbar__inner">

		<a class="navbar__logo" href="<?= esc_url( home_url( '/' ) ); ?>">
			<?= lr_logo( false, array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); ?>
		</a>

		<?php if ( $lr_tree ) : ?>
			<nav class="nav-main" aria-label="<?php esc_attr_e( 'Hauptnavigation', 'loveride' ); ?>">
				<ul class="nav-main__menu">
					<?php foreach ( $lr_tree as $lr_group ) : ?>
						<li class="nav-main__item<?= $lr_group['active'] ? ' is-active' : ''; ?>">
							<?php if ( $lr_group['children'] ) : ?>
								<a
									class="nav-main__link"
									<?= lr_link_attrs( $lr_group ); ?>
									aria-expanded="false"
									aria-controls="mega-<?= (int) $lr_group['id']; ?>"
									data-mega-toggle
								><?= esc_html( $lr_group['title'] ); ?></a>
								<?php get_template_part( 'template_parts/global/mega_panel', null, array( 'group' => $lr_group ) ); ?>
							<?php else : ?>
								<a class="nav-main__link" <?= lr_link_attrs( $lr_group ); ?>><?= esc_html( $lr_group['title'] ); ?></a>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</nav>
		<?php endif; ?>

		<div class="navbar__actions">

			<?php if ( ! empty( $lr_buttons['tickets'] ) ) : ?>
				<?php $lr_words = explode( ' ', trim( $lr_buttons['tickets']['title'] ), 2 ); ?>
				<a class="btn--primary btn--small navbar__tickets" <?= lr_link_attrs( $lr_buttons['tickets'] ); ?>>
					<span><?= esc_html( $lr_words[0] ); ?><?php if ( ! empty( $lr_words[1] ) ) : ?><span class="navbar__tickets-rest"> <?= esc_html( $lr_words[1] ); ?></span><?php endif; ?></span>
				</a>
			<?php endif; ?>

			<?php if ( ! empty( $lr_buttons['spenden'] ) ) : ?>
				<a class="btn--dark btn--small navbar__spenden" <?= lr_link_attrs( $lr_buttons['spenden'] ); ?>><?= esc_html( $lr_buttons['spenden']['title'] ); ?></a>
			<?php endif; ?>

			<?php if ( $lr_tree ) : ?>
				<button
					type="button"
					class="navbar__burger"
					aria-expanded="false"
					aria-controls="mobile-menu"
					aria-label="<?php esc_attr_e( 'Menü öffnen', 'loveride' ); ?>"
					data-label-open="<?php esc_attr_e( 'Menü öffnen', 'loveride' ); ?>"
					data-label-close="<?php esc_attr_e( 'Menü schliessen', 'loveride' ); ?>"
					data-menu-toggle
				>
					<?= lr_ui_icon( 'menu', 'navbar__burger-open' ); ?>
					<?= lr_ui_icon( 'close', 'navbar__burger-close' ); ?>
				</button>
			<?php endif; ?>

		</div>

	</div>
</div>
