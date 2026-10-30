<?php
/**
 * Popup (inc/popups.php): <dialog> with red label, title, close button, note and the Contact Form 7 form; Partner „Loverider-Anmeldung“, „Aussteller-Anmeldung“.
 *
 * @package loveride
 */

$lr_id      = (string) ( $args['id'] ?? '' );
$lr_form    = (int) ( $args['formular'] ?? 0 );
$lr_titel   = trim( (string) ( $args['titel'] ?? '' ) );
$lr_hinweis = trim( (string) ( $args['hinweis'] ?? '' ) );

if ( $lr_id === '' || ! $lr_form ) {
	return;
}
?>
<dialog class="popup" id="<?= esc_attr( $lr_id ); ?>" data-popup-dialog aria-labelledby="<?= esc_attr( $lr_id . '-titel' ); ?>">
	<div class="popup__inhalt">

		<div class="popup__kopf">
			<div class="popup__titelblock">
				<?php if ( trim( (string) ( $args['oberzeile'] ?? '' ) ) !== '' ) : ?>
					<p class="popup__oberzeile"><?= esc_html( $args['oberzeile'] ); ?></p>
				<?php endif; ?>
				<h2 class="popup__titel" id="<?= esc_attr( $lr_id . '-titel' ); ?>"><?= esc_html( $lr_titel ?: get_the_title( $lr_form ) ); ?></h2>
			</div>
			<button type="button" class="popup__schliessen" data-popup-close aria-label="<?= esc_attr__( 'Schliessen', 'loveride' ); ?>"><?= lr_ui_icon( 'close' ); ?></button>
		</div>

		<?php if ( $lr_hinweis !== '' ) : ?>
			<p class="popup__hinweis"><?= nl2br( esc_html( $lr_hinweis ) ); ?></p>
		<?php endif; ?>

		<?= do_shortcode( sprintf( '[contact-form-7 id="%d" html_class="lr-form lr-form--popup"]', $lr_form ) ); ?>

	</div>
</dialog>
