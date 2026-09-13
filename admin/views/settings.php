<?php
/**
 * Design Cart Woo Timelines
 *
 * Author: Paweł Nosko https://www.designcart.pl/pawel-nosko.html
 * Company: Design Cart https://www.designcart.pl/
 *
 * @package Design_Cart_Woo_Timelines
 *
 * @var array<string,mixed> $settings
 * @var int                 $timeline_id
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals -- View included from plugin class.

$updated    = isset( $_GET['updated'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$categories = get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => false,
	)
);
$cat_opts   = array( '0' => DCWT_I18n::t( 'choose_category' ) );
if ( ! is_wp_error( $categories ) ) {
	foreach ( $categories as $term ) {
		$cat_opts[ (string) $term->term_id ] = $term->name;
	}
}
$typo  = isset( $settings['typo'] ) ? $settings['typo'] : array();
$icons = array(
	'heading'      => 'fa-header',
	'date'         => 'fa-calendar',
	'product_name' => 'fa-link',
);
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
						<h1 class="dc-hero__title"><?php echo esc_html( $settings['title'] ? $settings['title'] : DCWT_I18n::t( 'plugin_title' ) ); ?></h1>
					</div>
				</div>
				<div class="dc-hero__actions">
					<button type="submit" form="dcwt-form" class="dc-btn dc-btn--light"><i class="fa fa-save"></i> <?php echo esc_html( DCWT_I18n::t( 'save' ) ); ?></button>
					<a class="dc-btn dc-btn--ghost" href="<?php echo esc_url( admin_url( 'admin.php?page=dcwt-settings' ) ); ?>"><i class="fa fa-arrow-left"></i> <?php echo esc_html( DCWT_I18n::t( 'back' ) ); ?></a>
				</div>
			</div>
			<ul class="dc-hero__bc">
				<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=dcwt-settings' ) ); ?>"><?php echo esc_html( DCWT_I18n::t( 'timelines' ) ); ?></a></li>
				<li><?php echo esc_html( $settings['title'] ? $settings['title'] : DCWT_I18n::t( 'tab_general' ) ); ?></li>
			</ul>
		</div>
	</div>

	<div class="dc-page__wrap">
		<div class="dc-form-card">
			<div class="dc-interface" id="dcwtInterface">
				<?php if ( $updated ) : ?>
					<div class="notice notice-success is-dismissible"><p><?php echo esc_html( DCWT_I18n::t( 'saved' ) ); ?></p></div>
				<?php endif; ?>

				<form method="post" class="dc-form dc-form--full" id="dcwt-form">
					<?php wp_nonce_field( 'dcwt_save' ); ?>
					<input type="hidden" name="dcwt_save" value="1" />
					<input type="hidden" name="timeline_id" value="<?php echo esc_attr( (string) $timeline_id ); ?>" />

					<nav class="dc-nav dc-tabs" role="tablist">
						<button type="button" class="dc-nav__btn dc-tabs__btn dc-active" data-dc-tab="tab-general" role="tab" aria-selected="true"><i class="fa fa-cog"></i> <?php echo esc_html( DCWT_I18n::t( 'tab_general' ) ); ?></button>
						<button type="button" class="dc-nav__btn dc-tabs__btn" data-dc-tab="tab-products" role="tab"><i class="fa fa-cubes"></i> <?php echo esc_html( DCWT_I18n::t( 'tab_products' ) ); ?></button>
						<button type="button" class="dc-nav__btn dc-tabs__btn" data-dc-tab="tab-appearance" role="tab"><i class="fa fa-paint-brush"></i> <?php echo esc_html( DCWT_I18n::t( 'tab_appearance' ) ); ?></button>
					</nav>

					<div class="dc-form-card__body">

						<div id="tab-general" class="dc-tab-panel dc-active" role="tabpanel">
							<div class="dc-section-card dc-section">
								<div class="dc-section-card__head">
									<span class="dc-section-card__icon"><i class="fa fa-cog"></i></span>
									<div>
										<h3 class="dc-section-card__title"><?php echo esc_html( DCWT_I18n::t( 'general_title' ) ); ?></h3>
										<p class="dc-section-card__sub"><?php echo esc_html( DCWT_I18n::t( 'general_sub' ) ); ?></p>
									</div>
								</div>

								<?php DCWT_Fields::input( 'title', $settings['title'], DCWT_I18n::t( 'timeline_title' ) ); ?>
								<?php DCWT_Fields::toggle( 'show_heading', (int) $settings['show_heading'], DCWT_I18n::t( 'show_heading' ) ); ?>

								<?php
								DCWT_Fields::switch_group(
									'source',
									$settings['source'],
									DCWT_I18n::t( 'source' ),
									array(
										'selected' => DCWT_I18n::t( 'source_selected' ),
										'category' => DCWT_I18n::t( 'source_category' ),
										'sale'     => DCWT_I18n::t( 'source_sale' ),
									)
								);
								?>

								<div class="dc-row dc-row--2">
									<div data-source-category>
										<?php DCWT_Fields::select( 'category_id', (string) $settings['category_id'], DCWT_I18n::t( 'category' ), $cat_opts ); ?>
									</div>
									<?php DCWT_Fields::input( 'limit', (int) $settings['limit'], DCWT_I18n::t( 'limit' ), 'number' ); ?>
								</div>

								<div class="dc-row dc-row--2">
									<?php DCWT_Fields::select( 'date_format', $settings['date_format'], DCWT_I18n::t( 'date_format' ), DCWT_Settings::date_formats() ); ?>
									<?php
									DCWT_Fields::switch_group(
										'date_order',
										$settings['date_order'],
										DCWT_I18n::t( 'date_order' ),
										array(
											'desc' => DCWT_I18n::t( 'date_order_desc' ),
											'asc'  => DCWT_I18n::t( 'date_order_asc' ),
										)
									);
									?>
								</div>
								<p class="dc-hint"><?php echo esc_html( DCWT_I18n::t( 'date_format_hint' ) ); ?></p>

								<p class="dc-hint"><?php echo esc_html( DCWT_I18n::t( 'shortcode_hint' ) ); ?>
									<button type="button" class="dcwt-tile__code dcwt-tile__code--inline" data-copy-shortcode="<?php echo esc_attr( '[design_cart_woo_timelines id="' . (int) $timeline_id . '"]' ); ?>">
										<code>[design_cart_woo_timelines id="<?php echo esc_html( (string) (int) $timeline_id ); ?>"]</code>
									</button>
								</p>
							</div>
						</div>

						<div id="tab-products" class="dc-tab-panel" role="tabpanel">
							<div class="dc-section-card dc-section">
								<div class="dc-section-card__head">
									<span class="dc-section-card__icon"><i class="fa fa-cubes"></i></span>
									<div>
										<h3 class="dc-section-card__title"><?php echo esc_html( DCWT_I18n::t( 'products_title' ) ); ?></h3>
										<p class="dc-section-card__sub"><?php echo esc_html( DCWT_I18n::t( 'products_sub' ) ); ?></p>
									</div>
								</div>

								<div class="dcwt-search" data-product-search>
									<input class="dc-input" type="search" autocomplete="off" placeholder="<?php echo esc_attr( DCWT_I18n::t( 'search_products' ) ); ?>" data-search-input />
									<ul class="dcwt-search__results" data-search-results hidden></ul>
								</div>

								<div class="dcwt-source-bar">
									<button type="button" class="dc-btn dc-btn--secondary" data-load-source>
										<i class="fa fa-refresh"></i> <?php echo esc_html( DCWT_I18n::t( 'load_source' ) ); ?>
									</button>
								</div>

								<p class="dc-hint"><?php echo esc_html( DCWT_I18n::t( 'product_date_hint' ) ); ?></p>

								<ul class="dcwt-products" data-product-list>
									<?php
									foreach ( (array) $settings['products'] as $idx => $item ) {
										DCWT_Fields::product_row( $item, (int) $idx );
									}
									?>
								</ul>
								<p class="dc-hint" data-empty-hint <?php echo empty( $settings['products'] ) ? '' : 'hidden'; ?>>
									<?php echo esc_html( DCWT_I18n::t( 'no_products' ) ); ?>
								</p>
							</div>
						</div>

						<div id="tab-appearance" class="dc-tab-panel" role="tabpanel">
							<div class="dc-section-card dc-section">
								<div class="dc-section-card__head">
									<span class="dc-section-card__icon"><i class="fa fa-sliders"></i></span>
									<div>
										<h3 class="dc-section-card__title"><?php echo esc_html( DCWT_I18n::t( 'layout_title' ) ); ?></h3>
										<p class="dc-section-card__sub"><?php echo esc_html( DCWT_I18n::t( 'layout_sub' ) ); ?></p>
									</div>
								</div>

								<?php
								DCWT_Fields::switch_group(
									'thumb_ratio',
									$settings['thumb_ratio'],
									DCWT_I18n::t( 'thumb_ratio' ),
									DCWT_Settings::ratios()
								);
								?>

								<div class="dc-row dc-row--2">
									<?php DCWT_Fields::color( 'bg_color', $settings['bg_color'], DCWT_I18n::t( 'bg_color' ) ); ?>
									<?php DCWT_Fields::color( 'line_color', $settings['line_color'], DCWT_I18n::t( 'line_color' ) ); ?>
								</div>
								<div class="dc-row dc-row--2">
									<?php DCWT_Fields::color( 'node_color', $settings['node_color'], DCWT_I18n::t( 'node_color' ) ); ?>
								</div>

								<?php
								DCWT_Fields::dimension(
									array(
										'name'      => 'container_width',
										'unit_name' => 'container_width_unit',
										'label'     => DCWT_I18n::t( 'container_width' ),
										'value'     => $settings['container_width'],
										'unit'      => $settings['container_width_unit'],
										'min'       => 1,
										'max_px'    => 4000,
										'max_pct'   => 100,
									)
								);
								?>
								<p class="dc-hint"><?php echo esc_html( DCWT_I18n::t( 'container_width_hint' ) ); ?></p>

								<div class="dc-row dc-row--2">
									<?php
									DCWT_Fields::dimension(
										array(
											'name'       => 'line_width',
											'label'      => DCWT_I18n::t( 'line_width' ),
											'value'      => $settings['line_width'],
											'fixed_unit' => 'px',
											'min'        => 1,
											'max_px'     => 20,
										)
									);
									DCWT_Fields::dimension(
										array(
											'name'       => 'hline_width',
											'label'      => DCWT_I18n::t( 'hline_width' ),
											'value'      => $settings['hline_width'],
											'fixed_unit' => 'px',
											'min'        => 1,
											'max_px'     => 20,
										)
									);
									?>
								</div>
								<div class="dc-row dc-row--2">
									<?php
									DCWT_Fields::dimension(
										array(
											'name'       => 'node_size',
											'label'      => DCWT_I18n::t( 'node_size' ),
											'value'      => $settings['node_size'],
											'fixed_unit' => 'px',
											'min'        => 6,
											'max_px'     => 40,
										)
									);
									DCWT_Fields::dimension(
										array(
											'name'       => 'thumb_width',
											'label'      => DCWT_I18n::t( 'thumb_width' ),
											'value'      => $settings['thumb_width'],
											'fixed_unit' => 'px',
											'min'        => 40,
											'max_px'     => 200,
										)
									);
									?>
								</div>

								<?php DCWT_Fields::toggle( 'thumb_border', (int) ( isset( $settings['thumb_border'] ) ? $settings['thumb_border'] : 0 ), DCWT_I18n::t( 'thumb_border' ) ); ?>
								<div class="dc-row dc-row--2" data-thumb-border-fields>
									<?php DCWT_Fields::color( 'thumb_border_color', isset( $settings['thumb_border_color'] ) ? $settings['thumb_border_color'] : '#1fa28c', DCWT_I18n::t( 'thumb_border_color' ) ); ?>
									<?php
									DCWT_Fields::dimension(
										array(
											'name'       => 'thumb_border_width',
											'label'      => DCWT_I18n::t( 'thumb_border_width' ),
											'value'      => isset( $settings['thumb_border_width'] ) ? $settings['thumb_border_width'] : 1,
											'fixed_unit' => 'px',
											'min'        => 1,
											'max_px'     => 20,
										)
									);
									?>
								</div>
							</div>

							<div class="dc-section-card dc-section">
								<div class="dc-section-card__head">
									<span class="dc-section-card__icon"><i class="fa fa-paint-brush"></i></span>
									<div>
										<h3 class="dc-section-card__title"><?php echo esc_html( DCWT_I18n::t( 'appearance_title' ) ); ?></h3>
										<p class="dc-section-card__sub"><?php echo esc_html( DCWT_I18n::t( 'appearance_sub' ) ); ?></p>
									</div>
								</div>

								<div class="dc-accordion" data-dc-accordion-single>
									<?php
									$first = true;
									foreach ( DCWT_Settings::typo_keys() as $key => $label_key ) :
										$icon = isset( $icons[ $key ] ) ? $icons[ $key ] : 'fa-font';
										$kind = DCWT_Settings::typo_kind( $key );
										$row  = isset( $typo[ $key ] ) ? $typo[ $key ] : array();
										?>
										<div class="dc-accordion__item<?php echo $first ? ' dc-open' : ''; ?>">
											<button type="button" class="dc-accordion__trigger" data-dc-accordion-trigger aria-expanded="<?php echo $first ? 'true' : 'false'; ?>">
												<span class="dc-accordion__icon"><i class="fa <?php echo esc_attr( $icon ); ?>"></i></span>
												<span class="dc-accordion__title">
													<?php echo esc_html( DCWT_I18n::t( $label_key ) ); ?>
												</span>
												<svg class="dc-accordion__chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
											</button>
											<div class="dc-accordion__panel">
												<div class="dc-accordion__content">
													<div class="dc-accordion__body">
														<?php DCWT_Fields::typo( 'typo[' . $key . ']', $row, $kind ); ?>
													</div>
												</div>
											</div>
										</div>
										<?php
										$first = false;
									endforeach;
									?>
								</div>
							</div>
						</div>

						<div class="dc-actions">
							<button type="submit" class="dc-btn dc-btn--primary"><i class="fa fa-save"></i> <?php echo esc_html( DCWT_I18n::t( 'save' ) ); ?></button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
