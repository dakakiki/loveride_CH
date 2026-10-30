<?php
/**
 * Single News: back link, category, date, author (WP user), title, excerpt as lead, title image, block content, links in two columns (News – Details, always new tab), tags, share buttons, Weitere Beiträge (by tag, then category).
 *
 * @package loveride
 */

$lr_id       = get_the_ID();
$lr_category = lr_news_category( $lr_id );
$lr_author   = lr_news_author( $lr_id );
$lr_lead     = lr_news_teaser( $lr_id );
$lr_boxen    = array_values( array_filter(
	array( get_field( 'links_links' ), get_field( 'links_rechts' ) ),
	fn( $box ) => ! empty( $box['links'] )
) );
$lr_tags     = implode( ' ', lr_news_tags( $lr_id ) );
$lr_url      = get_permalink( $lr_id );
$lr_title    = wp_strip_all_tags( html_entity_decode( get_the_title( $lr_id ), ENT_QUOTES, 'UTF-8' ) );
$lr_more     = lr_news_more( $lr_id );
$lr_news     = lr_news_page_url();

$lr_share = array(
	'facebook' => array( 'Facebook', 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( $lr_url ), __( 'Auf Facebook teilen', 'loveride' ) ),
	'whatsapp' => array( 'WhatsApp', 'https://wa.me/?text=' . rawurlencode( $lr_title . ' ' . $lr_url ), __( 'Auf WhatsApp teilen', 'loveride' ) ),
	'email'    => array( 'E-Mail', 'mailto:?subject=' . rawurlencode( $lr_title ) . '&body=' . rawurlencode( $lr_url ), __( 'Per E-Mail teilen', 'loveride' ) ),
);
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'news-article' ); ?>>

	<header class="container news-article__head">
		<a class="news-article__back" href="<?= esc_url( $lr_news ); ?>"><?= lr_ui_icon( 'arrow_back' ); ?><?php esc_html_e( 'Alle News', 'loveride' ); ?></a>

		<p class="news-meta">
			<?php if ( $lr_category ) : ?>
				<span class="news-tag"><?= esc_html( $lr_category->name ); ?></span>
			<?php endif; ?>
			<span class="news-meta__text"><time datetime="<?= esc_attr( get_the_date( 'Y-m-d' ) ); ?>"><?= esc_html( lr_news_date( $lr_id ) ); ?></time><?= $lr_author ? ' · ' . esc_html( $lr_author ) : ''; ?></span>
		</p>

		<h1 class="news-article__title"><?php the_title(); ?></h1>

		<?php if ( $lr_lead ) : ?>
			<p class="news-article__lead"><?= wp_kses_post( $lr_lead ); ?></p>
		<?php endif; ?>
	</header>

	<?php if ( has_post_thumbnail() ) : ?>
		<figure class="container news-article__image">
			<?php the_post_thumbnail( 'full', array(
				'class' => 'news-article__image-img',
				'sizes' => '(min-width: 1440px) 1280px, 100vw',
			) ); ?>
		</figure>
	<?php endif; ?>

	<div class="container news-article__body">

		<div class="wysiwyg news-article__content">
			<?php the_content(); ?>
		</div>

		<?php if ( $lr_boxen ) : ?>
			<div class="news-links">
				<?php foreach ( $lr_boxen as $lr_box ) : ?>
					<div class="news-links__group">
						<?php if ( ! empty( $lr_box['titel'] ) ) : ?>
							<p class="news-links__title"><?= esc_html( $lr_box['titel'] ); ?></p>
						<?php endif; ?>
						<ul class="news-links__list">
							<?php foreach ( $lr_box['links'] as $lr_link ) : ?>
								<?php $lr_link = (array) ( $lr_link['link'] ?? array() ); ?>
								<?php if ( empty( $lr_link['url'] ) ) : ?>
									<?php continue; ?>
								<?php endif; ?>
								<li>
									<a class="news-links__link" href="<?= esc_url( $lr_link['url'] ); ?>" target="_blank" rel="noopener"><?= esc_html( $lr_link['title'] ?: wp_parse_url( $lr_link['url'], PHP_URL_HOST ) ); ?><?= lr_icon( 'open_in_new' ); ?><span class="screen-reader-text"> <?php esc_html_e( '(öffnet in neuem Tab)', 'loveride' ); ?></span></a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( $lr_tags ) : ?>
			<p class="news-article__tags"><?= esc_html( $lr_tags ); ?></p>
		<?php endif; ?>

		<div class="news-share">
			<p class="news-share__label"><?php esc_html_e( 'Beitrag teilen', 'loveride' ); ?></p>
			<ul class="news-share__list">
				<?php foreach ( $lr_share as list( $lr_label, $lr_href, $lr_aria ) ) : ?>
					<li><a class="news-share__button" href="<?= esc_url( $lr_href ); ?>" aria-label="<?= esc_attr( $lr_aria ); ?>"<?= strpos( $lr_href, 'mailto:' ) === 0 ? '' : ' target="_blank" rel="noopener"'; ?>><?= esc_html( $lr_label ); ?></a></li>
				<?php endforeach; ?>
				<li>
					<button type="button" class="news-share__button" data-share-copy="<?= esc_attr( $lr_url ); ?>"><?php esc_html_e( 'Link kopieren', 'loveride' ); ?></button>
					<span class="screen-reader-text" aria-live="polite" data-share-status></span>
				</li>
			</ul>
		</div>

	</div>

</article>

<?php if ( $lr_more ) : ?>
	<section class="news-more" aria-labelledby="news-more-title">
		<div class="container">
			<div class="news-more__head">
				<h2 class="news-more__title" id="news-more-title"><?php esc_html_e( 'Weitere Beiträge', 'loveride' ); ?></h2>
				<a class="btn--link" href="<?= esc_url( $lr_news ); ?>"><?php esc_html_e( 'Alle News', 'loveride' ); ?><?= lr_ui_icon( 'chevron_right' ); ?></a>
			</div>
			<ul class="news-grid" data-gsap-cards>
				<?php foreach ( $lr_more as $lr_post ) : ?>
					<li><?php get_template_part( 'template_parts/cards/news', null, array( 'post' => $lr_post, 'heading' => 'h3' ) ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
<?php endif; ?>
