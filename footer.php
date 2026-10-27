<?php
/**
 * Footer: next Love Ride and newsletter, partner logo strip, contact/donation, footer menu columns, legal links.
 *
 * @package loveride
 */

$lr_event_date  = lr_event_date();
$lr_event_info  = implode( ' · ', array_filter( array( lr_event_opening_hours(), lr_event( 'ort' ) ) ) );
$lr_ticket_link = lr_event( 'ticket_link' ) ?: ( lr_header_buttons()['tickets'] ?? null );
$lr_newsletter  = (array) lr_get_option( 'geteilt_newsletter', array() );
$lr_sponsors    = lr_logo_sponsors();
$lr_kontakt     = (array) lr_get_option( 'kontakt', array() );
$lr_spenden     = (array) lr_get_option( 'spenden', array() );
$lr_social      = (array) lr_get_option( 'social', array() );
$lr_columns     = lr_menu_tree( 'menu-footer' );
$lr_legal       = lr_menu_items( 'menu-legal' );
?>

<footer class="footer">
	<div class="footer__inner">

		<?php if ( $lr_event_date || ! empty( $lr_newsletter['titel'] ) ) : ?>
			<div class="footer__top">

				<?php if ( $lr_event_date ) : ?>
					<div class="footer__event">
						<div class="footer__event-text">
							<p class="footer__event-label"><?= esc_html( lr_event( 'footer_titel', __( 'Nächster Love Ride', 'loveride' ) ) ); ?></p>
							<p class="footer__event-date"><?= esc_html( $lr_event_date ); ?></p>
							<?php if ( $lr_event_info ) : ?>
								<p class="footer__event-info"><?= esc_html( $lr_event_info ); ?></p>
							<?php endif; ?>
						</div>
						<?php if ( ! empty( $lr_ticket_link['url'] ) ) : ?>
							<a class="btn--primary" <?= lr_link_attrs( $lr_ticket_link ); ?>><?= esc_html( $lr_ticket_link['title'] ?: __( 'Tickets kaufen', 'loveride' ) ); ?></a>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $lr_newsletter['titel'] ) ) : ?>
					<div class="footer__newsletter">
						<p class="footer__title"><?= esc_html( $lr_newsletter['titel'] ); ?></p>
						<?php if ( ! empty( $lr_newsletter['text'] ) ) : ?>
							<p class="footer__newsletter-text"><?= esc_html( $lr_newsletter['text'] ); ?></p>
						<?php endif; ?>

						<?php if ( ! empty( $lr_newsletter['formular'] ) ) : ?>
							<div class="footer__cf7"><?= do_shortcode( $lr_newsletter['formular'] ); ?></div>
						<?php else : ?>
							<form class="footer__form" action="#" onsubmit="return false;">
								<label class="screen-reader-text" for="footer-newsletter-email"><?php esc_html_e( 'Deine E-Mail-Adresse', 'loveride' ); ?></label>
								<input id="footer-newsletter-email" type="email" placeholder="<?php esc_attr_e( 'Deine E-Mail-Adresse', 'loveride' ); ?>" autocomplete="email">
								<button class="btn--primary" type="submit"><?php esc_html_e( 'Anmelden', 'loveride' ); ?></button>
							</form>
						<?php endif; ?>

						<?php if ( ! empty( $lr_newsletter['datenschutz_hinweis'] ) ) : ?>
							<div class="footer__note"><?= wp_kses_post( $lr_newsletter['datenschutz_hinweis'] ); ?></div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

			</div>
		<?php endif; ?>

		<?php if ( $lr_sponsors ) : ?>
			<div class="footer__sponsors">
				<p class="footer__title"><?= esc_html( lr_get_option( 'sponsoren_titel', __( 'Unsere Hauptpartner', 'loveride' ) ) ); ?></p>
				<ul class="footer__logos">
					<?php foreach ( $lr_sponsors as $lr_sponsor ) : ?>
						<li>
							<?php if ( $lr_sponsor['website'] ) : ?>
								<a href="<?= esc_url( $lr_sponsor['website'] ); ?>" target="_blank" rel="noopener" title="<?= esc_attr( $lr_sponsor['title'] ); ?>">
									<?= lr_acf_image( $lr_sponsor['logo'], 'medium', array( 'loading' => 'lazy', 'alt' => $lr_sponsor['title'] ) ); ?>
								</a>
							<?php else : ?>
								<?= lr_acf_image( $lr_sponsor['logo'], 'medium', array( 'loading' => 'lazy', 'alt' => $lr_sponsor['title'] ) ); ?>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<div class="footer__main">

			<div class="footer__brand">

				<div class="footer__contact">
					<p class="footer__title"><?php esc_html_e( 'Kontakt', 'loveride' ); ?></p>
					<?php if ( ! empty( $lr_kontakt['organisation'] ) ) : ?>
						<p><?= esc_html( $lr_kontakt['organisation'] ); ?></p>
					<?php endif; ?>
					<?php if ( ! empty( $lr_kontakt['adresse'] ) ) : ?>
						<p><?= wp_kses( $lr_kontakt['adresse'], array( 'br' => array() ) ); ?></p>
					<?php endif; ?>
					<?php if ( ! empty( $lr_kontakt['telefon'] ) ) : ?>
						<p><a href="<?= esc_attr( lr_tel_href( $lr_kontakt['telefon'] ) ); ?>"><?= esc_html( $lr_kontakt['telefon'] ); ?></a></p>
					<?php endif; ?>
					<?php if ( ! empty( $lr_kontakt['email'] ) ) : ?>
						<p><a href="mailto:<?= esc_attr( antispambot( $lr_kontakt['email'] ) ); ?>"><?= esc_html( antispambot( $lr_kontakt['email'] ) ); ?></a></p>
					<?php endif; ?>
					<?php if ( $lr_social ) : ?>
						<ul class="footer__social">
							<?php foreach ( $lr_social as $lr_channel ) : ?>
								<?php if ( ! empty( $lr_channel['url'] ) ) : ?>
									<li><a href="<?= esc_url( $lr_channel['url'] ); ?>" target="_blank" rel="noopener"><?= esc_html( lr_social_label( (string) $lr_channel['plattform'] ) ); ?></a></li>
								<?php endif; ?>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>

				<?php if ( ! empty( $lr_spenden['iban'] ) || ! empty( $lr_spenden['button']['url'] ) ) : ?>
					<div class="footer__donate">
						<p class="footer__title"><?php esc_html_e( 'Spenden', 'loveride' ); ?></p>
						<?php if ( ! empty( $lr_spenden['iban'] ) ) : ?>
							<p><?php esc_html_e( 'IBAN', 'loveride' ); ?> <?= esc_html( $lr_spenden['iban'] ); ?></p>
						<?php endif; ?>
						<?php if ( ! empty( $lr_spenden['hinweis'] ) ) : ?>
							<p><?= esc_html( $lr_spenden['hinweis'] ); ?></p>
						<?php endif; ?>
						<?php if ( ! empty( $lr_spenden['button']['url'] ) ) : ?>
							<a class="btn--secondary btn--small" <?= lr_link_attrs( $lr_spenden['button'] ); ?>><?= esc_html( $lr_spenden['button']['title'] ); ?></a>
						<?php endif; ?>
					</div>
				<?php endif; ?>

			</div>

			<?php if ( $lr_columns ) : ?>
				<nav class="footer__nav" aria-label="<?php esc_attr_e( 'Footer-Navigation', 'loveride' ); ?>">
					<?php foreach ( $lr_columns as $lr_column ) : ?>
						<div class="footer__col">
							<a class="footer__title" <?= lr_link_attrs( $lr_column ); ?>><?= esc_html( $lr_column['title'] ); ?></a>
							<?php if ( $lr_column['children'] ) : ?>
								<ul>
									<?php foreach ( $lr_column['children'] as $lr_item ) : ?>
										<li><a <?= lr_link_attrs( $lr_item ); ?>><?= esc_html( $lr_item['title'] ); ?></a></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</nav>
			<?php endif; ?>

			<div class="footer__logo">
				<?= lr_logo( true, array( 'loading' => 'lazy' ) ); ?>
			</div>

		</div>

		<div class="footer__bottom">
			<p>&copy; <?= esc_html( wp_date( 'Y' ) ); ?> <?= esc_html( lr_get_option( 'copyright', get_bloginfo( 'name' ) ) ); ?></p>
			<?php if ( $lr_legal ) : ?>
				<nav aria-label="<?php esc_attr_e( 'Rechtliches', 'loveride' ); ?>">
					<ul class="footer__legal">
						<?php foreach ( $lr_legal as $lr_item ) : ?>
							<li><a <?= lr_link_attrs( array( 'url' => $lr_item->url, 'target' => $lr_item->target ) ); ?>><?= esc_html( $lr_item->title ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</nav>
			<?php endif; ?>
		</div>

	</div>
</footer>

<?php wp_footer(); ?>

</body>
</html>
