<?php
/**
 * Component: buttons (Baustein – Buttons: link + style primary / secondary / link, optional popup instead of the link) – used by content layouts; class = extra classes on the wrapper.
 *
 * @package loveride
 */

$lr_buttons = array_filter(
	(array) ( $args['buttons'] ?? array() ),
	fn( $button ) => ! empty( $button['link']['url'] ) || ( ! empty( $button['popup'] ) && ! empty( $button['link']['title'] ) )
);

if ( ! $lr_buttons ) {
	return;
}
?>
<div class="buttons<?= ! empty( $args['class'] ) ? ' ' . esc_attr( $args['class'] ) : ''; ?>">
	<?php foreach ( $lr_buttons as $lr_button ) : ?>
		<?php
		$lr_stil  = in_array( $lr_button['stil'] ?? '', array( 'primary', 'secondary', 'link' ), true ) ? $lr_button['stil'] : 'primary';
		$lr_popup = ! empty( $lr_button['popup'] ) ? lr_popup_attrs( (string) $lr_button['popup'] ) : '';
		?>
		<a class="btn--<?= esc_attr( $lr_stil ); ?>" <?= $lr_popup ?: lr_link_attrs( $lr_button['link'] ); ?>><?= esc_html( $lr_button['link']['title'] ?: __( 'Mehr erfahren', 'loveride' ) ); ?><?= $lr_stil === 'link' ? lr_ui_icon( 'chevron_right' ) : ''; ?></a>
	<?php endforeach; ?>
</div>
