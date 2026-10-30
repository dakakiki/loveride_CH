<?php
/**
 * Layout: Spenden (layout_spenden) – red section from Geteilte Inhalte → Spenden: call with the central Spenden button and note, white panel with further ways to donate (icon, label, title, text; link or copy IBAN); Passagiere & Charity, can be placed on any page.
 *
 * @package loveride
 */

$lr_data = (array) lr_get_option( 'geteilt_spenden', array() );

$lr_head = array(
	'tagline' => (string) ( $lr_data['tagline'] ?? '' ),
	'titel'   => (string) ( $lr_data['titel'] ?? '' ),
	'text'    => (string) ( $lr_data['text'] ?? '' ),
);

$lr_optionen = array_values( array_filter(
	(array) ( ( $lr_data['optionen'] ?? null ) ?: array() ),
	fn( $option ) => trim( (string) ( $option['titel'] ?? '' ) ) !== ''
) );

if ( ! array_filter( $lr_head ) && ! $lr_optionen ) {
	return;
}

$lr_button  = lr_central_button( 'spenden' );
$lr_hinweis = trim( (string) ( $lr_data['online_hinweis'] ?? '' ) );
?>
<section <?= lr_section_attrs( array( 'scheme-rot' ), array( 'id' => 'spenden', 'data-spenden' => true ) ); ?>>
	<div class="container">
		<div class="spenden">

			<div class="spenden__aufruf">
				<?php get_template_part( 'template_parts/components/section_head', null, $lr_head + array( 'animate' => true ) ); ?>

				<?php if ( $lr_button ) : ?>
					<div class="spenden__online fade-up">
						<a class="btn--primary" <?= lr_link_attrs( $lr_button ); ?>><?php esc_html_e( 'Jetzt online spenden', 'loveride' ); ?></a>
						<?php if ( $lr_hinweis !== '' ) : ?>
							<p class="spenden__hinweis"><?= esc_html( $lr_hinweis ); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( $lr_optionen ) : ?>
				<ul class="spenden__optionen fade-up">
					<?php foreach ( $lr_optionen as $lr_option ) : ?>
						<?php
						$lr_aktion = $lr_option['aktion'] ?? 'keine';
						$lr_link   = (array) ( ( $lr_option['link'] ?? null ) ?: array() );
						?>
						<li class="spenden-option">
							<span class="spenden-option__icon"><?= lr_icon( $lr_option['icon'] ?? '' ); ?></span>
							<div class="spenden-option__text">
								<?php if ( trim( (string) $lr_option['label'] ) !== '' ) : ?>
									<p class="spenden-option__label"><?= esc_html( $lr_option['label'] ); ?></p>
								<?php endif; ?>
								<p class="spenden-option__titel"><?= esc_html( $lr_option['titel'] ); ?></p>
								<?php if ( trim( (string) $lr_option['text'] ) !== '' ) : ?>
									<p class="spenden-option__beschreibung"><?= esc_html( $lr_option['text'] ); ?></p>
								<?php endif; ?>

								<?php if ( $lr_aktion === 'iban' ) : ?>
									<button type="button" class="spenden-option__aktion btn--link" data-copy="<?= esc_attr( trim( preg_replace( '/^\s*IBAN\s*/i', '', (string) $lr_option['titel'] ) ) ); ?>" data-copied="<?= esc_attr__( 'IBAN kopiert', 'loveride' ); ?>"><span><?php esc_html_e( 'IBAN kopieren', 'loveride' ); ?></span><?= lr_ui_icon( 'chevron_right' ); ?></button>
								<?php elseif ( $lr_aktion === 'link' && ! empty( $lr_link['url'] ) ) : ?>
									<a class="spenden-option__aktion btn--link" <?= lr_link_attrs( $lr_link ); ?>><?= esc_html( $lr_link['title'] ?: __( 'Mehr erfahren', 'loveride' ) ); ?><?= lr_ui_icon( 'chevron_right' ); ?></a>
								<?php endif; ?>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

		</div>
	</div>
</section>
