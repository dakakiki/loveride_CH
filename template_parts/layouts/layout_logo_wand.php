<?php
/**
 * Layout: Logo-Wand (layout_logo_wand) – section head and buttons next to a 2-column grid of partner logos (Sponsoren „In Logo-Leiste“, 5 random per load via logo-wand.js) and a dashed „Dein Logo hier?“ tile linking like the first button; Home „Danke an unsere Partner“.
 *
 * @package loveride
 */

$lr_head = array(
	'tagline' => (string) get_sub_field( 'tagline' ),
	'titel'   => (string) get_sub_field( 'titel' ),
	'text'    => (string) get_sub_field( 'text' ),
);

$lr_buttons     = (array) ( get_sub_field( 'buttons' ) ?: array() );
$lr_sponsors    = lr_logo_sponsors();
$lr_platzhalter = trim( (string) get_sub_field( 'platzhalter' ) );
$lr_cta         = array();

foreach ( $lr_buttons as $lr_button ) {
	if ( ! empty( $lr_button['link']['url'] ) ) {
		$lr_cta = $lr_button['link'];
		break;
	}
}

if ( ! $lr_sponsors && ! array_filter( $lr_head ) ) {
	return;
}
?>
<section <?= lr_section_attrs( array( 'scheme-offwhite' ) ); ?>>
	<div class="container logo-wand">

		<div class="logo-wand__content">
			<?php get_template_part( 'template_parts/components/section_head', null, $lr_head + array( 'animate' => true ) ); ?>
			<?php get_template_part( 'template_parts/components/buttons', null, array( 'buttons' => $lr_buttons, 'class' => 'fade-up' ) ); ?>
		</div>

		<?php if ( $lr_sponsors || $lr_platzhalter ) : ?>
			<ul class="logo-wand__grid" data-logo-wand="5" data-gsap-cards>
				<?php foreach ( $lr_sponsors as $lr_i => $lr_sponsor ) : ?>
					<li class="logo-wand__item" data-logo<?= $lr_i >= 5 ? ' hidden' : ''; ?>>
						<?php $lr_logo = lr_acf_image( $lr_sponsor['logo'], 'medium', array( 'class' => 'logo-wand__logo', 'loading' => 'lazy', 'alt' => $lr_sponsor['title'] ) ); ?>
						<?php if ( $lr_sponsor['website'] ) : ?>
							<a class="logo-wand__tile" href="<?= esc_url( $lr_sponsor['website'] ); ?>" target="_blank" rel="noopener"><?= $lr_logo; ?><span class="screen-reader-text"> <?php esc_html_e( '(öffnet in neuem Tab)', 'loveride' ); ?></span></a>
						<?php else : ?>
							<span class="logo-wand__tile"><?= $lr_logo; ?></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>

				<?php if ( $lr_platzhalter ) : ?>
					<li class="logo-wand__item" data-logo-cta>
						<?php if ( ! empty( $lr_cta['url'] ) ) : ?>
							<a class="logo-wand__tile logo-wand__tile--cta" <?= lr_link_attrs( $lr_cta ); ?>><?= esc_html( $lr_platzhalter ); ?></a>
						<?php else : ?>
							<span class="logo-wand__tile logo-wand__tile--cta"><?= esc_html( $lr_platzhalter ); ?></span>
						<?php endif; ?>
					</li>
				<?php endif; ?>
			</ul>
		<?php endif; ?>

	</div>
</section>
