<?php
/**
 * Layout: Schritte (layout_schritte) – numbered steps (label, title, text), optional contact person box, contact details from Theme-Einstellungen (address, phone, e-mail, donation account) and note with icon, and an optional side column: a box with icon points and a note, a facts box (label / value), or a Contact Form 7 form in a white box (head then moves into the left column); white / off-white / dark, optionally without top spacing; Passagiere & Charity „So fährst du mit“, „Gesuch stellen“, Sponsoren & Partner „Sponsoring anfragen“, Über uns „Verein“, „Kontakt“, later Helfen „Anmeldung“.
 *
 * @package loveride
 */

$lr_head = array(
	'tagline' => (string) get_sub_field( 'tagline' ),
	'titel'   => (string) get_sub_field( 'titel' ),
	'text'    => (string) get_sub_field( 'text' ),
);

$lr_schritte = array_values( array_filter(
	(array) ( get_sub_field( 'schritte' ) ?: array() ),
	fn( $schritt ) => trim( (string) ( $schritt['titel'] ?? '' ) ) !== ''
) );

$lr_kontakt = (array) ( get_sub_field( 'kontakt' ) ?: array() );
$lr_hinweis = (array) ( get_sub_field( 'hinweis' ) ?: array() );
$lr_k_name  = trim( (string) ( $lr_kontakt['name'] ?? '' ) );
$lr_k_link  = (array) ( ( $lr_kontakt['link'] ?? null ) ?: array() );
$lr_h_text  = trim( (string) ( $lr_hinweis['text'] ?? '' ) );

$lr_daten = array();

