<?php
/**
 * Design Cart Woo Timelines
 *
 * Author: Paweł Nosko https://www.designcart.pl/pawel-nosko.html
 * Company: Design Cart https://www.designcart.pl/
 *
 * @package Design_Cart_Woo_Timelines
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bootstrap pluginu.
 */
class DCWT_Plugin {

	/**
	 * @var DCWT_Plugin|null
	 */
	private static $instance = null;

	/**
	 * @return DCWT_Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'plugins_loaded', array( $this, 'boot' ) );
		add_action( 'before_woocommerce_init', array( $this, 'declare_hpos' ) );
	}

	/**
	 * @return void
	 */
	public function declare_hpos() {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', DCWT_FILE, true );
		}
	}

	/**
	 * @return void
	 */
	public function boot() {
		if ( ! $this->woocommerce_ready() ) {
			add_action( 'admin_notices', array( $this, 'woocommerce_notice' ) );
			return;
		}

		DCWT_Settings::migrate();
		DCWT_Ajax::instance();
		DCWT_Frontend::instance();

		if ( is_admin() ) {
			DCWT_Admin::instance();
		}
	}

	/**
	 * @return bool
	 */
	private function woocommerce_ready() {
		return class_exists( 'WooCommerce' );
	}

	/**
	 * @return void
	 */
	public function woocommerce_notice() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		echo '<div class="notice notice-error"><p>';
		echo esc_html( DCWT_I18n::t( 'wc_required' ) );
		echo '</p></div>';
	}

	/**
	 * @return void
	 */
	public static function activate() {
		DCWT_Settings::migrate();
	}

	/**
	 * @return void
	 */
	public static function deactivate() {
	}
}
