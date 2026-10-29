<?php
/**
 * Card: News post (image, category, date, title, teaser, „Weiterlesen“), whole card clickable; featured = wide card with author and red button; used by News-Archiv and Weitere Beiträge.
 *
 * @package loveride
 */

$lr_id       = (int) ( $args['post'] ?? get_the_ID() );
$lr_featured = ! empty( $args['featured'] );
$lr_tag      = in_array( $args['heading'] ?? 'h3', array( 'h2', 'h3' ), true ) ? $args['heading'] : 'h3';
$lr_category = lr_news_category( $lr_id );
$lr_teaser   = lr_news_teaser( $lr_id );
$lr_author   = $lr_featured ? trim( (string) get_field( 'autor_anzeige', $lr_id ) ) : '';
$lr_title    = get_the_title( $lr_id );
?>
<article class="news-card<?= $lr_featured ? ' news-card--featured' : ''; ?>">

	<div class="news-card__media">
		<?php if ( has_post_thumbnail( $lr_id ) ) : ?>
			<?= get_the_post_thumbnail( $lr_id, $lr_featured ? 'large' : 'medium_large', array(
				'class'   => 'news-card__image',
				'alt'     => '',
				'sizes'   => $lr_featured ? '(min-width: 991px) 720px, 100vw' : '(min-width: 991px) 405px, (min-width: 640px) 50vw, 100vw',
				'loading' => 'lazy',
			) ); ?>
		<?php endif; ?>
	</div>

	<div class="news-card__body">
		<p class="news-meta">
			<?php if ( $lr_category ) : ?>
				<span class="news-tag"><?= esc_html( $lr_category->name ); ?></span>
			<?php endif; ?>
			<span class="news-meta__text"><time datetime="<?= esc_attr( get_the_date( 'Y-m-d', $lr_id ) ); ?>"><?= esc_html( lr_news_date( $lr_id ) ); ?></time><?= $lr_author ? ' · ' . esc_html( $lr_author ) : ''; ?></span>
		</p>

		<<?= $lr_tag; ?> class="news-card__title"><?= wp_kses_post( $lr_title ); ?></<?= $lr_tag; ?>>

		<?php if ( $lr_teaser ) : ?>
			<p class="news-card__text"><?= wp_kses_post( $lr_teaser ); ?></p>
		<?php endif; ?>

		<a class="news-card__cta <?= $lr_featured ? 'btn--primary' : 'btn--link'; ?>" href="<?= esc_url( get_permalink( $lr_id ) ); ?>"><?php esc_html_e( 'Weiterlesen', 'loveride' ); ?><span class="screen-reader-text">: <?= wp_kses_post( $lr_title ); ?></span><?= $lr_featured ? '' : lr_ui_icon( 'chevron_right' ); ?></a>
	</div>

</article>
