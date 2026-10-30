<?php
/**
 * Layout: Banner (layout_banner) – band with optional icon, title and text on the left and buttons on the right, red / dark / off-white, three title sizes; Ride-Out „Als Passagier mitfahren“, „Bike-Show“, Helfen „Helferadmin“ (Klein), „Danke“, later Rückblick „Chronik“.
 *
 * @package loveride
 */

$lr_titel = trim( (string) get_sub_field( 'titel' ) );
$lr_text  = trim( (string) get_sub_field( 'text' ) );

if ( $lr_titel === '' ) {
	return;
}

$lr_bg    = in_array( get_sub_field( 'hintergrund' ), array( 'dunkel', 'offwhite' ), true ) ? get_sub_field( 'hintergrund' ) : 'rot';
$lr_size  = in_array( get_sub_field( 'titelgroesse' ), array( 'mittel', 'klein' ), true ) ? get_sub_field( 'titelgroesse' ) : 'gross';
$lr_icon  = lr_icon( (string) get_sub_field( 'icon' ), 'banner__icon' );
?>
<section <?= lr_section_attrs( array( 'scheme-' . $lr_bg, 'banner--' . $lr_bg, 'banner--titel-' . $lr_size ) ); ?>>
	<div class="container">
		<div class="banner fade-up">

			<div class="banner__inhalt">
				<?= $lr_icon; ?>
				<div class="banner__text">
					<h2 class="banner__titel"><?= esc_html( $lr_titel ); ?></h2>
					<?php if ( $lr_text !== '' ) : ?>
						<p class="banner__beschreibung"><?= nl2br( esc_html( $lr_text ) ); ?></p>
					<?php endif; ?>
				</div>
			</div>

			<?php get_template_part( 'template_parts/components/buttons', null, array( 'buttons' => get_sub_field( 'buttons' ), 'class' => 'banner__buttons' ) ); ?>

		</div>
	</div>
</section>