if ( get_sub_field( 'kontaktdaten' ) ) {
	$lr_global  = (array) lr_get_option( 'kontakt', array() );
	$lr_konto   = (array) lr_get_option( 'spenden', array() );
	$lr_adresse = trim( implode( "
", array_filter( array( (string) ( $lr_global['organisation'] ?? '' ), wp_strip_all_tags( str_replace( array( '<br>', '<br />' ), "
", (string) ( $lr_global['adresse'] ?? '' ) ) ) ) ) ) );

	$lr_daten = array_filter( array(
		array( 'location_on', __( 'Adresse', 'loveride' ), nl2br( esc_html( $lr_adresse ) ) ),
		array( 'call', __( 'Telefon', 'loveride' ), ! empty( $lr_global['telefon'] ) ? '<a href="' . esc_attr( lr_tel_href( $lr_global['telefon'] ) ) . '">' . esc_html( $lr_global['telefon'] ) . '</a>' : '' ),
		array( 'mail', __( 'E-Mail', 'loveride' ), is_email( (string) ( $lr_global['email'] ?? '' ) ) ? '<a href="mailto:' . esc_attr( antispambot( $lr_global['email'] ) ) . '">' . esc_html( antispambot( $lr_global['email'] ) ) . '</a>' : '' ),
		array( 'account_balance', __( 'Spendenkonto', 'loveride' ), ! empty( $lr_konto['iban'] ) ? nl2br( esc_html( trim( 'IBAN ' . $lr_konto['iban'] . "
" . ( $lr_konto['kontoinhaber'] ?? '' ) ) ) ) : '' ),
	), fn( $eintrag ) => $eintrag[2] !== '' );
}

$lr_seite  = in_array( get_sub_field( 'seitenspalte' ), array( 'box', 'fakten', 'formular' ), true ) ? get_sub_field( 'seitenspalte' ) : 'keine';
$lr_fakten = $lr_seite === 'fakten' ? array_filter( (array) ( get_sub_field( 'fakten' ) ?: array() ), fn( $fakt ) => trim( (string) ( $fakt['wert'] ?? '' ) ) !== '' ) : array();
$lr_links  = in_array( $lr_seite, array( 'fakten', 'formular' ), true );

if ( ! $lr_schritte && ! array_filter( $lr_head ) && $lr_k_name === '' && ! $lr_daten && ! get_sub_field( 'formular' ) ) {
	return;
}
$lr_form   = $lr_seite === 'formular' ? (int) get_sub_field( 'formular' ) : 0;
$lr_punkte = $lr_seite === 'box' ? array_filter( (array) ( get_sub_field( 'box_punkte' ) ?: array() ), fn( $punkt ) => trim( (string) ( $punkt['text'] ?? '' ) ) !== '' ) : array();
$lr_scheme = in_array( get_sub_field( 'hintergrund' ), array( 'offwhite', 'dunkel' ), true ) ? get_sub_field( 'hintergrund' ) : 'weiss';

if ( $lr_seite === 'formular' && ! $lr_form ) {
	$lr_seite = 'keine';
}
?>
<section <?= lr_section_attrs( array_filter( array( 'scheme-' . $lr_scheme, 'schritte--' . $lr_seite, 'schritte--' . $lr_scheme, get_sub_field( 'ohne_abstand_oben' ) ? 'schritte--anschluss' : '' ) ) ); ?>>
	<div class="container">

		<?php if ( ! $lr_links ) : ?>
			<?php get_template_part( 'template_parts/components/section_head', null, $lr_head + array( 'animate' => true ) ); ?>
		<?php endif; ?>

		<div class="schritte">
			<div class="schritte__haupt">
				<?php if ( $lr_links ) : ?>
					<?php get_template_part( 'template_parts/components/section_head', null, $lr_head + array( 'animate' => true ) ); ?>
				<?php endif; ?>

				<?php if ( $lr_schritte ) : ?>
					<ol class="schritte__liste" data-gsap-cards>
						<?php foreach ( $lr_schritte as $lr_i => $lr_schritt ) : ?>
							<li class="schritt">
								<span class="schritt__nr" aria-hidden="true"><?= (int) $lr_i + 1; ?></span>
								<div class="schritt__text">
									<?php if ( trim( (string) $lr_schritt['oberzeile'] ) !== '' ) : ?>
										<p class="schritt__oberzeile"><?= esc_html( $lr_schritt['oberzeile'] ); ?></p>
									<?php endif; ?>
									<h3 class="schritt__titel"><?= esc_html( $lr_schritt['titel'] ); ?></h3>
									<?php if ( trim( (string) $lr_schritt['text'] ) !== '' ) : ?>
										<p class="schritt__beschreibung"><?= esc_html( $lr_schritt['text'] ); ?></p>
									<?php endif; ?>
								</div>
							</li>
						<?php endforeach; ?>
					</ol>
				<?php endif; ?>

				<?php if ( $lr_daten ) : ?>
					<ul class="schritte-daten fade-up">
						<?php foreach ( $lr_daten as list( $lr_icon, $lr_label, $lr_wert ) ) : ?>
							<li class="schritte-daten__eintrag">
								<span class="schritte-daten__icon"><?= lr_icon( $lr_icon ); ?></span>
								<div class="schritte-daten__text">
									<p class="schritte-daten__label"><?= esc_html( $lr_label ); ?></p>
									<p class="schritte-daten__wert"><?= wp_kses( $lr_wert, array( 'a' => array( 'href' => true ), 'br' => array() ) ); ?></p>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if ( $lr_k_name !== '' ) : ?>
					<div class="schritte-kontakt fade-up">
						<?php if ( trim( (string) ( $lr_kontakt['oberzeile'] ?? '' ) ) !== '' ) : ?>
							<p class="schritte-kontakt__oberzeile"><?= esc_html( $lr_kontakt['oberzeile'] ); ?></p>
						<?php endif; ?>
						<p class="schritte-kontakt__name"><?= esc_html( $lr_k_name ); ?></p>
						<p class="schritte-kontakt__kanaele">
							<?php if ( trim( (string) ( $lr_kontakt['telefon'] ?? '' ) ) !== '' ) : ?>
								<a href="<?= esc_attr( lr_tel_href( $lr_kontakt['telefon'] ) ); ?>"><?= lr_icon( 'call' ); ?><?= esc_html( $lr_kontakt['telefon'] ); ?></a>
							<?php endif; ?>
							<?php if ( is_email( (string) ( $lr_kontakt['email'] ?? '' ) ) ) : ?>
								<a href="mailto:<?= esc_attr( antispambot( $lr_kontakt['email'] ) ); ?>"><?= lr_icon( 'mail' ); ?><?= esc_html( antispambot( $lr_kontakt['email'] ) ); ?></a>
							<?php endif; ?>
						</p>
						<?php if ( ! empty( $lr_k_link['url'] ) ) : ?>
							<a class="btn--secondary schritte-kontakt__button" <?= lr_link_attrs( $lr_k_link ); ?>><?= esc_html( $lr_k_link['title'] ?: __( 'Mehr erfahren', 'loveride' ) ); ?></a>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( $lr_h_text !== '' ) : ?>
					<p class="schritte-hinweis fade-up"><?= lr_icon( $lr_hinweis['icon'] ?? '', 'schritte-hinweis__icon' ); ?><span><?= esc_html( $lr_h_text ); ?></span></p>
				<?php endif; ?>
			</div>

			<?php if ( $lr_seite === 'box' ) : ?>
				<aside class="schritte__seite fade-up">
					<div class="schritte-box">
						<?php if ( trim( (string) get_sub_field( 'box_titel' ) ) !== '' ) : ?>
							<p class="schritte-box__titel"><?= esc_html( get_sub_field( 'box_titel' ) ); ?></p>
						<?php endif; ?>
						<?php if ( $lr_punkte ) : ?>
							<ul class="schritte-box__punkte">
								<?php foreach ( $lr_punkte as $lr_punkt ) : ?>
									<li><?= lr_icon( $lr_punkt['icon'] ?? '', 'schritte-box__icon' ); ?><span><?= esc_html( $lr_punkt['text'] ); ?></span></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>
					<?php if ( trim( (string) get_sub_field( 'box_hinweis' ) ) !== '' ) : ?>
						<p class="schritte__hinweis"><?= esc_html( get_sub_field( 'box_hinweis' ) ); ?></p>
					<?php endif; ?>
				</aside>
			<?php elseif ( $lr_seite === 'fakten' && $lr_fakten ) : ?>
				<aside class="schritte__seite fade-up">
					<dl class="schritte-fakten">
						<?php foreach ( $lr_fakten as $lr_fakt ) : ?>
							<div class="schritte-fakten__eintrag">
								<dt><?= esc_html( $lr_fakt['label'] ); ?></dt>
								<dd><?= esc_html( $lr_fakt['wert'] ); ?></dd>
							</div>
						<?php endforeach; ?>
					</dl>
				</aside>
			<?php elseif ( $lr_seite === 'formular' ) : ?>
				<div class="schritte__formular fade-up">
					<?php if ( trim( (string) get_sub_field( 'formular_titel' ) ) !== '' ) : ?>
						<h3 class="schritte__formular-titel"><?= esc_html( get_sub_field( 'formular_titel' ) ); ?></h3>
					<?php endif; ?>
					<?= do_shortcode( sprintf( '[contact-form-7 id="%d" html_class="lr-form"]', $lr_form ) ); ?>
				</div>
			<?php endif; ?>
		</div>

	</div>
</section>
