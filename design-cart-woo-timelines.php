<?php
/**
 * Design Cart Woo Timelines
 *
 * Plugin Name:       Design Cart Woo Timelines
 * Plugin URI:        https://www.designcart.pl/
 * Description:       Timeline produktów WooCommerce — kategoria, zaznaczone lub promocyjne, z datą i miniaturami.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Requires Plugins:  woocommerce
 * Author:            Paweł Nosko
 * Author URI:        https://www.designcart.pl/pawel-nosko.html
 * License:           GPL-2.0-or-later
 * Text Domain:       design-cart-woo-timelines
 * Domain Path:       /languages
 * WC requires at least: 8.0
 * WC tested up to:   9.9
 *
 * Author: Paweł Nosko https://www.designcart.pl/pawel-nosko.html
 * Company: Design Cart https://www.designcart.pl/
 *
 * @package Design_Cart_Woo_Timelines
 */

defined( 'ABSPATH' ) || exit;

define( 'DCWT_VERSION', '1.0.0' );
define( 'DCWT_FILE', __FILE__ );
define( 'DCWT_PATH', plugin_dir_path( __FILE__ ) );
define( 'DCWT_URL', plugin_dir_url( __FILE__ ) );
define( 'DCWT_BASENAME', plugin_basename( __FILE__ ) );

require_once DCWT_PATH . 'includes/class-dcwt-i18n.php';
require_once DCWT_PATH . 'includes/class-dcwt-settings.php';
require_once DCWT_PATH . 'includes/class-dcwt-fields.php';
require_once DCWT_PATH . 'includes/class-dcwt-products.php';
require_once DCWT_PATH . 'includes/class-dcwt-ajax.php';
require_once DCWT_PATH . 'includes/class-dcwt-admin.php';
require_once DCWT_PATH . 'includes/class-dcwt-frontend.php';
require_once DCWT_PATH . 'includes/class-dcwt-plugin.php';

register_activation_hook( __FILE__, array( 'DCWT_Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'DCWT_Plugin', 'deactivate' ) );

DCWT_Plugin::instance();
