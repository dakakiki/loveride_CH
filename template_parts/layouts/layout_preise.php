<?php
/**
 * Layout: Preise (layout_preise) – section head, price cards (label, price, text; standard / free with red price / highlighted dark card with the central Tickets button), footnote; Event „Tickets & Preise“.
 *
 * @package loveride
 */

$lr_head = array(
	'tagline' => (string) get_sub_field( 'tagline' ),
	'titel'   => (string) get_sub_field( 'titel' ),
	'text'    => (string) get_sub_field( 'text' ),
);

$lr_preise = array_values( array_filter(
	(array) ( get_sub_field( 'preise' ) ?: array() ),
	fn( $preis ) => trim( (string) ( $preis['preis'] ?? '' ) ) !== ''
) );

$lr_fussnote = trim( (string) get_sub_field( 'fussnote' ) );
$lr_tickets  = lr_central_button( 'tickets' );

if ( ! $lr_preise && ! array_filter( $lr_head ) ) {
	return;
}
?>
<section <?= lr_section_attrs( array( 'scheme-offwhite' ) ); ?>>
	<div class="container">

		<?php get_template_part( 'template_parts/components/section_head', null, $lr_head + array( 'animate' => true ) ); ?>

		<?php if ( $lr_preise ) : ?>
			<ul class="preise" data-gsap-cards>
				<?php foreach ( $lr_preise as $lr_preis ) : ?>
					<?php $lr_art = in_array( $lr_preis['darstellung'] ?? '', array( 'gratis', 'hervorgehoben' ), true ) ? $lr_preis['darstellung'] : 'standard'; ?>
					<li class="preise__item">
						<div class="preis preis--<?= esc_attr( $lr_art ); ?>">
							<p class="preis__label"><?= esc_html( $lr_preis['label'] ); ?></p>
							<p class="preis__value"><?= esc_html( $lr_preis['preis'] ); ?></p>
							<?php if ( trim( (string) $lr_preis['text'] ) !== '' ) : ?>
								<p class="preis__text"><?= esc_html( $lr_preis['text'] ); ?></p>
							<?php endif; ?>
							<?php if ( $lr_art === 'hervorgehoben' && $lr_tickets ) : ?>
								<a class="btn--primary preis__button" <?= lr_link_attrs( $lr_tickets ); ?>><?= esc_html( $lr_tickets['title'] ?: __( 'Tickets kaufen', 'loveride' ) ); ?></a>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php if ( $lr_fussnote ) : ?>
			<p class="preise__note fade-up"><?= esc_html( $lr_fussnote ); ?></p>
		<?php endif; ?>

	</div>
</section>
