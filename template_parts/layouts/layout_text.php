<?php
/**
 * Layout: Text (layout_text) – section head, small note and rich text; Impressum, Datenschutz and general text.
 *
 * @package loveride
 */

$lr_head = array(
	'tagline' => (string) get_sub_field( 'tagline' ),
	'titel'   => (string) get_sub_field( 'titel' ),
	'text'    => (string) get_sub_field( 'text' ),
);

$lr_hinweis = trim( (string) get_sub_field( 'hinweis' ) );
$lr_inhalt  = (string) get_sub_field( 'inhalt' );
$lr_breite  = get_sub_field( 'breite' ) === 'breit' ? 'breit' : 'schmal';

if ( ! array_filter( $lr_head ) && ! $lr_hinweis && ! $lr_inhalt ) {
	return;
}
?>
<section <?= lr_section_attrs( array( 'text-section--' . $lr_breite ) ); ?>>
	<div class="container">
		<div class="text-section__inner">

			<?php get_template_part( 'template_parts/components/section_head', null, $lr_head ); ?>

			<?php if ( $lr_hinweis ) : ?>
				<p class="text-section__note"><?= esc_html( $lr_hinweis ); ?></p>
			<?php endif; ?>

			<?php if ( $lr_inhalt ) : ?>
				<div class="text-section__body wysiwyg"><?= wp_kses_post( $lr_inhalt ); ?></div>
			<?php endif; ?>

		</div>
	</div>
</section>
