<?php
/**
 * Component: hero background (image, optional MP4 video, overlay) – used by hero layouts.
 *
 * @package loveride
 */

$lr_bild  = $args['bild'] ?? null;
$lr_video = $args['video'] ?? null;
$lr_eager = ! empty( $args['eager'] );

if ( empty( $lr_bild ) && empty( $lr_video['url'] ) ) {
	return;
}

$lr_img_attrs = array(
	'class'   => 'hero-media__image',
	'sizes'   => '100vw',
	'loading' => $lr_eager ? 'eager' : 'lazy',
);

if ( $lr_eager ) {
	$lr_img_attrs['fetchpriority'] = 'high';
}
?>
<div class="hero-media">
	<?= lr_acf_image( $lr_bild, 'full', $lr_img_attrs ); ?>

	<?php if ( ! empty( $lr_video['url'] ) ) : ?>
		<video class="hero-media__video" muted loop playsinline preload="<?= $lr_eager ? 'auto' : 'none'; ?>" aria-hidden="true" data-hero-video>
			<source src="<?= esc_url( $lr_video['url'] ); ?>" type="video/mp4">
		</video>
	<?php endif; ?>
</div>
