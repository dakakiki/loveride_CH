<?php
/**
 * Layout: Preise (layout_preise) – section head, optional benefits box (title, two-column list), price cards (label, price, text; standard / free with red price / highlighted dark card; own button (link or popup) or, on highlighted cards, the central Tickets button), footnote; Event „Tickets & Preise“, Sponsoren & Partner „Loverider-Club“.
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
$lr_leistung = array_values( array_filter( wp_list_pluck( (array) ( get_sub_field( 'leistungen' ) ?: array() ), 'text' ), fn( $text ) => trim( (string) $text ) !== '' ) );

if ( ! $lr_preise && ! array_filter( $lr_head ) ) {
	return;
}
?>
<section <?= lr_section_attrs( array( 'scheme-offwhite' ) ); ?>>
	<div class="container">

		<?php get_template_part( 'template_parts/components/section_head', null, $lr_head + array( 'animate' => true ) ); ?>

		<?php if ( $lr_preise ) : ?>
			<ul class="preise<?= $lr_leistung ? ' preise--leistungen' : ''; ?>" style="--preise: <?= (int) count( $lr_preise ); ?>" data-gsap-cards>
				<?php if ( $lr_leistung ) : ?>
					<li class="preise__item preise__item--leistungen">
						<div class="preise-leistungen">
							<?php if ( trim( (string) get_sub_field( 'leistungen_titel' ) ) !== '' ) : ?>
								<h3 class="preise-leistungen__titel"><?= esc_html( get_sub_field( 'leistungen_titel' ) ); ?></h3>
							<?php endif; ?>
							<ul class="preise-leistungen__liste">
								<?php foreach ( $lr_leistung as $lr_text ) : ?>
									<li><?= esc_html( $lr_text ); ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
					</li>
				<?php endif; ?>
				<?php foreach ( $lr_preise as $lr_preis ) : ?>
					<?php $lr_art = in_array( $lr_preis['darstellung'] ?? '', array( 'gratis', 'hervorgehoben' ), true ) ? $lr_preis['darstellung'] : 'standard'; ?>
					<li class="preise__item">
						<div class="preis preis--<?= esc_attr( $lr_art ); ?>">
							<p class="preis__label"><?= esc_html( $lr_preis['label'] ); ?></p>
							<p class="preis__value"><?= esc_html( $lr_preis['preis'] ); ?></p>
							<?php if ( trim( (string) $lr_preis['text'] ) !== '' ) : ?>
								<p class="preis__text"><?= esc_html( $lr_preis['text'] ); ?></p>
							<?php endif; ?>
							<?php $lr_popup = ! empty( $lr_preis['popup'] ) ? lr_popup_attrs( (string) $lr_preis['popup'], (string) $lr_preis['label'] ) : ''; ?>
							<?php if ( $lr_popup ) : ?>
								<a class="btn--primary preis__button preis__button--eigen" <?= $lr_popup; ?>><?= esc_html( ( $lr_preis['link']['title'] ?? '' ) ?: __( 'Anmelden', 'loveride' ) ); ?></a>
							<?php elseif ( ! empty( $lr_preis['link']['url'] ) ) : ?>
								<a class="btn--primary preis__button preis__button--eigen" <?= lr_link_attrs( $lr_preis['link'] ); ?>><?= esc_html( $lr_preis['link']['title'] ?: __( 'Mehr erfahren', 'loveride' ) ); ?></a>
							<?php elseif ( $lr_art === 'hervorgehoben' && $lr_tickets ) : ?>
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
