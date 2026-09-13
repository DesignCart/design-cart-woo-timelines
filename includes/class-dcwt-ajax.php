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
 * AJAX: wyszukiwanie i wczytywanie produktów w panelu.
 */
class DCWT_Ajax {

	/**
	 * @var DCWT_Ajax|null
	 */
	private static $instance = null;

	/**
	 * @return DCWT_Ajax
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'wp_ajax_dcwt_search_products', array( $this, 'search_products' ) );
		add_action( 'wp_ajax_dcwt_load_source', array( $this, 'load_source' ) );
	}

	/**
	 * @return void
	 */
	public function search_products() {
		check_ajax_referer( 'dcwt_admin', 'nonce' );
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error();
		}
		$term = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';
		wp_send_json_success( DCWT_Products::search( $term ) );
	}

	/**
	 * @return void
	 */
	public function load_source() {
		check_ajax_referer( 'dcwt_admin', 'nonce' );
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error();
		}
		$source      = isset( $_POST['source'] ) ? sanitize_key( wp_unslash( $_POST['source'] ) ) : 'selected';
		$category_id = isset( $_POST['category_id'] ) ? absint( $_POST['category_id'] ) : 0;
		$limit       = isset( $_POST['limit'] ) ? absint( $_POST['limit'] ) : 24;
		$existing    = isset( $_POST['products'] ) ? json_decode( wp_unslash( $_POST['products'] ), true ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( ! is_array( $existing ) ) {
			$existing = array();
		}

		$rows = DCWT_Products::listing_for_source( $source, $category_id, $limit, $existing );
		ob_start();
		foreach ( $rows as $idx => $row ) {
			DCWT_Fields::product_row( $row, (int) $idx );
		}
		$html = ob_get_clean();
		wp_send_json_success(
			array(
				'html'  => $html,
				'count' => count( $rows ),
			)
		);
	}
}
