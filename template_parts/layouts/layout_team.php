<?php
/**
 * Layout: Team (layout_team) – section head and team members of the chosen Teamgruppen (none = all) in their order: photo, function, name, profession; 4 / 2 columns; Über uns „Committee“.
 *
 * @package loveride
 */

$lr_head = array(
	'tagline' => (string) get_sub_field( 'tagline' ),
	'titel'   => (string) get_sub_field( 'titel' ),
	'text'    => (string) get_sub_field( 'text' ),
);

$lr_gruppen = array_filter( array_map( 'intval', (array) get_sub_field( 'gruppen' ) ) );
$lr_team    = get_posts( array(
	'post_type'      => 'mitglied',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
	'tax_query'      => $lr_gruppen ? array( array( 'taxonomy' => 'teamgruppe', 'terms' => $lr_gruppen ) ) : array(),
) );

if ( ! $lr_team && ! array_filter( $lr_head ) ) {
	return;
}
?>
<section <?= lr_section_attrs( array( 'scheme-weiss' ), array( 'id' => 'team' ) ); ?>>
	<div class="container">

		<?php get_template_part( 'template_parts/components/section_head', null, $lr_head + array( 'animate' => true ) ); ?>

		<?php if ( $lr_team ) : ?>
			<ul class="team" data-gsap-cards>
				<?php foreach ( $lr_team as $lr_person ) : ?>
					<?php
					$lr_name     = trim( get_field( 'vorname', $lr_person->ID ) . ' ' . get_field( 'nachname', $lr_person->ID ) ) ?: get_the_title( $lr_person );
					$lr_funktion = trim( (string) get_field( 'funktion', $lr_person->ID ) );
					$lr_beruf    = trim( (string) get_field( 'beruf', $lr_person->ID ) );
					?>
					<li class="team-person">
						<div class="team-person__foto">
							<?php if ( has_post_thumbnail( $lr_person ) ) : ?>
								<?= get_the_post_thumbnail( $lr_person, 'medium_large', array(
									'class'   => 'team-person__bild',
									'sizes'   => '(min-width: 991px) 25vw, 50vw',
									'loading' => 'lazy',
									'alt'     => $lr_name,
								) ); ?>
							<?php endif; ?>
						</div>
						<?php if ( $lr_funktion !== '' ) : ?>
							<p class="team-person__funktion"><?= esc_html( $lr_funktion ); ?></p>
						<?php endif; ?>
						<h3 class="team-person__name"><?= esc_html( $lr_name ); ?></h3>
						<?php if ( $lr_beruf !== '' ) : ?>
							<p class="team-person__beruf"><?= esc_html( $lr_beruf ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

	</div>
</section>
