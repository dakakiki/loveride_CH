<?php
/**
 * Layout: Event-Teaser (layout_event_teaser) – next Love Ride from Event-Daten: year logo, title, text, countdown to the opening, Save the Date, „Programm folgt“ (until the programme is published), central Tickets button, footnote; Home.
 *
 * @package loveride
 */

$lr_teaser   = (array) ( lr_event( 'teaser', array() ) ?: array() );
$lr_titel    = trim( (string) lr_event( 'titel' ) );
$lr_logo     = lr_event( 'jahreslogo' );
$lr_text     = trim( (string) ( $lr_teaser['text'] ?? '' ) );
$lr_programm = lr_event( 'programm_veroeffentlicht' ) ? '' : trim( (string) ( $lr_teaser['text_programm_folgt'] ?? '' ) );
$lr_fussnote = trim( (string) ( $lr_teaser['fussnote'] ?? '' ) );
$lr_date     = lr_event_date();
$lr_button   = lr_central_button( 'tickets' );
$lr_start    = lr_event_start();
$lr_seconds  = $lr_start ? $lr_start->getTimestamp() - time() : 0;

if ( ! $lr_titel && ! $lr_text && ! $lr_date ) {
	return;
}

$lr_units = array(
	'tage' => array( intdiv( $lr_seconds, DAY_IN_SECONDS ), __( 'Tage', 'loveride' ) ),
	'std'  => array( intdiv( $lr_seconds % DAY_IN_SECONDS, HOUR_IN_SECONDS ), __( 'Std.', 'loveride' ) ),
	'min'  => array( intdiv( $lr_seconds % HOUR_IN_SECONDS, MINUTE_IN_SECONDS ), __( 'Min.', 'loveride' ) ),
	'sek'  => array( $lr_seconds % MINUTE_IN_SECONDS, __( 'Sek.', 'loveride' ) ),
);
?>
<section <?= lr_section_attrs(); ?>>

	<?php if ( ! empty( $lr_teaser['bild'] ) ) : ?>
		<div class="event-teaser__bg" aria-hidden="true">
			<?= lr_acf_image( $lr_teaser['bild'], 'full', array(
				'class'   => 'event-teaser__bg-image',
				'alt'     => '',
				'sizes'   => '100vw',
				'loading' => 'lazy',
			) ); ?>
		</div>
	<?php endif; ?>

	<div class="container">
		<div class="event-teaser__inner group-fade-up">

			<?php if ( $lr_logo ) : ?>
				<div class="event-teaser__logo fade-up">
					<?= lr_acf_image( $lr_logo, 'medium_large', array(
						'class'   => 'event-teaser__logo-image',
						'sizes'   => '226px',
						'loading' => 'lazy',
					) ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $lr_titel ) : ?>
				<h2 class="event-teaser__title fade-up"><?= esc_html( $lr_titel ); ?></h2>
			<?php endif; ?>

			<?php if ( $lr_text ) : ?>
				<p class="event-teaser__text fade-up"><?= nl2br( esc_html( $lr_text ) ); ?></p>
			<?php endif; ?>

			<?php if ( $lr_seconds > 0 ) : ?>
				<div class="event-teaser__countdown fade-up" data-countdown="<?= esc_attr( $lr_start->format( 'c' ) ); ?>">
					<p class="screen-reader-text" data-countdown-text>
						<?php /* translators: %d: days until the Love Ride */ ?>
						<?= esc_html( sprintf( _n( 'Noch %d Tag bis zum Love Ride', 'Noch %d Tage bis zum Love Ride', $lr_units['tage'][0], 'loveride' ), $lr_units['tage'][0] ) ); ?>
					</p>
					<ul class="countdown" aria-hidden="true">
						<?php foreach ( $lr_units as $lr_key => list( $lr_value, $lr_label ) ) : ?>
							<li class="countdown__unit">
								<span class="countdown__value" data-countdown-<?= esc_attr( $lr_key ); ?>><?= esc_html( $lr_key === 'tage' ? (string) $lr_value : sprintf( '%02d', $lr_value ) ); ?></span>
								<span class="countdown__label"><?= esc_html( $lr_label ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php if ( $lr_date ) : ?>
				<p class="event-teaser__date fade-up"><?= esc_html( trim( trim( (string) lr_get_option( 'topbar_praefix', '' ) ) . ' ' . $lr_date ) ); ?></p>
			<?php endif; ?>

			<?php if ( $lr_programm ) : ?>
				<div class="event-teaser__programm fade-up"><?= wp_kses_post( $lr_programm ); ?></div>
			<?php endif; ?>

			<?php if ( $lr_button || $lr_fussnote ) : ?>
				<div class="event-teaser__actions fade-up">
					<?php if ( $lr_button ) : ?>
						<a class="btn--primary" <?= lr_link_attrs( $lr_button ); ?>><?= esc_html( $lr_button['title'] ?: __( 'Tickets kaufen', 'loveride' ) ); ?></a>
					<?php endif; ?>

					<?php if ( $lr_fussnote ) : ?>
						<p class="event-teaser__note"><?= esc_html( $lr_fussnote ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

		</div>
	</div>
</section>
