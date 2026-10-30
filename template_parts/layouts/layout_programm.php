<?php
/**
 * Layout: Programm & Line-up (layout_programm) – dark section: head with PDF button, day tabs, programme list (time, title / act, subtitle, image, place) with live bar on the event day (programm.js), Alle Acts grid (Act posts, „Mehr zum Act“ to the band website); Event #line-up / #acts.
 *
 * @package loveride
 */

$lr_head = array(
	'tagline' => (string) get_sub_field( 'tagline' ),
	'titel'   => (string) get_sub_field( 'titel' ),
	'text'    => (string) get_sub_field( 'text' ),
);

$lr_pdf      = get_sub_field( 'pdf' );
$lr_zone     = wp_timezone();
$lr_event    = (string) lr_event( 'datum' );
$lr_show     = (bool) get_sub_field( 'acts_anzeigen' );
$lr_acts     = $lr_show ? get_posts( array(
	'post_type'      => 'act',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
) ) : array();
$lr_act_ids  = wp_list_pluck( $lr_acts, 'ID' );
$lr_tage     = array();

foreach ( (array) ( get_sub_field( 'tage' ) ?: array() ) as $lr_tag ) {
	$lr_day = DateTimeImmutable::createFromFormat( '!Ymd', (string) ( $lr_tag['datum'] ?? '' ), $lr_zone );

	if ( ! $lr_day ) {
		continue;
	}

	$lr_items = array();

	foreach ( (array) ( $lr_tag['eintraege'] ?: array() ) as $lr_entry ) {
		$lr_time = DateTimeImmutable::createFromFormat( 'Ymd H:i', $lr_day->format( 'Ymd' ) . ' ' . substr( (string) $lr_entry['zeit'], 0, 5 ), $lr_zone );
		$lr_act  = (int) ( $lr_entry['act'] ?? 0 );
		$lr_act  = $lr_act && get_post_status( $lr_act ) === 'publish' ? $lr_act : 0;

		if ( ! $lr_time ) {
			continue;
		}

		$lr_titel = trim( (string) $lr_entry['titel'] ) ?: ( $lr_act ? get_post_field( 'post_title', $lr_act ) : '' );
		$lr_sub   = trim( (string) $lr_entry['untertitel'] ) ?: ( $lr_act ? implode( ', ', array_filter( array( get_field( 'genre', $lr_act ), get_field( 'herkunft', $lr_act ) ) ) ) : '' );
		$lr_link  = '';

		if ( $lr_act && in_array( $lr_act, $lr_act_ids, true ) ) {
			$lr_link = '#act-' . get_post_field( 'post_name', $lr_act );
		} elseif ( $lr_act && get_field( 'website', $lr_act ) ) {
			$lr_link = (string) get_field( 'website', $lr_act );
		}

		$lr_items[] = array(
			'time'  => $lr_time,
			'titel' => $lr_titel,
			'sub'   => $lr_sub,
			'ort'   => trim( (string) $lr_entry['ort'] ),
			'bild'  => ! empty( $lr_entry['bild'] ) ? $lr_entry['bild'] : ( $lr_act ? get_post_thumbnail_id( $lr_act ) : 0 ),
			'link'  => $lr_link,
		);
	}

	usort( $lr_items, fn( $a, $b ) => $a['time'] <=> $b['time'] );

	$lr_end = $lr_day->format( 'Ymd' ) === $lr_event && lr_event( 'offen_bis' )
		? DateTimeImmutable::createFromFormat( 'Ymd H:i', $lr_event . ' ' . substr( (string) lr_event( 'offen_bis' ), 0, 5 ), $lr_zone )
		: $lr_day->modify( '+1 day' );

	$lr_tage[] = array(
		'day'   => $lr_day,
		'label' => lr_format_date( $lr_day->format( 'Ymd' ), 'upper' ),
		'sub'   => trim( (string) $lr_tag['untertitel'] ),
		'end'   => $lr_end ?: $lr_day->modify( '+1 day' ),
		'items' => $lr_items,
	);
}

if ( ! $lr_tage && ! $lr_acts && ! array_filter( $lr_head ) ) {
	return;
}

