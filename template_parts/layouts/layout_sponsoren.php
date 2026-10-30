<?php
/**
 * Layout: Sponsoren (layout_sponsoren) – section head, then the chosen Sponsoren-Stufen (none = all) in their order, each with its label (or intro box) and display: portrait (one wide, several as cards), logo grid (2 / 3 / 4 columns, name tile without logo) or alphabetical name list with „Alle … anzeigen“; Sponsoren & Partner.
 *
 * @package loveride
 */

$lr_head = array(
	'tagline' => (string) get_sub_field( 'tagline' ),
	'titel'   => (string) get_sub_field( 'titel' ),
	'text'    => (string) get_sub_field( 'text' ),
);

$lr_auswahl = array_filter( array_map( 'intval', (array) get_sub_field( 'stufen' ) ) );
$lr_stufen  = get_terms( array(
	'taxonomy'   => 'sponsorenstufe',
	'hide_empty' => true,
	'include'    => $lr_auswahl,
) );

if ( is_wp_error( $lr_stufen ) || ! $lr_stufen ) {
	return;
}

usort( $lr_stufen, fn( $a, $b ) => (int) get_field( 'reihenfolge', $a ) <=> (int) get_field( 'reihenfolge', $b ) );
?>
<section <?= lr_section_attrs( array( 'scheme-weiss' ), array( 'id' => 'sponsoren', 'data-sponsoren' => true ) ); ?>>
	<div class="container">

		<?php get_template_part( 'template_parts/components/section_head', null, $lr_head + array( 'animate' => true ) ); ?>

		<?php foreach ( $lr_stufen as $lr_stufe ) : ?>
			<?php
			$lr_art      = in_array( get_field( 'darstellung', $lr_stufe ), array( 'portraet', 'namen' ), true ) ? get_field( 'darstellung', $lr_stufe ) : 'logos';
			$lr_einl     = (array) ( get_field( 'einleitung', $lr_stufe ) ?: array() );
			$lr_sponsors = get_posts( array(
				'post_type'      => 'sponsor',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => $lr_art === 'namen' ? array( 'title' => 'ASC' ) : array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
				'tax_query'      => array( array( 'taxonomy' => 'sponsorenstufe', 'terms' => $lr_stufe->term_id ) ),
			) );
			?>
			<div class="sponsoren-stufe sponsoren-stufe--<?= esc_attr( $lr_art ); ?> fade-up" id="<?= esc_attr( $lr_stufe->slug ); ?>">

				<?php if ( trim( (string) ( $lr_einl['titel'] ?? '' ) ) !== '' ) : ?>
					<div class="sponsoren-einleitung">
						<?= lr_icon( $lr_einl['icon'] ?? '', 'sponsoren-einleitung__icon' ); ?>
						<div class="sponsoren-einleitung__text">
							<h3 class="sponsoren-einleitung__titel"><?= esc_html( $lr_einl['titel'] ); ?></h3>
							<?php if ( trim( (string) ( $lr_einl['text'] ?? '' ) ) !== '' ) : ?>
								<p class="sponsoren-einleitung__beschreibung"><?= esc_html( $lr_einl['text'] ); ?></p>
							<?php endif; ?>
						</div>
					</div>
				<?php else : ?>
					<h3 class="sponsoren-stufe__titel"><?= esc_html( (string) get_field( 'oeffentlicher_titel', $lr_stufe ) ?: $lr_stufe->name ); ?></h3>
				<?php endif; ?>

				<?php if ( $lr_art === 'portraet' ) : ?>
					<ul class="sponsoren-portraets<?= count( $lr_sponsors ) === 1 ? ' sponsoren-portraets--einzeln' : ''; ?>">
						<?php foreach ( $lr_sponsors as $lr_sponsor ) : ?>
							<?php
							$lr_logo    = get_field( 'logo', $lr_sponsor->ID );
							$lr_website = (string) get_field( 'website', $lr_sponsor->ID );
							$lr_text    = trim( (string) get_field( 'kurzportraet', $lr_sponsor->ID ) );
							?>
							<li class="sponsor-portraet">
								<div class="sponsor-portraet__logo">
									<?= $lr_logo ? lr_acf_image( $lr_logo, 'medium_large', array( 'class' => 'sponsor-portraet__bild', 'loading' => 'lazy' ) ) : '<span class="sponsor-portraet__name">' . esc_html( get_the_title( $lr_sponsor ) ) . '</span>'; ?>
								</div>
								<div class="sponsor-portraet__text">
									<h4 class="sponsor-portraet__titel"><?= esc_html( get_the_title( $lr_sponsor ) ); ?></h4>
									<?php if ( $lr_text !== '' ) : ?>
										<p class="sponsor-portraet__beschreibung"><?= esc_html( $lr_text ); ?></p>
									<?php endif; ?>
									<?php if ( $lr_website !== '' ) : ?>
										<a class="sponsor-portraet__website" href="<?= esc_url( $lr_website ); ?>" target="_blank" rel="noopener"><?= esc_html( lr_url_domain( $lr_website ) ); ?><?= lr_icon( 'open_in_new' ); ?><span class="screen-reader-text"> <?php esc_html_e( '(öffnet in neuem Tab)', 'loveride' ); ?></span></a>
									<?php endif; ?>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php elseif ( $lr_art === 'logos' ) : ?>
					<?php $lr_spalten = in_array( (string) get_field( 'spalten', $lr_stufe ), array( '2', '3', '4' ), true ) ? (string) get_field( 'spalten', $lr_stufe ) : '4'; ?>
					<ul class="sponsoren-logos sponsoren-logos--<?= esc_attr( $lr_spalten ); ?>">
						<?php foreach ( $lr_sponsors as $lr_sponsor ) : ?>
							<?php
							$lr_logo    = get_field( 'logo', $lr_sponsor->ID );
							$lr_website = (string) get_field( 'website', $lr_sponsor->ID );
							$lr_inhalt  = $lr_logo ? lr_acf_image( $lr_logo, 'medium', array( 'class' => 'sponsor-logo__bild', 'loading' => 'lazy', 'alt' => get_the_title( $lr_sponsor ) ) ) : '<span class="sponsor-logo__name">' . esc_html( get_the_title( $lr_sponsor ) ) . '</span>';
							?>
							<li class="sponsor-logo<?= $lr_logo ? '' : ' sponsor-logo--name'; ?>">
								<?php if ( $lr_website !== '' ) : ?>
									<a class="sponsor-logo__link" href="<?= esc_url( $lr_website ); ?>" target="_blank" rel="noopener"><?= $lr_inhalt; ?><span class="screen-reader-text"> <?php esc_html_e( '(öffnet in neuem Tab)', 'loveride' ); ?></span></a>
								<?php else : ?>
									<?= $lr_inhalt; ?>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php else : ?>
					<?php $lr_sichtbar = max( 4, (int) ( get_field( 'anzahl_sichtbar', $lr_stufe ) ?: 20 ) ); ?>
					<ul class="sponsoren-namen">
						<?php foreach ( $lr_sponsors as $lr_i => $lr_sponsor ) : ?>
							<li<?= $lr_i >= $lr_sichtbar ? ' hidden' : ''; ?>><?= esc_html( get_the_title( $lr_sponsor ) ); ?></li>
						<?php endforeach; ?>
					</ul>
					<?php if ( count( $lr_sponsors ) > $lr_sichtbar ) : ?>
						<button type="button" class="btn--secondary sponsoren-namen__alle" data-alle>
							<?= esc_html( sprintf( __( 'Alle %d anzeigen', 'loveride' ), count( $lr_sponsors ) ) ); ?>
						</button>
					<?php endif; ?>
				<?php endif; ?>

			</div>
		<?php endforeach; ?>

	</div>
</section>
