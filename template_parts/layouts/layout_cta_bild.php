<?php
/**
 * Layout: CTA mit Bild (layout_cta_bild) – half-width photo to the edge next to an off-white text panel (tagline, title, text, buttons), photo left or right; Home „Ohne dich kein Love Ride“, „Teil deinen Platz“.
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

if ( ! $lr_bild && ! array_filter( $lr_head ) ) {
	return;
}
?>
<section <?= lr_section_attrs( array( 'scheme-offwhite', 'cta-bild--bild-' . $lr_position ) ); ?>>

	<div class="cta-bild__media">
		<?= lr_acf_image( $lr_bild, 'large', array(
			'class'   => 'cta-bild__image',
			'sizes'   => '(min-width: 991px) 50vw, 100vw',
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
