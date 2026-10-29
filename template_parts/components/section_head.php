<?php
/**
 * Component: section head (Baustein – Sektionskopf: tagline, title, text, optional link) – used by content layouts; split = text in a second column, animate = scroll reveal.
 *
 * @package loveride
 */

$lr_tagline = (string) ( $args['tagline'] ?? '' );
$lr_titel   = (string) ( $args['titel'] ?? '' );
$lr_text    = (string) ( $args['text'] ?? '' );
$lr_link    = (array) ( ( $args['link'] ?? null ) ?: array() );
$lr_tag     = $args['tag'] ?? 'h2';
$lr_tag     = in_array( $lr_tag, array( 'h1', 'h2', 'h3' ), true ) ? $lr_tag : 'h2';
$lr_item    = ! empty( $args['animate'] ) ? ' fade-up' : '';

if ( ! $lr_tagline && ! $lr_titel && ! $lr_text && empty( $lr_link['url'] ) ) {
	return;
}

$lr_classes = array_filter( array(
	'section-head',
	! empty( $args['split'] ) ? 'section-head--split' : '',
	! empty( $args['animate'] ) ? 'group-fade-up' : '',
) );
?>
<div class="<?= esc_attr( implode( ' ', $lr_classes ) ); ?>">

	<?php if ( $lr_tagline || $lr_titel ) : ?>
		<div class="section-head__main<?= $lr_item; ?>">
			<?php if ( $lr_tagline ) : ?>
				<p class="section-head__tagline tagline"><?= esc_html( $lr_tagline ); ?></p>
			<?php endif; ?>

			<?php if ( $lr_titel ) : ?>
				<<?= $lr_tag; ?> class="section-head__title"><?= wp_kses_post( $lr_titel ); ?></<?= $lr_tag; ?>>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php if ( $lr_text || ! empty( $lr_link['url'] ) ) : ?>
		<div class="section-head__aside<?= $lr_item; ?>">
			<?php if ( $lr_text ) : ?>
				<div class="section-head__text"><?= wp_kses_post( $lr_text ); ?></div>
			<?php endif; ?>

			<?php if ( ! empty( $lr_link['url'] ) ) : ?>
				<a class="section-head__link arrow-link" <?= lr_link_attrs( $lr_link ); ?>><?= esc_html( $lr_link['title'] ?: __( 'Mehr erfahren', 'loveride' ) ); ?> <span aria-hidden="true">→</span></a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

</div>
