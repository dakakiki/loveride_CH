<?php
/**
 * Layout: Live-Hinweise (layout_live) – box with notice and tiles from Geteilte Inhalte → Live-Hinweise, only while switched on; Home, Anreise.
 *
 * @package loveride
 */

if ( ! lr_live_active() ) {
	return;
}

$lr_live    = lr_live();
$lr_hinweis = array_map( 'trim', array_map( 'strval', (array) ( $lr_live['hinweis'] ?? array() ) ) );
$lr_kacheln = array_filter(
	(array) ( ( $lr_live['kacheln'] ?? null ) ?: array() ),
	fn( $kachel ) => trim( (string) ( $kachel['titel'] ?? '' ) ) !== '' || trim( (string) ( $kachel['text'] ?? '' ) ) !== ''
);

$lr_has_hinweis = ( $lr_hinweis['titel'] ?? '' ) !== '' || ( $lr_hinweis['text'] ?? '' ) !== '';

if ( ! $lr_has_hinweis && ! $lr_kacheln ) {
	return;
}

$lr_titel   = trim( (string) get_sub_field( 'titel' ) ) ?: __( 'Live-Hinweise', 'loveride' );
$lr_link    = get_sub_field( 'link' );
$lr_info    = lr_live_infozeile();
$lr_updated = lr_live_updated_text();
?>
<section <?= lr_section_attrs( array( 'scheme-offwhite' ), array( 'id' => 'live' ) ); ?>>
	<div class="container">
		<div class="live-box group-fade-up">

			<div class="live-box__head fade-up">
				<div class="live-box__intro">
					<h2 class="live-box__title"><?= esc_html( $lr_titel ); ?></h2>
					<?php if ( $lr_info ) : ?>
						<p class="live-box__info"><?= esc_html( $lr_info ); ?></p>
					<?php endif; ?>
				</div>

				<?php if ( $lr_updated || ! empty( $lr_link['url'] ) ) : ?>
					<div class="live-box__meta">
						<?php if ( $lr_updated ) : ?>
							<p class="live-box__updated"><?= esc_html( sprintf( __( 'Zuletzt aktualisiert: %s', 'loveride' ), $lr_updated ) ); ?></p>
						<?php endif; ?>
						<?php if ( ! empty( $lr_link['url'] ) ) : ?>
							<a class="live-box__link" <?= lr_link_attrs( $lr_link ); ?>><?= esc_html( $lr_link['title'] ?: __( 'Alle Live-Hinweise', 'loveride' ) ); ?> <span aria-hidden="true">→</span></a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( $lr_has_hinweis ) : ?>
				<div class="live-box__notice fade-up">
					<?= lr_icon( 'warning', 'live-box__notice-icon' ); ?>
					<div class="live-box__notice-body">
						<?php if ( $lr_hinweis['titel'] ?? '' ) : ?>
							<p class="live-box__notice-title"><?= esc_html( $lr_hinweis['titel'] ); ?></p>
						<?php endif; ?>
						<?php if ( $lr_hinweis['text'] ?? '' ) : ?>
							<p class="live-box__notice-text"><?= esc_html( $lr_hinweis['text'] ); ?></p>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $lr_kacheln ) : ?>
				<ul class="live-box__tiles" data-gsap-cards style="--live-cols: <?= (int) min( 4, count( $lr_kacheln ) ); ?>">
					<?php foreach ( $lr_kacheln as $lr_kachel ) : ?>
						<li class="live-tile">
							<?= lr_icon( $lr_kachel['icon'] ?? '', 'live-tile__icon' ); ?>
							<?php if ( $lr_kachel['titel'] ) : ?>
								<h3 class="live-tile__title"><?= esc_html( $lr_kachel['titel'] ); ?></h3>
							<?php endif; ?>
							<?php if ( $lr_kachel['text'] ) : ?>
								<p class="live-tile__text"><?= esc_html( $lr_kachel['text'] ); ?></p>
							<?php endif; ?>
							<?php if ( ! empty( $lr_kachel['link']['url'] ) ) : ?>
								<a class="live-tile__link" <?= lr_link_attrs( $lr_kachel['link'] ); ?>><?= esc_html( $lr_kachel['link']['title'] ?: __( 'Mehr erfahren', 'loveride' ) ); ?> <span aria-hidden="true">→</span></a>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

		</div>
	</div>
</section>
