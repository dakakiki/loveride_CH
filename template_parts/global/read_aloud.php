<?php
/**
 * Read aloud: floating "Seite vorlesen" button (Theme-Einstellungen → Vorlesen), all pages; shown by read-aloud.js when a German voice is available.
 *
 * @package loveride
 */

if ( ! lr_read_aloud_enabled() ) {
	return;
}

$lr_label = lr_get_option( 'vorlesen_label', __( 'Seite vorlesen', 'loveride' ) );
?>

<div class="read-aloud" role="group" aria-label="<?php esc_attr_e( 'Vorlesen', 'loveride' ); ?>" data-read-aloud data-state="idle" hidden>

	<button class="read-aloud__stop" type="button" aria-label="<?php esc_attr_e( 'Vorlesen beenden', 'loveride' ); ?>" title="<?php esc_attr_e( 'Vorlesen beenden', 'loveride' ); ?>" data-read-aloud-stop hidden>
		<?= lr_ui_icon( 'stop' ); ?>
	</button>

	<button
		class="read-aloud__toggle"
		type="button"
		data-read-aloud-toggle
		data-label-start="<?= esc_attr( $lr_label ); ?>"
		data-label-pause="<?php esc_attr_e( 'Vorlesen pausieren', 'loveride' ); ?>"
		data-label-resume="<?php esc_attr_e( 'Weiter vorlesen', 'loveride' ); ?>"
	>
		<?= lr_icon( 'volume_up', 'read-aloud__icon read-aloud__icon--start' ); ?>
		<?= lr_ui_icon( 'pause', 'read-aloud__icon read-aloud__icon--pause' ); ?>
		<?= lr_ui_icon( 'play_arrow', 'read-aloud__icon read-aloud__icon--resume' ); ?>
		<span class="read-aloud__label" data-read-aloud-label><?= esc_html( $lr_label ); ?></span>
	</button>

</div>
