<?php
/**
 * Design Cart Woo Timelines
 *
 * Author: Paweł Nosko https://www.designcart.pl/pawel-nosko.html
 * Company: Design Cart https://www.designcart.pl/
 *
 * @package Design_Cart_Woo_Timelines
 *
 * @var array<int,array<string,mixed>> $timelines
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals -- View included from plugin class.

$deleted = isset( $_GET['deleted'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
?>
<div class="dc-page dcwt-page">
	<div class="dc-hero">
		<div class="dc-hero__mesh"></div>
		<div class="dc-hero__orb dc-hero__orb--1"></div>
		<div class="dc-hero__orb dc-hero__orb--2"></div>
		<div class="dc-hero__inner">
			<div class="dc-hero__row">
				<div class="dc-hero__brand">
					<span class="dc-hero__icon"><i class="fa fa-clock-o"></i></span>
					<div>
						<p class="dc-hero__eyebrow"><?php echo esc_html( DCWT_I18n::t( 'eyebrow' ) ); ?></p>
						<h1 class="dc-hero__title"><?php echo esc_html( DCWT_I18n::t( 'plugin_title' ) ); ?></h1>
					</div>
				</div>
				<div class="dc-hero__actions">
					<form method="post">
						<?php wp_nonce_field( 'dcwt_instances' ); ?>
						<button type="submit" name="dcwt_create" value="1" class="dc-btn dc-btn--light">
							<i class="fa fa-plus"></i> <?php echo esc_html( DCWT_I18n::t( 'add_timeline' ) ); ?>
						</button>
					</form>
				</div>
			</div>
			<ul class="dc-hero__bc">
				<li><?php echo esc_html( DCWT_I18n::t( 'plugin_menu' ) ); ?></li>
				<li><?php echo esc_html( DCWT_I18n::t( 'timelines' ) ); ?></li>
			</ul>
		</div>
	</div>

	<div class="dc-page__wrap">
		<div class="dc-form-card">
			<div class="dc-interface">
				<div class="dc-form-card__body">
					<?php if ( $deleted ) : ?>
						<div class="notice notice-success is-dismissible"><p><?php echo esc_html( DCWT_I18n::t( 'deleted' ) ); ?></p></div>
					<?php endif; ?>

					<p class="dcwt-dashboard__lead"><?php echo esc_html( DCWT_I18n::t( 'timelines_sub' ) ); ?></p>

					<?php if ( empty( $timelines ) ) : ?>
						<p class="dc-hint"><?php echo esc_html( DCWT_I18n::t( 'no_timelines' ) ); ?></p>
					<?php else : ?>
						<div class="dcwt-tiles">
							<?php foreach ( $timelines as $id => $timeline ) : ?>
								<?php
								$count     = isset( $timeline['products'] ) && is_array( $timeline['products'] ) ? count( $timeline['products'] ) : 0;
								$shortcode = '[design_cart_woo_timelines id="' . (int) $id . '"]';
								?>
								<div class="dcwt-tile">
									<a class="dcwt-tile__main" href="<?php echo esc_url( admin_url( 'admin.php?page=dcwt-settings&timeline=' . (int) $id ) ); ?>">
										<span class="dcwt-tile__icon"><i class="fa fa-clock-o"></i></span>
										<span class="dcwt-tile__title"><?php echo esc_html( $timeline['title'] ? $timeline['title'] : sprintf( DCWT_I18n::t( 'timeline_n' ), $id ) ); ?></span>
										<span class="dcwt-tile__sub"><?php echo esc_html( sprintf( DCWT_I18n::t( 'products_count' ), $count ) ); ?></span>
									</a>
									<button type="button" class="dcwt-tile__code" data-copy-shortcode="<?php echo esc_attr( $shortcode ); ?>" title="<?php echo esc_attr( DCWT_I18n::t( 'copied' ) ); ?>">
										<code><?php echo esc_html( $shortcode ); ?></code>
									</button>
									<div class="dcwt-tile__actions">
										<a class="dc-btn dc-btn--secondary" href="<?php echo esc_url( admin_url( 'admin.php?page=dcwt-settings&timeline=' . (int) $id ) ); ?>">
											<i class="fa fa-pencil"></i> <?php echo esc_html( DCWT_I18n::t( 'edit_timeline' ) ); ?>
										</a>
										<form method="post">
											<?php wp_nonce_field( 'dcwt_instances' ); ?>
											<input type="hidden" name="timeline_id" value="<?php echo esc_attr( (string) $id ); ?>" />
											<button type="submit" name="dcwt_duplicate" value="1" class="dc-btn dc-btn--ghost">
												<i class="fa fa-copy"></i> <?php echo esc_html( DCWT_I18n::t( 'duplicate' ) ); ?>
											</button>
										</form>
										<form method="post" data-confirm-delete>
											<?php wp_nonce_field( 'dcwt_instances' ); ?>
											<input type="hidden" name="timeline_id" value="<?php echo esc_attr( (string) $id ); ?>" />
											<button type="submit" name="dcwt_delete" value="1" class="dc-btn dc-btn--ghost dcwt-tile__delete">
												<i class="fa fa-trash"></i> <?php echo esc_html( DCWT_I18n::t( 'remove' ) ); ?>
											</button>
										</form>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>
