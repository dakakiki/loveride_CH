<?php
/**
 * Layout: FAQ (layout_faq) – head with buttons next to an accordion of FAQ posts from the chosen FAQ-Kategorien (order = Reihenfolge), first question optionally open; Programm & Event, Ride-Out, Passagiere & Charity, Helfen.
 *
 * @package loveride
 */

$lr_head = array(
	'tagline' => (string) get_sub_field( 'tagline' ),
	'titel'   => (string) get_sub_field( 'titel' ),
	'text'    => (string) get_sub_field( 'text' ),
);

$lr_kategorien = array_filter( array_map( 'intval', (array) get_sub_field( 'kategorien' ) ) );
$lr_anzahl     = (int) get_sub_field( 'anzahl' );

$lr_fragen = get_posts( array(
	'post_type'      => 'faq',
	'post_status'    => 'publish',
	'posts_per_page' => $lr_anzahl > 0 ? $lr_anzahl : -1,
	'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
	'tax_query'      => $lr_kategorien ? array( array( 'taxonomy' => 'faq_kategorie', 'terms' => $lr_kategorien ) ) : array(),
) );

if ( ! $lr_fragen ) {
	return;
}

$lr_offen = (bool) get_sub_field( 'erste_offen' );
$lr_uid   = wp_unique_id( 'faq-' );
?>
<section <?= lr_section_attrs( array( 'scheme-weiss' ), array( 'id' => 'faq', 'data-faq' => true ) ); ?>>
	<div class="container">
		<div class="faq">

			<div class="faq__head">
				<?php get_template_part( 'template_parts/components/section_head', null, $lr_head + array( 'animate' => true ) ); ?>
				<?php get_template_part( 'template_parts/components/buttons', null, array( 'buttons' => get_sub_field( 'buttons' ), 'class' => 'fade-up' ) ); ?>
			</div>

			<div class="faq__liste fade-up">
				<?php foreach ( $lr_fragen as $lr_i => $lr_frage ) : ?>
					<?php
					$lr_open = $lr_offen && $lr_i === 0;
					$lr_id   = $lr_uid . '-' . $lr_i;
					?>
					<div class="faq-item<?= $lr_open ? ' is-open' : ''; ?>">
						<h3 class="faq-item__frage">
							<button type="button" class="faq-item__button" id="<?= esc_attr( $lr_id ); ?>-frage" aria-expanded="<?= $lr_open ? 'true' : 'false'; ?>" aria-controls="<?= esc_attr( $lr_id ); ?>">
								<span class="faq-item__text"><?= esc_html( get_the_title( $lr_frage ) ); ?></span>
								<span class="faq-item__icon" aria-hidden="true"></span>
							</button>
						</h3>
						<div class="faq-item__panel" id="<?= esc_attr( $lr_id ); ?>" role="region" aria-labelledby="<?= esc_attr( $lr_id ); ?>-frage">
							<div class="faq-item__antwort">
								<?= wp_kses_post( (string) get_field( 'antwort', $lr_frage->ID ) ); ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

		</div>
	</div>
</section>
