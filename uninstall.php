<?php
/**
 * Design Cart Woo Timelines
 *
 * Author: Paweł Nosko https://www.designcart.pl/pawel-nosko.html
 * Company: Design Cart https://www.designcart.pl/
 *
 * @package Design_Cart_Woo_Timelines
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'dcwt_timelines' );
delete_option( 'dcwt_timeline_next_id' );
