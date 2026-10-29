<?php
/**
 * Layout: Zahlenband (layout_zahlenband) – numbers with count-up; content from Geteilte Inhalte «Wirkung» (Home, Charity) or own (Partner, Helfen, Rückblick); gross = red gradient with image, kompakt = dark row.
 *
 * @package loveride
 */

$lr_data = get_sub_field( 'quelle' ) === 'eigen'
	? (array) ( get_sub_field( 'inhalt' ) ?: array() )
	: (array) lr_get_option( 'geteilt_zahlenband', array() );

$lr_head = array(
	'tagline' => (string) ( $lr_data['tagline'] ?? '' ),
	'titel'   => (string) ( $lr_data['titel'] ?? '' ),
	'text'    => (string) ( $lr_data['text'] ?? '' ),
	'link'    => $lr_data['link'] ?? null,
);

$lr_zahlen = array_values( array_filter(
	(array) ( ( $lr_data['zahlen'] ?? null ) ?: array() ),
	fn( $zahl ) => trim( (string) ( $zahl['zahl'] ?? '' ) ) !== ''
) );

$lr_fussnote   = trim( (string) ( $lr_data['fussnote'] ?? '' ) );
$lr_darstellung = get_sub_field( 'darstellung' ) === 'kompakt' ? 'kompakt' : 'gross';
$lr_bild       = $lr_darstellung === 'gross' ? get_sub_field( 'bild' ) : null;

if ( ! $lr_zahlen && ! array_filter( $lr_head ) ) {
	return;
}
?>
<section <?= lr_section_attrs( array( 'zahlenband--' . $lr_darstellung ) ); ?>>

	<?php if ( $lr_bild ) : ?>
		<div class="zahlenband__bg" aria-hidden="true">
			<?= lr_acf_image( $lr_bild, 'full', array(
				'class'   => 'zahlenband__bg-image',
				'alt'     => '',
				'sizes'   => '100vw',
				'loading' => 'lazy',
			) ); ?>
		</div>
	<?php endif; ?>

	<div class="container zahlenband__inner">

		<?php get_template_part( 'template_parts/components/section_head', null, $lr_head + array( 'split' => true, 'animate' => true ) ); ?>

		<?php if ( $lr_zahlen ) : ?>
			<ul class="zahlenband__list" data-gsap-cards style="--zahlen: <?= (int) count( $lr_zahlen ); ?>">
				<?php foreach ( $lr_zahlen as $lr_zahl ) : ?>
					<li class="zahl">
						<p class="zahl__value">
							<span aria-hidden="true" data-count><?= esc_html( $lr_zahl['zahl'] ); ?></span>
							<span class="screen-reader-text"><?= esc_html( $lr_zahl['zahl'] ); ?></span>
						</p>
						<?php if ( trim( (string) $lr_zahl['text'] ) !== '' ) : ?>
							<p class="zahl__text"><?= esc_html( $lr_zahl['text'] ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php if ( $lr_fussnote ) : ?>
			<p class="zahlenband__note fade-up"><?= esc_html( $lr_fussnote ); ?></p>
		<?php endif; ?>

	</div>
</section>
