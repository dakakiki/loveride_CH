<?php
/**
 * Layout: Bild & Text (layout_cta_bild) – photo to the edge (half or narrow, left or right) next to a text panel (tagline, title, text with red-square list and H4 note with red line, buttons) on light grey / off-white / white / dark; Home „Ohne dich kein Love Ride“, „Teil deinen Platz“, Event „Familien“.
 *
 * @package loveride
 */

$lr_bild = get_sub_field( 'bild' );

$lr_head = array(
	'tagline' => (string) get_sub_field( 'tagline' ),
	'titel'   => (string) get_sub_field( 'titel' ),
	'text'    => (string) get_sub_field( 'text' ),
);

$lr_position = get_sub_field( 'bildposition' ) === 'rechts' ? 'rechts' : 'links';
$lr_breite   = get_sub_field( 'bildbreite' ) === 'schmal' ? 'schmal' : 'haelfte';
$lr_bg       = in_array( get_sub_field( 'hintergrund' ), array( 'offwhite', 'weiss', 'dunkel' ), true ) ? get_sub_field( 'hintergrund' ) : 'hellgrau';
$lr_scheme   = $lr_bg === 'hellgrau' ? 'offwhite' : $lr_bg;

if ( ! $lr_bild && ! array_filter( $lr_head ) ) {
	return;
}
?>
<section <?= lr_section_attrs( array( 'scheme-' . $lr_scheme, 'cta-bild--bild-' . $lr_position, 'cta-bild--' . $lr_breite, 'cta-bild--' . $lr_bg ) ); ?>>

	<div class="cta-bild__media">
		<?= lr_acf_image( $lr_bild, 'large', array(
			'class'   => 'cta-bild__image',
			'sizes'   => $lr_breite === 'schmal' ? '(min-width: 991px) 45vw, 100vw' : '(min-width: 991px) 50vw, 100vw',
			'loading' => 'lazy',
		) ); ?>
	</div>

	<div class="cta-bild__body">
		<div class="cta-bild__content">
			<?php get_template_part( 'template_parts/components/section_head', null, $lr_head + array( 'animate' => true ) ); ?>
			<?php get_template_part( 'template_parts/components/buttons', null, array( 'buttons' => get_sub_field( 'buttons' ), 'class' => 'fade-up' ) ); ?>
		</div>
	</div>

</section>
