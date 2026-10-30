<?php
/**
 * Layout: Gut zu wissen (layout_gut_zu_wissen) – dark section with the selected points from Geteilte Inhalte → Gut zu wissen (none selected = all) in one row, red top line, icon, title, text; Programm & Event, Ride-Out, Passagiere & Charity.
 *
 * @package loveride
 */

$lr_auswahl = array_filter( (array) get_sub_field( 'punkte' ) );
$lr_punkte  = lr_gut_zu_wissen_punkte();

if ( $lr_auswahl ) {
	$lr_punkte = array_values( array_filter( $lr_punkte, fn( $punkt ) => in_array( $punkt['key'], $lr_auswahl, true ) ) );
}

if ( ! $lr_punkte ) {
	return;
}

$lr_titel = trim( (string) ( lr_get_option( 'geteilt_gut_zu_wissen', array() )['titel'] ?? '' ) );
?>
<section <?= lr_section_attrs( array( 'scheme-dunkel' ), array( 'id' => 'gut-zu-wissen' ) ); ?>>
	<div class="container">

		<?php if ( $lr_titel !== '' ) : ?>
			<h2 class="gut-zu-wissen__title fade-up"><?= esc_html( $lr_titel ); ?></h2>
		<?php endif; ?>

		<ul class="gut-zu-wissen" style="--spalten: <?= (int) min( count( $lr_punkte ), 4 ); ?>" data-gsap-cards>
			<?php foreach ( $lr_punkte as $lr_punkt ) : ?>
				<li class="gut-zu-wissen__punkt">
					<?= lr_icon( $lr_punkt['icon'], 'gut-zu-wissen__icon' ); ?>
					<h3 class="gut-zu-wissen__name"><?= esc_html( $lr_punkt['titel'] ); ?></h3>

					<?php if ( $lr_punkt['text'] !== '' ) : ?>
						<p class="gut-zu-wissen__text"><?= esc_html( $lr_punkt['text'] ); ?></p>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>

	</div>
</section>