$lr_uid      = 'programm-' . get_row_index();
$lr_selected = array_search( $lr_event, array_map( fn( $tag ) => $tag['day']->format( 'Ymd' ), $lr_tage ), true ) ?: 0;
?>
<section <?= lr_section_attrs( array( 'scheme-dunkel' ), array( 'data-programm' => true ) ); ?>>
	<div class="container">

		<div class="programm__head">
			<?php get_template_part( 'template_parts/components/section_head', null, $lr_head + array( 'animate' => true ) ); ?>

			<?php if ( ! empty( $lr_pdf['url'] ) ) : ?>
				<a class="btn--secondary programm__pdf fade-up" href="<?= esc_url( $lr_pdf['url'] ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Programm als PDF', 'loveride' ); ?><span class="screen-reader-text"> <?php esc_html_e( '(öffnet in neuem Tab)', 'loveride' ); ?></span></a>
			<?php endif; ?>
		</div>

		<?php if ( count( $lr_tage ) > 1 ) : ?>
			<div class="programm__tabs" role="tablist" aria-label="<?= esc_attr__( 'Programmtage', 'loveride' ); ?>">
				<?php foreach ( $lr_tage as $lr_i => $lr_tag ) : ?>
					<button type="button" class="programm__tab" role="tab" id="<?= esc_attr( $lr_uid . '-tab-' . $lr_i ); ?>" aria-controls="<?= esc_attr( $lr_uid . '-tag-' . $lr_i ); ?>" aria-selected="<?= $lr_i === $lr_selected ? 'true' : 'false'; ?>"<?= $lr_i === $lr_selected ? '' : ' tabindex="-1"'; ?>>
						<span class="programm__tab-date"><?= esc_html( $lr_tag['label'] ); ?></span>
						<?php if ( $lr_tag['sub'] ) : ?>
							<span class="programm__tab-sub"><?= esc_html( $lr_tag['sub'] ); ?></span>
						<?php endif; ?>
					</button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php foreach ( $lr_tage as $lr_i => $lr_tag ) : ?>
			<div class="programm__day" id="<?= esc_attr( $lr_uid . '-tag-' . $lr_i ); ?>" data-programm-day data-day-start="<?= esc_attr( $lr_tag['day']->format( 'c' ) ); ?>" data-day-end="<?= esc_attr( $lr_tag['day']->modify( '+1 day' )->format( 'c' ) ); ?>" data-end="<?= esc_attr( $lr_tag['end']->format( 'c' ) ); ?>"<?= count( $lr_tage ) > 1 ? ' role="tabpanel" aria-labelledby="' . esc_attr( $lr_uid . '-tab-' . $lr_i ) . '" tabindex="0"' : ''; ?>>
				<ol class="programm__list">
					<?php foreach ( $lr_tag['items'] as $lr_item ) : ?>
						<li class="programm-item" data-start="<?= esc_attr( $lr_item['time']->format( 'c' ) ); ?>">
							<p class="programm-item__time"><time datetime="<?= esc_attr( $lr_item['time']->format( 'c' ) ); ?>"><?= esc_html( $lr_item['time']->format( 'G.i' ) ); ?></time><span class="screen-reader-text programm-item__now" hidden> – <?php esc_html_e( 'läuft gerade', 'loveride' ); ?></span></p>

							<div class="programm-item__main">
								<p class="programm-item__title">
									<?php if ( $lr_item['link'] ) : ?>
										<a class="programm-item__link" href="<?= esc_url( $lr_item['link'] ); ?>"<?= strpos( $lr_item['link'], '#' ) === 0 ? '' : ' target="_blank" rel="noopener"'; ?>><?= esc_html( $lr_item['titel'] ); ?> <span aria-hidden="true">→</span></a>
									<?php else : ?>
										<?= esc_html( $lr_item['titel'] ); ?>
									<?php endif; ?>
								</p>
								<?php if ( $lr_item['sub'] ) : ?>
									<p class="programm-item__sub"><?= esc_html( $lr_item['sub'] ); ?></p>
								<?php endif; ?>
							</div>

							<div class="programm-item__media">
								<?php if ( $lr_item['bild'] ) : ?>
									<?= wp_get_attachment_image( is_array( $lr_item['bild'] ) ? $lr_item['bild']['ID'] : (int) $lr_item['bild'], 'medium', false, array(
										'class'   => 'programm-item__image',
										'alt'     => '',
										'sizes'   => '120px',
										'loading' => 'lazy',
									) ); ?>
								<?php endif; ?>
							</div>

							<p class="programm-item__ort"><?= esc_html( $lr_item['ort'] ); ?></p>
						</li>
					<?php endforeach; ?>
				</ol>
			</div>
		<?php endforeach; ?>

		<?php if ( $lr_acts ) : ?>
			<div class="programm__acts" id="acts">
				<div class="programm__acts-head">
					<h3 class="programm__acts-title"><?= esc_html( trim( (string) get_sub_field( 'acts_titel' ) ) ?: __( 'Alle Acts', 'loveride' ) ); ?></h3>
					<p class="programm__acts-count"><?= esc_html( sprintf( _n( '%d Act', '%d Acts', count( $lr_acts ), 'loveride' ), count( $lr_acts ) ) ); ?></p>
				</div>

				<ul class="acts-grid" data-gsap-cards>
					<?php foreach ( $lr_acts as $lr_act ) : ?>
						<?php
						$lr_website = (string) get_field( 'website', $lr_act->ID );
						$lr_genre   = implode( ' · ', array_filter( array( get_field( 'genre', $lr_act->ID ), get_field( 'herkunft', $lr_act->ID ) ) ) );
						?>
						<li>
							<article class="act-card" id="<?= esc_attr( 'act-' . $lr_act->post_name ); ?>">
								<div class="act-card__media">
									<?= get_the_post_thumbnail( $lr_act, 'medium_large', array(
										'class'   => 'act-card__image',
										'alt'     => '',
										'sizes'   => '(min-width: 991px) 308px, (min-width: 640px) 50vw, 100vw',
										'loading' => 'lazy',
									) ); ?>
								</div>
								<div class="act-card__body">
									<?php if ( $lr_genre ) : ?>
										<p class="act-card__genre"><?= esc_html( $lr_genre ); ?></p>
									<?php endif; ?>
									<h4 class="act-card__title"><?= esc_html( $lr_act->post_title ); ?></h4>
									<?php if ( get_field( 'kurztext', $lr_act->ID ) ) : ?>
										<p class="act-card__text"><?= esc_html( get_field( 'kurztext', $lr_act->ID ) ); ?></p>
									<?php endif; ?>
									<?php if ( $lr_website ) : ?>
										<a class="act-card__cta btn--link" href="<?= esc_url( $lr_website ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Mehr zum Act', 'loveride' ); ?><span class="screen-reader-text">: <?= esc_html( $lr_act->post_title ); ?> <?php esc_html_e( '(öffnet in neuem Tab)', 'loveride' ); ?></span><?= lr_ui_icon( 'chevron_right' ); ?></a>
									<?php endif; ?>
								</div>
							</article>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

	</div>
</section>
