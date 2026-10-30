<?php
/**
 * Component: lightbox <dialog> for photos and the aftermovie (assets/js/modules/lightbox.js) – used by Galerie-Slider and Jahrgänge (Fotogalerie).
 *
 * @package loveride
 */
?>
<dialog class="lightbox" data-lightbox aria-label="<?= esc_attr__( 'Bild- und Videoansicht', 'loveride' ); ?>">
	<button type="button" class="lightbox__button lightbox__close" data-lightbox-close aria-label="<?= esc_attr__( 'Schliessen', 'loveride' ); ?>" autofocus><?= lr_ui_icon( 'close' ); ?></button>
	<div class="lightbox__stage" data-lightbox-stage></div>
	<button type="button" class="lightbox__button lightbox__arrow lightbox__arrow--prev" data-lightbox-prev aria-label="<?= esc_attr__( 'Vorheriges Bild', 'loveride' ); ?>"><?= lr_ui_icon( 'arrow_back' ); ?></button>
	<button type="button" class="lightbox__button lightbox__arrow lightbox__arrow--next" data-lightbox-next aria-label="<?= esc_attr__( 'Nächstes Bild', 'loveride' ); ?>"><?= lr_ui_icon( 'arrow_forward' ); ?></button>
	<p class="lightbox__counter" data-lightbox-counter aria-live="polite"></p>
</dialog>
