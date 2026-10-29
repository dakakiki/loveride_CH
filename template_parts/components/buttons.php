<?php
/**
 * Component: buttons (Baustein – Buttons: link + style primary / secondary / link) – used by content layouts; class = extra classes on the wrapper.
 *
 * @package loveride
 */

$lr_buttons = array_filter(
	(array) ( $args['buttons'] ?? array() ),
	fn( $button ) => ! empty( $button['link']['url'] )
);

if ( ! $lr_buttons ) {
	return;
}
?>
<div class="buttons<?= ! empty( $args['class'] ) ? ' ' . esc_attr( $args['class'] ) : ''; ?>">
	<?php foreach ( $lr_buttons as $lr_button ) : ?>
		<?php $lr_stil = in_array( $lr_button['stil'] ?? '', array( 'primary', 'secondary', 'link' ), true ) ? $lr_button['stil'] : 'primary'; ?>
		<a class="btn--<?= esc_attr( $lr_stil ); ?>" <?= lr_link_attrs( $lr_button['link'] ); ?>><?= esc_html( $lr_button['link']['title'] ?: __( 'Mehr erfahren', 'loveride' ) ); ?><?= $lr_stil === 'link' ? lr_ui_icon( 'chevron_right' ) : ''; ?></a>
	<?php endforeach; ?>
</div>
