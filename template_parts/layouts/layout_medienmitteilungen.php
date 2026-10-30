<?php
/**
 * Layout: Medienmitteilungen (layout_medienmitteilungen) – Medienmitteilung posts: „Aktuell“ = newest (title, date + lead, files as download list) with a dark info box (Eckdaten from Event-Daten or own values, button); „Archiv“ = all older ones as rows (year, title, download of the German or first file) in a white box; Medien #aktuell, #archiv.
 *
 * @package loveride
 */

$lr_archiv = get_sub_field( 'darstellung' ) === 'archiv';
$lr_alle   = lr_medienmitteilungen();
$lr_posts  = $lr_archiv ? array_slice( $lr_alle, 1 ) : array_slice( $lr_alle, 0, 1 );

if ( ! $lr_posts ) {
	return;
}

$lr_hintergrund = get_sub_field( 'hintergrund' ) === 'offwhite' ? 'offwhite' : 'weiss';
$lr_head        = array(
	'tagline' => (string) get_sub_field( 'tagline' ),
	'titel'   => (string) get_sub_field( 'titel' ),
	'text'    => (string) get_sub_field( 'text' ),
);
?>
<?php if ( $lr_archiv ) : ?>
	<section <?= lr_section_attrs( array( 'scheme-' . $lr_hintergrund, 'medien--archiv' ), array( 'id' => 'archiv' ) ); ?>>
		<div class="container">

			<?php get_template_part( 'template_parts/components/section_head', null, $lr_head + array( 'animate' => true ) ); ?>

			<ul class="medien-archiv fade-up">
				<?php foreach ( $lr_posts as $lr_post ) : ?>
					<?php
					$lr_dateien = lr_medienmitteilung_dateien( $lr_post );
					$lr_datei   = current( array_filter( $lr_dateien, fn( $datei ) => $datei['sprache'] === 'de' ) ) ?: ( $lr_dateien[0] ?? null );
					?>
					<li class="medien-archiv__eintrag">
						<span class="medien-archiv__jahr"><?= esc_html( substr( lr_medienmitteilung_datum( $lr_post ), 0, 4 ) ); ?></span>
						<?php if ( $lr_datei ) : ?>
							<a class="medien-archiv__link" href="<?= esc_url( $lr_datei['url'] ); ?>" download>
								<span class="medien-archiv__titel"><?= esc_html( get_the_title( $lr_post ) ); ?></span>
								<?= lr_icon( 'download', 'medien-archiv__icon' ); ?>
								<span class="screen-reader-text"><?= esc_html( $lr_datei['typ'] ); ?></span>
							</a>
						<?php else : ?>
							<span class="medien-archiv__titel"><?= esc_html( get_the_title( $lr_post ) ); ?></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>

		</div>
	</section>
	<?php return; ?>
<?php endif; ?>
<?php
$lr_post    = $lr_posts[0];
$lr_lead    = trim( (string) get_field( 'lead', $lr_post->ID ) );
$lr_datum   = lr_format_date( lr_medienmitteilung_datum( $lr_post ), 'long' );
$lr_dateien = lr_medienmitteilung_dateien( $lr_post );
$lr_info    = (array) ( get_sub_field( 'info' ) ?: array() );
$lr_fakten  = lr_eckdaten( (array) ( $lr_info['eckdaten'] ?? array() ) );
$lr_link    = (array) ( ( $lr_info['link'] ?? null ) ?: array() );
$lr_mit_box = trim( (string) ( $lr_info['titel'] ?? '' ) ) !== '' || $lr_fakten;

$lr_head['titel'] = $lr_head['titel'] ?: esc_html( get_the_title( $lr_post ) );
$lr_head['text']  = $lr_head['text'] ?: '<p>' . esc_html( trim( sprintf( __( 'Medienmitteilung vom %s.', 'loveride' ), $lr_datum ) . ' ' . $lr_lead ) ) . '</p>';
?>
<section <?= lr_section_attrs( array( 'scheme-' . $lr_hintergrund, 'medien--aktuell' ), array( 'id' => 'aktuell' ) ); ?>>
	<div class="container">
		<div class="medien-aktuell<?= $lr_mit_box ? ' medien-aktuell--mit-box' : ''; ?>">

			<div class="medien-aktuell__haupt">
				<?php get_template_part( 'template_parts/components/section_head', null, $lr_head + array( 'animate' => true ) ); ?>

				<?php if ( $lr_dateien ) : ?>
					<ul class="downloads fade-up">
						<?php foreach ( $lr_dateien as $lr_datei ) : ?>
							<li>
								<a class="download" href="<?= esc_url( $lr_datei['url'] ); ?>" download<?= $lr_datei['sprache'] ? ' hreflang="' . esc_attr( $lr_datei['sprache'] ) . '"' : ''; ?>>
									<?= lr_icon( 'description', 'download__doc' ); ?>
									<span class="download__text">
										<span class="download__titel"<?= $lr_datei['sprache'] ? ' lang="' . esc_attr( $lr_datei['sprache'] ) . '"' : ''; ?>><?= esc_html( $lr_datei['titel'] ); ?></span>
										<?php if ( $lr_datei['typ'] !== '' ) : ?>
											<span class="download__typ"><?= esc_html( $lr_datei['typ'] ); ?></span>
										<?php endif; ?>
									</span>
									<?= lr_icon( 'download', 'download__icon' ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<?php if ( $lr_mit_box ) : ?>
				<aside class="medien-info fade-up">
					<?php if ( trim( (string) ( $lr_info['oberzeile'] ?? '' ) ) !== '' ) : ?>
						<p class="medien-info__oberzeile"><?= esc_html( $lr_info['oberzeile'] ); ?></p>
					<?php endif; ?>

					<?php if ( trim( (string) ( $lr_info['titel'] ?? '' ) ) !== '' ) : ?>
						<h3 class="medien-info__titel"><?= esc_html( $lr_info['titel'] ); ?></h3>
					<?php endif; ?>

					<?php if ( $lr_fakten ) : ?>
						<dl class="medien-info__fakten">
							<?php foreach ( $lr_fakten as $lr_fakt ) : ?>
								<div>
									<dt><?= esc_html( $lr_fakt['label'] ); ?></dt>
									<dd><?= esc_html( $lr_fakt['wert'] ); ?></dd>
								</div>
							<?php endforeach; ?>
						</dl>
					<?php endif; ?>

					<?php if ( ! empty( $lr_link['url'] ) ) : ?>
						<a class="btn--primary medien-info__button" <?= lr_link_attrs( $lr_link ); ?>><?= esc_html( $lr_link['title'] ?: __( 'Herunterladen', 'loveride' ) ); ?></a>
					<?php endif; ?>
				</aside>
			<?php endif; ?>

		</div>
	</div>
</section>
