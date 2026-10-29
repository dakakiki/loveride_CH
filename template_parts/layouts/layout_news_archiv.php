<?php
/**
 * Layout: News-Archiv (layout_news_archiv) – newest post highlighted, grid of News cards, „Ältere Beiträge laden“ (?seite=N, appended without reload by news.js); News page.
 *
 * @package loveride
 */

$lr_per_page = max( 3, (int) ( get_sub_field( 'pro_seite' ) ?: 9 ) );
$lr_featured = (bool) get_sub_field( 'neuester' );
$lr_page     = max( 1, absint( wp_unslash( $_GET['seite'] ?? 1 ) ) ); // phpcs:ignore WordPress.Security.NonceVerification
$lr_offset   = $lr_page === 1 ? 0 : ( $lr_featured ? 1 : 0 ) + ( $lr_page - 1 ) * $lr_per_page;
$lr_count    = $lr_per_page + ( $lr_featured && $lr_page === 1 ? 1 : 0 );

$lr_query = new WP_Query( array(
	'post_type'           => 'news',
	'post_status'         => 'publish',
	'posts_per_page'      => $lr_count,
	'offset'              => $lr_offset,
	'ignore_sticky_posts' => true,
	'fields'              => 'ids',
) );

$lr_posts     = array_map( 'intval', $lr_query->posts );
$lr_highlight = $lr_featured && $lr_page === 1 ? array_shift( $lr_posts ) : 0;
$lr_more      = $lr_query->found_posts > $lr_offset + $lr_count;
?>
<section <?= lr_section_attrs( array(), array( 'data-news-archiv' => true ) ); ?>>
	<div class="container">

		<?php if ( $lr_highlight ) : ?>
			<div class="news-archiv__featured fade-up">
				<?php get_template_part( 'template_parts/cards/news', null, array( 'post' => $lr_highlight, 'featured' => true, 'heading' => 'h2' ) ); ?>
			</div>
		<?php endif; ?>

		<?php if ( $lr_posts ) : ?>
			<ul class="news-grid" data-news-grid data-gsap-cards>
				<?php foreach ( $lr_posts as $lr_post ) : ?>
					<li><?php get_template_part( 'template_parts/cards/news', null, array( 'post' => $lr_post, 'heading' => 'h2' ) ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php elseif ( ! $lr_highlight ) : ?>
			<p class="news-archiv__empty"><?php esc_html_e( 'Noch keine Beiträge.', 'loveride' ); ?></p>
		<?php endif; ?>

		<?php if ( $lr_more ) : ?>
			<div class="news-archiv__more">
				<a class="btn--secondary" href="<?= esc_url( add_query_arg( 'seite', $lr_page + 1 ) ); ?>" data-news-more><?php esc_html_e( 'Ältere Beiträge laden', 'loveride' ); ?></a>
			</div>
		<?php endif; ?>

		<p class="screen-reader-text" aria-live="polite" data-news-status></p>

	</div>
</section>
