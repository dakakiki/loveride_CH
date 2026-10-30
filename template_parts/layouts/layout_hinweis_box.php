<?php
/**
 * Layout: Hinweis-Box (layout_hinweis_box) – centred white box with red frame: icon in a red circle, tagline, title, text, link; Ride-Out „Bitte nicht abklatschen!“.
 *
 * @package loveride
 */

$lr_head = array(
	'tagline' => (string) get_sub_field( 'tagline' ),
	'titel'   => (string) get_sub_field( 'titel' ),
	'text'    => (string) get_sub_field( 'text' ),
);

$lr_link = (array) ( get_sub_field( 'link' ) ?: array() );

if ( ! array_filter( $lr_head ) ) {
	return;
}

$lr_scheme = get_sub_field( 'hintergrund' ) === 'weiss' ? 'weiss' : 'offwhite';
$lr_icon   = lr_icon( (string) get_sub_field( 'icon' ) );
?>
<section <?= lr_section_attrs( array( 'scheme-' . $lr_scheme ) ); ?>>
	<div class="container">
		<div class="hinweis-box fade-up">

			<?php if ( $lr_icon ) : ?>
				<span class="hinweis-box__icon"><?= $lr_icon; ?></span>
			<?php endif; ?>

			<?php get_template_part( 'template_parts/components/section_head', null, $lr_head ); ?>

			<?php if ( ! empty( $lr_link['url'] ) ) : ?>
				<a class="hinweis-box__link btn--link" <?= lr_link_attrs( $lr_link ); ?>><?= esc_html( $lr_link['title'] ?: __( 'Mehr erfahren', 'loveride' ) ); ?><?= lr_ui_icon( 'chevron_right' ); ?></a>
			<?php endif; ?>

		</div>
	</div>
</section>
