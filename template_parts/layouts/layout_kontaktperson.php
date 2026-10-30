<?php
/**
 * Layout: Kontaktperson (layout_kontaktperson) – one team member (Mitglied) as contact: round photo, label, name, Ressort (or function), phone and e-mail on the right; Medien „Medienkontakt“.
 *
 * @package loveride
 */

$lr_person = get_post( (int) get_sub_field( 'person' ) );

if ( ! $lr_person || $lr_person->post_type !== 'mitglied' || $lr_person->post_status !== 'publish' ) {
	return;
}

$lr_name        = trim( get_field( 'vorname', $lr_person->ID ) . ' ' . get_field( 'nachname', $lr_person->ID ) ) ?: get_the_title( $lr_person );
$lr_rolle       = trim( (string) get_field( 'ressort', $lr_person->ID ) ) ?: trim( (string) get_field( 'funktion', $lr_person->ID ) );
$lr_telefon     = trim( (string) get_field( 'telefon', $lr_person->ID ) );
$lr_email       = trim( (string) get_field( 'email', $lr_person->ID ) );
$lr_oberzeile   = trim( (string) get_sub_field( 'oberzeile' ) );
$lr_hintergrund = get_sub_field( 'hintergrund' ) === 'offwhite' ? 'offwhite' : 'weiss';
?>
<section <?= lr_section_attrs( array( 'scheme-' . $lr_hintergrund ), array( 'id' => 'kontakt' ) ); ?>>
	<div class="container">
		<div class="kontaktperson fade-up">

			<div class="kontaktperson__person">
				<div class="kontaktperson__foto">
					<?php if ( has_post_thumbnail( $lr_person ) ) : ?>
						<?= get_the_post_thumbnail( $lr_person, 'thumbnail', array(
							'class'   => 'kontaktperson__bild',
							'loading' => 'lazy',
							'alt'     => $lr_name,
						) ); ?>
					<?php endif; ?>
				</div>
				<div class="kontaktperson__text">
					<?php if ( $lr_oberzeile !== '' ) : ?>
						<p class="kontaktperson__oberzeile"><?= esc_html( $lr_oberzeile ); ?></p>
					<?php endif; ?>
					<h2 class="kontaktperson__name"><?= esc_html( $lr_name ); ?></h2>
					<?php if ( $lr_rolle !== '' ) : ?>
						<p class="kontaktperson__rolle"><?= esc_html( $lr_rolle ); ?></p>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( $lr_telefon !== '' || is_email( $lr_email ) ) : ?>
				<ul class="kontaktperson__kanaele">
					<?php if ( $lr_telefon !== '' ) : ?>
						<li><a href="<?= esc_attr( lr_tel_href( $lr_telefon ) ); ?>"><?= lr_icon( 'call', 'kontaktperson__icon' ); ?><?= esc_html( $lr_telefon ); ?></a></li>
					<?php endif; ?>
					<?php if ( is_email( $lr_email ) ) : ?>
						<li><a href="mailto:<?= esc_attr( antispambot( $lr_email ) ); ?>"><?= lr_icon( 'mail', 'kontaktperson__icon' ); ?><?= esc_html( antispambot( $lr_email ) ); ?></a></li>
					<?php endif; ?>
				</ul>
			<?php endif; ?>

		</div>
	</div>
</section>
