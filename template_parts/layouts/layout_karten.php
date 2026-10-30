<?php
/**
 * Layout: Karten-Grid (layout_karten) – section head and cards (icon on top or left, optionally numbered, label, optional title, text, footer line with icon, optional link; single cards red) in 2 / 3 / 4 columns on white, off-white or dark, optional red top line, optional dark box (icon, title, text, button) and central Tickets button below; variant Zeitleiste = steps in one row (label as large time / year, red = highlighted step); Anreise „Notfall“, Ride-Out „Ablauf“, „Solo-Biker“, „Regeln“, Passagiere & Charity „Barrierefreiheit“, „Partnerorganisationen“, „Spendenverwendung“.
 *
 * @package loveride
 */

$lr_head = array(
	'tagline' => (string) get_sub_field( 'tagline' ),
	'titel'   => (string) get_sub_field( 'titel' ),
	'text'    => (string) get_sub_field( 'text' ),
);

$lr_karten = array_values( array_filter(
	(array) ( get_sub_field( 'karten' ) ?: array() ),
	fn( $karte ) => trim( (string) ( $karte['titel'] ?? '' ) ) !== '' || trim( (string) ( $karte['text'] ?? '' ) ) !== ''
) );

if ( ! $lr_karten && ! array_filter( $lr_head ) ) {
	return;
}

$lr_zeitleiste  = get_sub_field( 'darstellung' ) === 'zeitleiste';
$lr_tickets     = get_sub_field( 'tickets_button' ) ? lr_central_button( 'tickets' ) : array();
$lr_box         = (array) ( get_sub_field( 'box' ) ?: array() );
$lr_box_link    = (array) ( ( $lr_box['link'] ?? null ) ?: array() );
$lr_hintergrund = in_array( get_sub_field( 'hintergrund' ), array( 'offwhite', 'dunkel' ), true ) ? get_sub_field( 'hintergrund' ) : 'weiss';
$lr_spalten     = in_array( (string) get_sub_field( 'spalten' ), array( '2', '3', '4' ), true ) ? (string) get_sub_field( 'spalten' ) : '3';
$lr_titel_size  = in_array( get_sub_field( 'titelgroesse' ), array( 'klein', 'gross' ), true ) ? get_sub_field( 'titelgroesse' ) : 'standard';

$lr_classes = array(
	'scheme-' . $lr_hintergrund,
	'karten--' . $lr_hintergrund,
	'karten--titel-' . $lr_titel_size,
	$lr_zeitleiste ? 'karten--zeitleiste' : 'karten--spalten-' . $lr_spalten,
	! $lr_zeitleiste && get_sub_field( 'icon_position' ) === 'links' ? 'karten--icon-links' : '',
	! $lr_zeitleiste && get_sub_field( 'linie' ) ? 'karten--linie' : '',
);
$lr_tag    = $lr_zeitleiste ? 'ol' : 'ul';
$lr_nummer = ! $lr_zeitleiste && get_sub_field( 'nummerierung' );
?>
<section <?= lr_section_attrs( array_filter( $lr_classes ) ); ?>>
	<div class="container">

		<?php get_template_part( 'template_parts/components/section_head', null, $lr_head + array( 'animate' => true ) ); ?>

		<?php if ( $lr_karten ) : ?>
			<<?= $lr_tag; ?> class="karten" style="--anzahl: <?= (int) count( $lr_karten ); ?>" data-gsap-cards>
				<?php foreach ( $lr_karten as $lr_i => $lr_karte ) : ?>
					<?php
					$lr_link  = (array) ( $lr_karte['link'] ?: array() );
					$lr_titel = trim( (string) $lr_karte['titel'] );
					?>
					<li class="karte<?= ! empty( $lr_karte['rot'] ) ? ' karte--rot' : ''; ?><?= $lr_titel === '' ? ' karte--ohne-titel' : ''; ?>">
						<?php if ( $lr_nummer ) : ?>
							<div class="karte__kopf"><?= lr_icon( $lr_karte['icon'] ?? '', 'karte__icon' ); ?><span class="karte__nr"><?= esc_html( sprintf( '%02d', $lr_i + 1 ) ); ?></span></div>
						<?php elseif ( ! $lr_zeitleiste ) : ?>
							<?= lr_icon( $lr_karte['icon'] ?? '', 'karte__icon' ); ?>
						<?php endif; ?>

						<div class="karte__body">
							<?php if ( trim( (string) $lr_karte['oberzeile'] ) !== '' ) : ?>
								<p class="karte__oberzeile"><?= esc_html( $lr_karte['oberzeile'] ); ?></p>
							<?php endif; ?>

							<?php if ( $lr_titel !== '' ) : ?>
								<h3 class="karte__titel"><?= esc_html( $lr_titel ); ?></h3>
							<?php endif; ?>

							<?php if ( trim( (string) $lr_karte['text'] ) !== '' ) : ?>
								<p class="karte__text"><?= nl2br( esc_html( $lr_karte['text'] ) ); ?></p>
							<?php endif; ?>

							<?php if ( trim( (string) ( $lr_karte['fusszeile']['text'] ?? '' ) ) !== '' ) : ?>
								<p class="karte__fusszeile"><?= lr_icon( $lr_karte['fusszeile']['icon'] ?? '', 'karte__fusszeile-icon' ); ?><?= esc_html( $lr_karte['fusszeile']['text'] ); ?></p>
							<?php endif; ?>

							<?php if ( ! empty( $lr_link['url'] ) ) : ?>
								<a class="karte__link btn--link" <?= lr_link_attrs( $lr_link ); ?>><?= esc_html( $lr_link['title'] ?: __( 'Mehr erfahren', 'loveride' ) ); ?><?= lr_ui_icon( 'chevron_right' ); ?></a>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</<?= $lr_tag; ?>>
		<?php endif; ?>

		<?php if ( trim( (string) ( $lr_box['titel'] ?? '' ) ) !== '' ) : ?>
			<div class="karten-box fade-up">
				<?= lr_icon( $lr_box['icon'] ?? '', 'karten-box__icon' ); ?>
				<div class="karten-box__text">
					<p class="karten-box__titel"><?= esc_html( $lr_box['titel'] ); ?></p>
					<?php if ( trim( (string) ( $lr_box['text'] ?? '' ) ) !== '' ) : ?>
						<p class="karten-box__beschreibung"><?= esc_html( $lr_box['text'] ); ?></p>
					<?php endif; ?>
				</div>
				<?php if ( ! empty( $lr_box_link['url'] ) ) : ?>
					<a class="btn--primary karten-box__button" <?= lr_link_attrs( $lr_box_link ); ?>><?= esc_html( $lr_box_link['title'] ?: __( 'Mehr erfahren', 'loveride' ) ); ?></a>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $lr_tickets ) : ?>
			<div class="buttons karten__buttons fade-up">
				<a class="btn--primary" <?= lr_link_attrs( $lr_tickets ); ?>><?= esc_html( $lr_tickets['title'] ?: __( 'Tickets kaufen', 'loveride' ) ); ?></a>
			</div>
		<?php endif; ?>

	</div>
</section>
