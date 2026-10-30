<?php
/**
 * Layout: News-Teaser (layout_news_teaser) – section head, latest News as plain cards (3 or 6), „Alle News“ to the News page; Home „Neuigkeiten“.
 *
 * @package loveride
 */

$lr_head = array(
	'tagline' => (string) get_sub_field( 'tagline' ),
	'titel'   => (string) get_sub_field( 'titel' ),
	'text'    => (string) get_sub_field( 'text' ),
);

$lr_posts = get_posts( array(
	'post_type'      => 'news',
	'post_status'    => 'publish',
	'posts_per_page' => (int) get_sub_field( 'anzahl' ) === 6 ? 6 : 3,
	'fields'         => 'ids',
) );

if ( ! $lr_posts ) {
	return;
}
?>
<section <?= lr_section_attrs(); ?>>
	<div class="container">

		<?php get_template_part( 'template_parts/components/section_head', null, $lr_head + array( 'animate' => true ) ); ?>

		<ul class="news-grid" data-gsap-cards>
			<?php foreach ( $lr_posts as $lr_post ) : ?>
				<li><?php get_template_part( 'template_parts/cards/news', null, array( 'post' => $lr_post, 'plain' => true, 'heading' => array_filter( $lr_head ) ? 'h3' : 'h2' ) ); ?></li>
			<?php endforeach; ?>
		</ul>

		<div class="news-teaser__actions fade-up">
			<a class="btn--secondary" href="<?= esc_url( lr_news_page_url() ); ?>"><?php esc_html_e( 'Alle News', 'loveride' ); ?></a>
		</div>

	</div>
</section>
