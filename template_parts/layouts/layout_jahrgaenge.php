<?php
/**
 * Layout: Jahrgänge (layout_jahrgaenge) – Jahrgang posts by year: Jahreskarten (2 columns: year, edition, date, Kennzahlen; paged), Aftermovie (poster, YouTube without cookies loaded on click), Fotogalerie (4 columns, „Mehr Bilder laden“, lightbox, external gallery link) or Helferliste (names by last name, „Alle … anzeigen“, organisations); year buttons (5 newest, older years in a select), „{jahr}“ in the section head shows the chosen year; Rückblick.
 *
 * @package loveride
 */

$lr_darstellung = in_array( get_sub_field( 'darstellung' ), array( 'aftermovie', 'galerie', 'helfer' ), true ) ? get_sub_field( 'darstellung' ) : 'karten';
$lr_hintergrund = get_sub_field( 'hintergrund' ) === 'offwhite' ? 'offwhite' : 'weiss';
$lr_jahrgaenge  = array_values( array_filter( lr_jahrgaenge(), function ( WP_Post $post ) use ( $lr_darstellung ): bool {
	switch ( $lr_darstellung ) {
		case 'aftermovie':
			return lr_youtube_id( (string) get_field( 'aftermovie', $post->ID, false ) ) !== '';
		case 'galerie':
			return (bool) get_field( 'galerie', $post->ID, false ) || ! empty( ( get_field( 'galerie_extern', $post->ID ) ?: array() )['url'] );
		case 'helfer':
			return lr_jahrgang_helfer( $post ) || trim( (string) get_field( 'helfer_organisationen', $post->ID ) ) !== '';
	}

	return true;
} ) );

if ( ! $lr_jahrgaenge ) {
	return;
}

