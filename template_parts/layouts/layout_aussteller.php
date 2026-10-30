<?php
/**
 * Layout: Aussteller (layout_aussteller) – section head, optional filter by Aussteller-Kategorie and cards (logo, category, name, website) of all exhibitors; Sponsoren & Partner „Alle Aussteller“.
 *
 * @package loveride
 */

$lr_head = array(
	'tagline' => (string) get_sub_field( 'tagline' ),
	'titel'   => (string) get_sub_field( 'titel' ),
	'text'    => (string) get_sub_field( 'text' ),
);

$lr_aussteller = get_posts( array(
	'post_type'      => 'aussteller',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
) );

if ( ! $lr_aussteller && ! array_filter( $lr_head ) ) {
	return;
}

$lr_kategorien = get_sub_field( 'filter' ) ? get_terms( array(
	'taxonomy'   => 'aussteller_kategorie',
	'hide_empty' => true,
	'orderby'    => 'term_id',
) ) : array();
?>
<section <?= lr_section_attrs( array( 'scheme-weiss' ), array( 'id' => 'aussteller-liste', 'data-aussteller' => true ) ); ?>>
	<div class="container">

		<?php get_template_part( 'template_parts/components/section_head', null, $lr_head + array( 'animate' => true ) ); ?>

		<?php if ( ! is_wp_error( $lr_kategorien ) && count( $lr_kategorien ) > 1 ) : ?>
			<div class="aussteller-filter fade-up" role="group" aria-label="<?= esc_attr__( 'Nach Kategorie filtern', 'loveride' ); ?>">
				<button type="button" class="aussteller-filter__button" data-filter="" aria-pressed="true"><?php esc_html_e( 'Alle', 'loveride' ); ?></button>
				<?php foreach ( $lr_kategorien as $lr_kategorie ) : ?>
					<button type="button" class="aussteller-filter__button" data-filter="<?= esc_attr( $lr_kategorie->slug ); ?>" aria-pressed="false"><?= esc_html( $lr_kategorie->name ); ?></button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( $lr_aussteller ) : ?>
			<ul class="aussteller-liste" data-gsap-cards aria-live="polite">
				<?php foreach ( $lr_aussteller as $lr_eintrag ) : ?>
					<?php
					$lr_terms   = wp_get_post_terms( $lr_eintrag->ID, 'aussteller_kategorie' );
					$lr_terms   = is_wp_error( $lr_terms ) ? array() : $lr_terms;
					$lr_logo    = get_field( 'logo', $lr_eintrag->ID );
					$lr_website = (string) get_field( 'website', $lr_eintrag->ID );
					?>
					<li class="aussteller-karte" data-kategorien="<?= esc_attr( implode( ' ', wp_list_pluck( $lr_terms, 'slug' ) ) ); ?>">
						<div class="aussteller-karte__logo">
							<?php if ( $lr_logo ) : ?>
								<?= lr_acf_image( $lr_logo, 'medium', array( 'class' => 'aussteller-karte__bild', 'loading' => 'lazy', 'alt' => get_the_title( $lr_eintrag ) ) ); ?>
							<?php endif; ?>
						</div>
						<?php if ( $lr_terms ) : ?>
							<p class="aussteller-karte__kategorie"><?= esc_html( $lr_terms[0]->name ); ?></p>
						<?php endif; ?>
						<h3 class="aussteller-karte__name"><?= esc_html( get_the_title( $lr_eintrag ) ); ?></h3>
						<?php if ( $lr_website !== '' ) : ?>
							<a class="aussteller-karte__website" href="<?= esc_url( $lr_website ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Zur Website', 'loveride' ); ?><?= lr_icon( 'open_in_new' ); ?><span class="screen-reader-text"> <?php esc_html_e( '(öffnet in neuem Tab)', 'loveride' ); ?></span></a>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

	</div>
</section>
