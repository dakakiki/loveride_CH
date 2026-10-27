<?php
/**
 * Mega panel: dropdown of one main menu group (intro, links with subtitle, highlighted box), desktop only.
 *
 * @package loveride
 */

$lr_group = $args['group'] ?? array();

if ( empty( $lr_group['children'] ) ) {
	return;
}

$lr_box = $lr_group['box'];
?>

<div class="mega__panel" id="mega-<?= (int) $lr_group['id']; ?>" hidden>
	<div class="mega__inner">

		<div class="mega__intro">
			<a class="mega__title" <?= lr_link_attrs( $lr_group ); ?>><?= esc_html( $lr_group['title'] ); ?></a>
			<?php if ( $lr_group['intro'] ) : ?>
				<p class="mega__text"><?= esc_html( $lr_group['intro'] ); ?></p>
			<?php endif; ?>
		</div>

		<ul class="mega__links">
			<?php foreach ( $lr_group['children'] as $lr_item ) : ?>
				<li>
					<a class="mega__link<?= $lr_item['current'] ? ' is-current' : ''; ?>" <?= lr_link_attrs( $lr_item ); ?>>
						<span class="mega__link-title"><?= esc_html( $lr_item['title'] ); ?></span>
						<?php if ( $lr_item['untertitel'] ) : ?>
							<span class="mega__link-sub"><?= esc_html( $lr_item['untertitel'] ); ?></span>
						<?php endif; ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php if ( ! empty( $lr_box['titel'] ) || ! empty( $lr_box['text'] ) ) : ?>
			<div class="mega__box">
				<?php if ( ! empty( $lr_box['titel'] ) ) : ?>
					<p class="mega__box-title"><?= esc_html( $lr_box['titel'] ); ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $lr_box['text'] ) ) : ?>
					<p class="mega__box-text"><?= esc_html( $lr_box['text'] ); ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $lr_box['link']['url'] ) ) : ?>
					<a class="mega__box-link" <?= lr_link_attrs( $lr_box['link'] ); ?>>
						<?= esc_html( $lr_box['link']['title'] ); ?><?= lr_ui_icon( 'chevron_right' ); ?>
					</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>

	</div>
</div>