$lr_id    = sanitize_title( (string) get_sub_field( 'anker' ) ) ?: 'jahrgaenge-' . get_row_index();
$lr_jahr  = lr_jahrgang_jahr( $lr_jahrgaenge[0] );
$lr_marke = '<span data-jahr-text>' . $lr_jahr . '</span>';
$lr_head  = array(
	'tagline' => str_replace( '{jahr}', (string) $lr_jahr, (string) get_sub_field( 'tagline' ) ),
	'titel'   => str_replace( '{jahr}', $lr_marke, (string) get_sub_field( 'titel' ) ),
	'text'    => str_replace( '{jahr}', $lr_marke, (string) get_sub_field( 'text' ) ),
);
$lr_filter = $lr_darstellung !== 'karten' && count( $lr_jahrgaenge ) > 1;
?>
<section <?= lr_section_attrs( array( 'scheme-' . $lr_hintergrund, 'jahrgaenge--' . $lr_darstellung ), array( 'id' => $lr_id, 'data-jahrgaenge' => $lr_darstellung !== 'karten' ? true : null ) ); ?>>
	<div class="container">

		<div class="jahrgaenge__kopf">
			<?php get_template_part( 'template_parts/components/section_head', null, $lr_head + array( 'animate' => true ) ); ?>

			<?php if ( $lr_filter ) : ?>
				<div class="jahr-filter fade-up">
					<div class="jahr-filter__buttons" role="tablist" aria-label="<?= esc_attr__( 'Jahr wählen', 'loveride' ); ?>">
						<?php foreach ( array_slice( $lr_jahrgaenge, 0, 5 ) as $lr_i => $lr_post ) : ?>
							<?php $lr_j = lr_jahrgang_jahr( $lr_post ); ?>
							<button type="button" class="jahr-filter__button" role="tab" id="<?= esc_attr( $lr_id . '-tab-' . $lr_j ); ?>" aria-controls="<?= esc_attr( $lr_id . '-' . $lr_j ); ?>" aria-selected="<?= $lr_i === 0 ? 'true' : 'false'; ?>" tabindex="<?= $lr_i === 0 ? '0' : '-1'; ?>" data-jahr="<?= esc_attr( (string) $lr_j ); ?>"><?= esc_html( (string) $lr_j ); ?></button>
						<?php endforeach; ?>
					</div>

					<?php if ( count( $lr_jahrgaenge ) > 5 ) : ?>
						<select class="jahr-filter__aelter" data-jahr-select aria-label="<?= esc_attr__( 'Ältere Jahre', 'loveride' ); ?>">
							<option value=""><?php esc_html_e( 'Ältere Jahre', 'loveride' ); ?></option>
							<?php foreach ( array_slice( $lr_jahrgaenge, 5 ) as $lr_post ) : ?>
								<option value="<?= esc_attr( (string) lr_jahrgang_jahr( $lr_post ) ); ?>"><?= esc_html( (string) lr_jahrgang_jahr( $lr_post ) ); ?></option>
							<?php endforeach; ?>
						</select>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $lr_darstellung === 'karten' ) : ?>
			<?php
			$lr_pro_seite = max( 2, (int) ( get_sub_field( 'pro_seite' ) ?: 6 ) );
			$lr_seiten    = (int) ceil( count( $lr_jahrgaenge ) / $lr_pro_seite );
			$lr_seite     = min( max( 1, absint( wp_unslash( $_GET['jahre'] ?? 1 ) ) ), $lr_seiten ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$lr_seite_url = fn( int $seite ) => ( $seite > 1 ? add_query_arg( 'jahre', $seite ) : remove_query_arg( 'jahre' ) ) . '#' . $lr_id;
			?>
			<ul class="jahreskarten" data-gsap-cards>
				<?php foreach ( array_slice( $lr_jahrgaenge, ( $lr_seite - 1 ) * $lr_pro_seite, $lr_pro_seite ) as $lr_post ) : ?>
					<?php
					$lr_ausgabe = (int) get_field( 'ausgabe', $lr_post->ID );
					$lr_datum   = lr_format_date( (string) get_field( 'datum', $lr_post->ID ) );
					?>
					<li class="jahreskarte">
						<div class="jahreskarte__kopf">
							<h3 class="jahreskarte__jahr"><?= esc_html( (string) lr_jahrgang_jahr( $lr_post ) ); ?></h3>
							<p class="jahreskarte__meta">
								<?php if ( $lr_ausgabe ) : ?>
									<span class="jahreskarte__ausgabe"><?= esc_html( sprintf( __( 'Love Ride %d', 'loveride' ), $lr_ausgabe ) ); ?></span>
								<?php endif; ?>
								<span class="jahreskarte__datum"><?= esc_html( $lr_datum ?: __( 'Datum folgt', 'loveride' ) ); ?></span>
							</p>
						</div>
						<dl class="jahreskarte__zahlen">
							<?php foreach ( lr_jahrgang_kennzahlen( $lr_post ) as $lr_zahl ) : ?>
								<div class="jahreskarte__zahl<?= $lr_zahl['wert'] === '' ? ' jahreskarte__zahl--leer' : ''; ?>">
									<dt><?= esc_html( $lr_zahl['label'] ); ?></dt>
									<dd><?= esc_html( $lr_zahl['wert'] ?: '–' ); ?></dd>
								</div>
							<?php endforeach; ?>
						</dl>
					</li>
				<?php endforeach; ?>
			</ul>

			<?php if ( $lr_seiten > 1 ) : ?>
				<nav class="pagination fade-up" aria-label="<?= esc_attr__( 'Seiten', 'loveride' ); ?>">
					<?php if ( $lr_seite > 1 ) : ?>
						<a class="pagination__item pagination__pfeil" href="<?= esc_url( $lr_seite_url( $lr_seite - 1 ) ); ?>" aria-label="<?= esc_attr__( 'Vorherige Seite', 'loveride' ); ?>"><?= lr_ui_icon( 'arrow_back' ); ?></a>
					<?php else : ?>
						<span class="pagination__item pagination__pfeil is-disabled" aria-hidden="true"><?= lr_ui_icon( 'arrow_back' ); ?></span>
					<?php endif; ?>
					<?php for ( $lr_n = 1; $lr_n <= $lr_seiten; $lr_n++ ) : ?>
						<a class="pagination__item" href="<?= esc_url( $lr_seite_url( $lr_n ) ); ?>"<?= $lr_n === $lr_seite ? ' aria-current="page"' : ''; ?>><?= (int) $lr_n; ?></a>
					<?php endfor; ?>
					<?php if ( $lr_seite < $lr_seiten ) : ?>
						<a class="pagination__item pagination__pfeil" href="<?= esc_url( $lr_seite_url( $lr_seite + 1 ) ); ?>" aria-label="<?= esc_attr__( 'Nächste Seite', 'loveride' ); ?>"><?= lr_ui_icon( 'arrow_forward' ); ?></a>
					<?php else : ?>
						<span class="pagination__item pagination__pfeil is-disabled" aria-hidden="true"><?= lr_ui_icon( 'arrow_forward' ); ?></span>
					<?php endif; ?>
				</nav>
			<?php endif; ?>

		<?php else : ?>
			<?php foreach ( $lr_jahrgaenge as $lr_i => $lr_post ) : ?>
				<?php $lr_j = lr_jahrgang_jahr( $lr_post ); ?>
				<div class="jahrgaenge__panel" id="<?= esc_attr( $lr_id . '-' . $lr_j ); ?>" data-jahr-panel="<?= esc_attr( (string) $lr_j ); ?>"<?= $lr_filter && $lr_i < 5 ? ' role="tabpanel" aria-labelledby="' . esc_attr( $lr_id . '-tab-' . $lr_j ) . '"' : ''; ?><?= $lr_i > 0 ? ' hidden' : ''; ?>>

					<?php if ( $lr_darstellung === 'aftermovie' ) : ?>
						<?php
						$lr_youtube = lr_youtube_id( (string) get_field( 'aftermovie', $lr_post->ID, false ) );
						$lr_poster  = get_field( 'aftermovie_bild', $lr_post->ID );
						?>
						<div class="jahrgang-video fade-up">
							<?= $lr_poster
								? lr_acf_image( $lr_poster, 'full', array(
									'class'   => 'jahrgang-video__poster',
									'alt'     => '',
									'sizes'   => '(min-width: 1440px) 1280px, 100vw',
									'loading' => 'lazy',
								) )
								: '<img class="jahrgang-video__poster" src="' . esc_url( 'https://i.ytimg.com/vi/' . $lr_youtube . '/hqdefault.jpg' ) . '" alt="" loading="lazy" referrerpolicy="no-referrer">'; ?>
							<button type="button" class="jahrgang-video__play" data-youtube="<?= esc_attr( $lr_youtube ); ?>" data-title="<?= esc_attr( sprintf( __( 'Aftermovie Love Ride %d', 'loveride' ), $lr_j ) ); ?>" aria-label="<?= esc_attr( sprintf( __( 'Aftermovie %d abspielen (YouTube)', 'loveride' ), $lr_j ) ); ?>">
								<span class="jahrgang-video__icon"><?= lr_icon( 'play_circle' ); ?></span>
							</button>
						</div>

					<?php elseif ( $lr_darstellung === 'galerie' ) : ?>
						<?php
						$lr_bilder  = array_values( array_filter( (array) ( get_field( 'galerie', $lr_post->ID ) ?: array() ), fn( $bild ) => ! empty( $bild['ID'] ) ) );
						$lr_extern  = (array) ( get_field( 'galerie_extern', $lr_post->ID ) ?: array() );
						$lr_schritt = max( 4, (int) ( get_sub_field( 'bilder_anzahl' ) ?: 8 ) );
						?>
						<?php if ( $lr_bilder ) : ?>
							<ul class="jahrgang-galerie" data-schritt="<?= (int) $lr_schritt; ?>">
								<?php foreach ( $lr_bilder as $lr_b => $lr_bild ) : ?>
									<li<?= $lr_b >= $lr_schritt ? ' hidden' : ''; ?>>
										<a class="jahrgang-galerie__link" href="<?= esc_url( wp_get_attachment_image_url( $lr_bild['ID'], 'full' ) ); ?>" data-lightbox-index="<?= (int) $lr_b; ?>" aria-label="<?= esc_attr( sprintf( __( 'Bild %1$d von %2$d vergrössern', 'loveride' ), $lr_b + 1, count( $lr_bilder ) ) ); ?>">
											<?= lr_acf_image( $lr_bild, 'medium_large', array(
												'class'   => 'jahrgang-galerie__bild',
												'sizes'   => '(min-width: 991px) 25vw, 50vw',
												'loading' => 'lazy',
											) ); ?>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>

						<?php if ( count( $lr_bilder ) > $lr_schritt || ! empty( $lr_extern['url'] ) ) : ?>
							<div class="buttons jahrgang-galerie__buttons">
								<?php if ( count( $lr_bilder ) > $lr_schritt ) : ?>
									<button type="button" class="btn--secondary" data-mehr><?php esc_html_e( 'Mehr Bilder laden', 'loveride' ); ?></button>
								<?php endif; ?>
								<?php if ( ! empty( $lr_extern['url'] ) ) : ?>
									<a class="btn--secondary" href="<?= esc_url( $lr_extern['url'] ); ?>" target="_blank" rel="noopener"><?= esc_html( $lr_extern['title'] ?: sprintf( __( 'Alle Bilder %d', 'loveride' ), $lr_j ) ); ?> ↗<span class="screen-reader-text"> <?php esc_html_e( '(öffnet in neuem Tab)', 'loveride' ); ?></span></a>
								<?php endif; ?>
							</div>
						<?php endif; ?>

					<?php else : ?>
						<?php
						$lr_namen    = lr_jahrgang_helfer( $lr_post );
						$lr_sichtbar = max( 4, (int) ( get_sub_field( 'namen_sichtbar' ) ?: 20 ) );
						$lr_orgs     = trim( (string) get_field( 'helfer_organisationen', $lr_post->ID ) );
						?>
						<?php if ( $lr_namen ) : ?>
							<ul class="namensliste">
								<?php foreach ( $lr_namen as $lr_n => $lr_name ) : ?>
									<li<?= $lr_n >= $lr_sichtbar ? ' hidden' : ''; ?>><?= esc_html( $lr_name ); ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>

						<?php if ( $lr_orgs !== '' ) : ?>
							<p class="jahrgang-helfer__organisationen"><?= nl2br( esc_html( $lr_orgs ) ); ?></p>
						<?php endif; ?>

						<?php if ( count( $lr_namen ) > $lr_sichtbar ) : ?>
							<?php $lr_anzahl = count( $lr_namen ); ?>
							<button type="button" class="btn--secondary namensliste__alle" data-alle>
								<?= esc_html( $lr_anzahl >= 100 ? sprintf( __( 'Alle rund %d anzeigen', 'loveride' ), (int) round( $lr_anzahl, -1 ) ) : sprintf( __( 'Alle %d anzeigen', 'loveride' ), $lr_anzahl ) ); ?>
							</button>
						<?php endif; ?>
					<?php endif; ?>

				</div>
			<?php endforeach; ?>
		<?php endif; ?>

	</div>

	<?php if ( $lr_darstellung === 'galerie' ) : ?>
		<?php get_template_part( 'template_parts/components/lightbox' ); ?>
	<?php endif; ?>
</section>
