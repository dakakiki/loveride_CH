<?php
/**
 * Layout: Geländeplan (layout_gelaendeplan) – section head, Leaflet map (swisstopo aerial image or the site plan graphic) with numbered markers, numbered legend (click zooms to the place), PDF button, Food & Drinks points with icons; Event #gelaende.
 *
 * @package loveride
 */

$lr_head = array(
	'tagline' => (string) get_sub_field( 'tagline' ),
	'titel'   => (string) get_sub_field( 'titel' ),
	'text'    => (string) get_sub_field( 'text' ),
);

$lr_mode   = get_sub_field( 'hintergrund' ) === 'plan' ? 'plan' : 'luftbild';
$lr_plan   = $lr_mode === 'plan' ? get_sub_field( 'plan' ) : null;
$lr_pdf    = get_sub_field( 'pdf' );
$lr_punkte = array_values( array_filter( (array) ( get_sub_field( 'punkte' ) ?: array() ), fn( $punkt ) => trim( (string) ( $punkt['titel'] ?? '' ) ) !== '' ) );
$lr_orte   = array();

foreach ( (array) ( get_sub_field( 'legende' ) ?: array() ) as $lr_ort ) {
	$lr_titel = trim( (string) ( $lr_ort['titel'] ?? '' ) );

	if ( $lr_titel === '' ) {
		continue;
	}

	$lr_pos = preg_match( '/^\s*(-?\d+(?:[.,]\d+)?)\s*[,;\s]\s*(-?\d+(?:[.,]\d+)?)\s*$/', (string) $lr_ort['position'], $lr_m )
		? array( (float) str_replace( ',', '.', $lr_m[1] ), (float) str_replace( ',', '.', $lr_m[2] ) )
		: null;

	$lr_orte[] = array(
		'titel' => $lr_titel,
		'pos'   => $lr_pos,
		'rot'   => ! empty( $lr_ort['rot'] ),
	);
}

$lr_mitte = preg_match( '/^\s*(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)\s*$/', (string) get_sub_field( 'mitte' ), $lr_c ) ? array( (float) $lr_c[1], (float) $lr_c[2] ) : array( 47.3985, 8.6432 );
$lr_map   = array(
	'mode'   => $lr_mode,
	'center' => $lr_mitte,
	'zoom'   => (int) ( get_sub_field( 'zoom' ) ?: 16 ),
);

if ( ! empty( $lr_plan['url'] ) ) {
	$lr_map['plan'] = array(
		'url' => $lr_plan['url'],
		'w'   => (int) $lr_plan['width'],
		'h'   => (int) $lr_plan['height'],
	);
}

if ( ! $lr_orte && ! $lr_punkte && ! array_filter( $lr_head ) ) {
	return;
}
?>
<section <?= lr_section_attrs( array(), array( 'data-gelaendeplan' => true ) ); ?>>
	<div class="container">

		<?php get_template_part( 'template_parts/components/section_head', null, $lr_head + array( 'animate' => true ) ); ?>

		<div class="gelaende">
			<div class="gelaende__map fade-up" data-map="<?= esc_attr( wp_json_encode( $lr_map ) ); ?>" role="region" aria-label="<?= esc_attr__( 'Geländeplan (Karte)', 'loveride' ); ?>">
				<?php if ( ! empty( $lr_plan['url'] ) ) : ?>
					<noscript><?= lr_acf_image( $lr_plan, 'large', array( 'class' => 'gelaende__fallback' ) ); ?></noscript>
				<?php endif; ?>
			</div>

			<div class="gelaende__aside fade-up">
				<?php if ( $lr_orte ) : ?>
					<ol class="gelaende__legende">
						<?php foreach ( $lr_orte as $lr_i => $lr_ort ) : ?>
							<li>
								<?php if ( $lr_ort['pos'] ) : ?>
									<button type="button" class="gelaende__ort<?= $lr_ort['rot'] ? ' gelaende__ort--rot' : ''; ?>" data-marker="<?= esc_attr( wp_json_encode( $lr_ort['pos'] ) ); ?>" aria-pressed="false">
										<span class="gelaende__nr" aria-hidden="true"><?= (int) $lr_i + 1; ?></span>
										<span class="gelaende__titel"><?= esc_html( $lr_ort['titel'] ); ?></span>
										<span class="screen-reader-text"> – <?php esc_html_e( 'auf der Karte zeigen', 'loveride' ); ?></span>
									</button>
								<?php else : ?>
									<span class="gelaende__ort<?= $lr_ort['rot'] ? ' gelaende__ort--rot' : ''; ?>">
										<span class="gelaende__nr" aria-hidden="true"><?= (int) $lr_i + 1; ?></span>
										<span class="gelaende__titel"><?= esc_html( $lr_ort['titel'] ); ?></span>
									</span>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ol>
				<?php endif; ?>

				<?php if ( ! empty( $lr_pdf['url'] ) ) : ?>
					<a class="btn--secondary gelaende__pdf" href="<?= esc_url( $lr_pdf['url'] ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Plan als PDF', 'loveride' ); ?><span class="screen-reader-text"> <?php esc_html_e( '(öffnet in neuem Tab)', 'loveride' ); ?></span></a>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( $lr_punkte ) : ?>
			<div class="gelaende-punkte">
				<?php if ( trim( (string) get_sub_field( 'punkte_titel' ) ) !== '' ) : ?>
					<h3 class="gelaende-punkte__title fade-up"><?= esc_html( get_sub_field( 'punkte_titel' ) ); ?></h3>
				<?php endif; ?>
				<ul class="gelaende-punkte__list" data-gsap-cards>
					<?php foreach ( $lr_punkte as $lr_punkt ) : ?>
						<li class="gelaende-punkt">
							<?= lr_icon( $lr_punkt['icon'] ?? '', 'gelaende-punkt__icon' ); ?>
							<div class="gelaende-punkt__text">
								<p class="gelaende-punkt__title"><?= esc_html( $lr_punkt['titel'] ); ?></p>
								<?php if ( trim( (string) $lr_punkt['text'] ) !== '' ) : ?>
									<p class="gelaende-punkt__desc"><?= esc_html( $lr_punkt['text'] ); ?></p>
								<?php endif; ?>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

	</div>
</section>
