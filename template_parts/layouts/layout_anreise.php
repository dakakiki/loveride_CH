<?php
/**
 * Layout: Anreise & Parken (layout_anreise) – dark section from Geteilte Inhalte → Anreise & Parken: head with Anfahrtsplan button, swisstopo map (simple or with layer panel), Wege boxes (links from the shared content or all to one page); Programm & Event, Anreise.
 *
 * @package loveride
 */

$lr_data = (array) lr_get_option( 'geteilt_anreise', array() );
$lr_text = trim( (string) get_sub_field( 'text' ) );

$lr_head = array(
	'tagline' => (string) ( $lr_data['tagline'] ?? '' ),
	'titel'   => (string) ( $lr_data['titel'] ?? '' ),
	'text'    => $lr_text !== '' ? wpautop( esc_html( $lr_text ) ) : (string) ( $lr_data['text'] ?? '' ),
);

$lr_pdf       = (array) ( ( $lr_data['anfahrtsplan'] ?? null ) ?: array() );
$lr_wege_link = (array) ( get_sub_field( 'wege_link' ) ?: array() );
$lr_ebenen_ui = get_sub_field( 'karte' ) === 'ebenen';
$lr_karte     = lr_anreise_karte( (array) ( $lr_data['karte'] ?? array() ) );

$lr_wege = array_values( array_filter(
	(array) ( ( $lr_data['wege'] ?? null ) ?: array() ),
	fn( $weg ) => trim( (string) ( $weg['titel'] ?? '' ) ) !== ''
) );

$lr_map = array(
	'ziel'   => array(
		'titel' => $lr_karte['ziel'],
		'pos'   => $lr_karte['pos'],
		'icon'  => lr_icon( 'location_on' ),
	),
	'zoom'   => $lr_karte['zoom'],
	'ebenen' => array_map( fn( $ebene ) => array(
		'icon'    => lr_icon( $ebene['icon'] ),
		'aktiv'   => $lr_ebenen_ui ? $ebene['aktiv'] : true,
		'marker'  => $ebene['marker'],
		'geojson' => $ebene['geojson'],
	), $lr_karte['ebenen'] ),
);

if ( ! array_filter( $lr_head ) && ! $lr_wege ) {
	return;
}
?>
<section <?= lr_section_attrs( array( 'scheme-dunkel' ), array( 'id' => 'anreise', 'data-anreise' => true ) ); ?>>
	<div class="container">

		<div class="anreise__head">
			<?php get_template_part( 'template_parts/components/section_head', null, $lr_head + array( 'animate' => true ) ); ?>

			<?php if ( ! empty( $lr_pdf['url'] ) ) : ?>
				<a class="btn--secondary anreise__pdf fade-up" href="<?= esc_url( $lr_pdf['url'] ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Anfahrtsplan (PDF)', 'loveride' ); ?><span class="screen-reader-text"> <?php esc_html_e( '(öffnet in neuem Tab)', 'loveride' ); ?></span></a>
			<?php endif; ?>
		</div>

		<div class="anreise-karte<?= $lr_ebenen_ui && $lr_karte['ebenen'] ? ' anreise-karte--ebenen' : ''; ?> fade-up">
			<?php if ( $lr_ebenen_ui && $lr_karte['ebenen'] ) : ?>
				<fieldset class="anreise-karte__ebenen">
					<legend class="anreise-karte__legend tagline"><?php esc_html_e( 'Ebenen ein- und ausblenden', 'loveride' ); ?></legend>
					<ul class="anreise-karte__liste">
						<?php foreach ( $lr_karte['ebenen'] as $lr_i => $lr_ebene ) : ?>
							<li>
								<label class="anreise-ebene">
									<input class="anreise-ebene__input" type="checkbox" data-ebene="<?= (int) $lr_i; ?>"<?php checked( $lr_ebene['aktiv'] ); ?>>
									<span class="anreise-ebene__box" aria-hidden="true"></span>
									<?= lr_icon( $lr_ebene['icon'], 'anreise-ebene__icon' ); ?>
									<span class="anreise-ebene__text">
										<span class="anreise-ebene__name"><?= esc_html( $lr_ebene['name'] ); ?></span>
										<?php if ( $lr_ebene['hinweis'] !== '' ) : ?>
											<span class="anreise-ebene__hinweis"><?= esc_html( $lr_ebene['hinweis'] ); ?></span>
										<?php endif; ?>
									</span>
								</label>
							</li>
						<?php endforeach; ?>
					</ul>
				</fieldset>
			<?php endif; ?>

			<div class="anreise-karte__map" data-map="<?= esc_attr( wp_json_encode( $lr_map ) ); ?>" role="region" aria-label="<?= esc_attr__( 'Karte Anreise & Parken', 'loveride' ); ?>"></div>
		</div>

		<?php if ( $lr_wege ) : ?>
			<ul class="anreise-wege" data-gsap-cards>
				<?php foreach ( $lr_wege as $lr_weg ) : ?>
					<?php
					$lr_link = (array) ( $lr_weg['link'] ?: array() );

					if ( ! empty( $lr_wege_link['url'] ) ) {
						$lr_link = array(
							'title'  => $lr_link['title'] ?? '',
							'url'    => $lr_wege_link['url'],
							'target' => $lr_wege_link['target'] ?? '',
						);
					}
					?>
					<li class="anreise-weg<?= ! empty( $lr_link['url'] ) ? ' anreise-weg--link' : ''; ?>">
						<?= lr_icon( $lr_weg['icon'] ?? '', 'anreise-weg__icon' ); ?>

						<div class="anreise-weg__content">
							<h3 class="anreise-weg__title"><?= esc_html( $lr_weg['titel'] ); ?></h3>

							<?php if ( trim( (string) $lr_weg['text'] ) !== '' ) : ?>
								<p class="anreise-weg__text"><?= esc_html( $lr_weg['text'] ); ?></p>
							<?php endif; ?>
						</div>

						<?php if ( ! empty( $lr_link['url'] ) ) : ?>
							<a class="anreise-weg__cta btn--link" <?= lr_link_attrs( $lr_link ); ?>><?= esc_html( $lr_link['title'] ?: __( 'Mehr erfahren', 'loveride' ) ); ?><?= lr_ui_icon( 'chevron_right' ); ?></a>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

	</div>
</section>
