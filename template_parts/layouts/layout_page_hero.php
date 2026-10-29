<?php
/**
 * Layout: Page-Hero (layout_page_hero) – page head with image/video slider or without image, tagline, title, text and Eckdaten (Event-Daten or own values).
 *
 * @package loveride
 */

$lr_ohne_bild = get_sub_field( 'variante' ) === 'ohne';
$lr_medien    = $lr_ohne_bild ? array() : array_values( array_filter( (array) ( get_sub_field( 'medien' ) ?: array() ), function ( $item ) {
	return ! empty( $item['bild'] );
} ) );

$lr_tagline = (string) get_sub_field( 'tagline' );
$lr_titel   = (string) get_sub_field( 'titel' );
$lr_text    = (string) get_sub_field( 'text' );

if ( ! $lr_titel && ! $lr_medien ) {
	return;
}

$lr_facts = array();

foreach ( (array) ( get_sub_field( 'eckdaten' ) ?: array() ) as $lr_row ) {
	$lr_type  = $lr_row['typ'] ?? 'eigen';
	$lr_value = $lr_type === 'eigen' ? trim( (string) ( $lr_row['wert'] ?? '' ) ) : lr_event_value( $lr_type );

	if ( $lr_value !== '' && $lr_type !== 'eigen' && ! empty( $lr_row['vor'] ) ) {
		$lr_value = trim( $lr_row['vor'] ) . ' ' . $lr_value;
	}

	if ( $lr_value === '' || empty( $lr_row['label'] ) ) {
		continue;
	}

	$lr_facts[] = array(
		'label' => $lr_row['label'],
		'wert'  => $lr_value,
	);
}

$lr_count    = count( $lr_medien );
$lr_autoplay = $lr_count > 1 && get_sub_field( 'autoplay' );
$lr_interval = max( 3, (int) ( get_sub_field( 'intervall' ) ?: 7 ) );
$lr_classes  = array( 'page-hero--' . ( $lr_ohne_bild ? 'ohne-bild' : sanitize_html_class( (string) ( get_sub_field( 'hoehe' ) ?: 'standard' ) ) ) );

if ( get_sub_field( 'titelgroesse' ) === 'gross' ) {
	$lr_classes[] = 'page-hero--titel-gross';
}

if ( $lr_facts ) {
	$lr_classes[] = 'page-hero--eckdaten';
}
?>
<section <?= lr_section_attrs( $lr_classes, array(
	'data-hero'     => $lr_count > 0,
	'data-autoplay' => $lr_count ? ( $lr_autoplay ? 'true' : 'false' ) : null,
	'data-interval' => $lr_count ? $lr_interval : null,
) ); ?>>

	<?php if ( $lr_count ) : ?>
		<div class="hero-slides"<?= $lr_count > 1 ? ' role="region" aria-roledescription="' . esc_attr__( 'Karussell', 'loveride' ) . '" aria-label="' . esc_attr__( 'Bilder', 'loveride' ) . '"' : ''; ?> aria-live="<?= $lr_autoplay ? 'off' : 'polite'; ?>" data-hero-slides>

			<?php foreach ( $lr_medien as $lr_i => $lr_item ) : ?>
				<div class="hero-slide<?= $lr_i === 0 ? ' is-active' : ''; ?>"<?php if ( $lr_count > 1 ) : ?> role="group" aria-roledescription="<?= esc_attr__( 'Slide', 'loveride' ); ?>" aria-label="<?= esc_attr( sprintf( __( '%1$d von %2$d', 'loveride' ), $lr_i + 1, $lr_count ) ); ?>"<?php endif; ?> data-hero-slide<?= $lr_i === 0 ? '' : ' aria-hidden="true" inert'; ?>>
					<?php
					get_template_part( 'template_parts/components/hero_media', null, array(
						'bild'  => $lr_item['bild'],
						'video' => ( $lr_item['typ'] ?? '' ) === 'video' ? ( $lr_item['video'] ?? null ) : null,
						'eager' => $lr_i === 0,
					) );
					?>
				</div>
			<?php endforeach; ?>

		</div>
	<?php endif; ?>

	<div class="page-hero__inner">

		<div class="page-hero__content">

			<?php if ( $lr_tagline ) : ?>
				<p class="page-hero__tagline tagline"><?= esc_html( $lr_tagline ); ?></p>
			<?php endif; ?>

			<?php if ( $lr_titel ) : ?>
				<h1 class="page-hero__title"><?= wp_kses_post( $lr_titel ); ?></h1>
			<?php endif; ?>

			<?php if ( $lr_text ) : ?>
				<div class="page-hero__text"><?= wp_kses_post( $lr_text ); ?></div>
			<?php endif; ?>

			<?php if ( $lr_facts ) : ?>
				<dl class="page-hero__facts">
					<?php foreach ( $lr_facts as $lr_fact ) : ?>
						<div class="page-hero__fact">
							<dt><?= esc_html( $lr_fact['label'] ); ?></dt>
							<dd><?= esc_html( $lr_fact['wert'] ); ?></dd>
						</div>
					<?php endforeach; ?>
				</dl>
			<?php endif; ?>

		</div>

		<?php get_template_part( 'template_parts/components/hero_controls', null, array( 'count' => $lr_count ) ); ?>

	</div>

</section>
