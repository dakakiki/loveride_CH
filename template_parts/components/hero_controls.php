<?php
/**
 * Component: hero slider controls (dots, counter, arrows) – used by Hero-Slider and Page-Hero.
 *
 * @package loveride
 */

$lr_count = (int) ( $args['count'] ?? 0 );

if ( $lr_count < 2 ) {
	return;
}
?>
<div class="hero-controls">

	<div class="hero-controls__dots">
		<?php for ( $lr_i = 0; $lr_i < $lr_count; $lr_i++ ) : ?>
			<button type="button" class="hero-controls__dot<?= $lr_i === 0 ? ' is-active' : ''; ?>" aria-label="<?= esc_attr( sprintf( __( 'Slide %d anzeigen', 'loveride' ), $lr_i + 1 ) ); ?>"<?= $lr_i === 0 ? ' aria-current="true"' : ''; ?> data-hero-dot="<?= $lr_i; ?>"></button>
		<?php endfor; ?>
	</div>

	<p class="hero-controls__counter" aria-hidden="true">
		<span class="hero-controls__counter-current" data-hero-current><span><?= esc_html( sprintf( '%02d', 1 ) ); ?></span></span>
		<span>/</span>
		<span><?= esc_html( sprintf( '%02d', $lr_count ) ); ?></span>
	</p>

	<button type="button" class="hero-controls__arrow hero-controls__arrow--prev" aria-label="<?= esc_attr__( 'Vorheriger Slide', 'loveride' ); ?>" data-hero-prev>
		<?= lr_ui_icon( 'slider_prev' ); ?>
	</button>

	<button type="button" class="hero-controls__arrow hero-controls__arrow--next" aria-label="<?= esc_attr__( 'Nächster Slide', 'loveride' ); ?>" data-hero-next>
		<?= lr_ui_icon( 'slider_next' ); ?>
	</button>

</div>
