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
 * Panel admina DC Interface.
 */
class DCWT_Admin {

	/**
	 * @var DCWT_Admin|null
	 */
	private static $instance = null;

	/**
	 * @return DCWT_Admin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_init', array( $this, 'handle_actions' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		add_filter( 'admin_body_class', array( $this, 'body_class' ) );
	}

	/**
	 * @param string $classes .
	 * @return string
	 */
	public function body_class( $classes ) {
		$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( 'dcwt-settings' === $page ) {
			$classes .= ' dcwt-admin';
		}
		return $classes;
	}

	/**
	 * @return void
	 */
	public function register_menu() {
		add_menu_page(
			DCWT_I18n::t( 'plugin_title' ),
			DCWT_I18n::t( 'plugin_menu' ),
			'manage_woocommerce',
			'dcwt-settings',
			array( $this, 'render' ),
			'dashicons-backup',
			59
		);
	}

	/**
	 * @return void
	 */
	public function handle_actions() {
		if ( ! is_admin() || ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		if ( ! empty( $_POST['dcwt_save'] ) ) {
			check_admin_referer( 'dcwt_save' );
			$id = isset( $_POST['timeline_id'] ) ? absint( $_POST['timeline_id'] ) : 0;
			if ( ! $id || ! DCWT_Settings::exists( $id ) ) {
				wp_safe_redirect( admin_url( 'admin.php?page=dcwt-settings' ) );
				exit;
			}
			DCWT_Settings::save( $id, $this->sanitize( wp_unslash( $_POST ) ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			wp_safe_redirect( admin_url( 'admin.php?page=dcwt-settings&timeline=' . $id . '&updated=1' ) );
			exit;
		}

		if ( ! empty( $_POST['dcwt_create'] ) ) {
			check_admin_referer( 'dcwt_instances' );
			$id = DCWT_Settings::create();
			wp_safe_redirect( admin_url( 'admin.php?page=dcwt-settings&timeline=' . $id ) );
			exit;
		}

		if ( ! empty( $_POST['dcwt_duplicate'] ) ) {
			check_admin_referer( 'dcwt_instances' );
			$src = isset( $_POST['timeline_id'] ) ? absint( $_POST['timeline_id'] ) : 0;
			$id  = $src ? DCWT_Settings::duplicate( $src ) : 0;
			$to  = $id ? admin_url( 'admin.php?page=dcwt-settings&timeline=' . $id ) : admin_url( 'admin.php?page=dcwt-settings' );
			wp_safe_redirect( $to );
			exit;
		}

		if ( ! empty( $_POST['dcwt_delete'] ) ) {
			check_admin_referer( 'dcwt_instances' );
			$id = isset( $_POST['timeline_id'] ) ? absint( $_POST['timeline_id'] ) : 0;
			if ( $id ) {
				DCWT_Settings::delete( $id );
			}
			wp_safe_redirect( admin_url( 'admin.php?page=dcwt-settings&deleted=1' ) );
			exit;
		}
	}

	/**
	 * @param array<string,mixed> $post .
	 * @return array<string,mixed>
	 */
	private function sanitize( $post ) {
		$defaults = DCWT_Settings::defaults();
		$source   = isset( $post['source'] ) ? sanitize_key( $post['source'] ) : 'selected';
		if ( ! in_array( $source, array( 'selected', 'category', 'sale' ), true ) ) {
			$source = 'selected';
		}

		$format  = isset( $post['date_format'] ) ? (string) $post['date_format'] : 'F Y';
		$allowed = array_keys( DCWT_Settings::date_formats() );
		if ( ! in_array( $format, $allowed, true ) ) {
			$format = 'F Y';
		}
		$group = DCWT_Settings::group_from_format( $format );

		$ratio = isset( $post['thumb_ratio'] ) ? sanitize_key( $post['thumb_ratio'] ) : '1-1';
		if ( ! array_key_exists( $ratio, DCWT_Settings::ratios() ) ) {
			$ratio = '1-1';
		}

		$order = isset( $post['date_order'] ) ? sanitize_key( $post['date_order'] ) : 'desc';
		if ( ! in_array( $order, array( 'asc', 'desc' ), true ) ) {
			$order = 'desc';
		}

		$out = array(
			'title'                => isset( $post['title'] ) ? sanitize_text_field( $post['title'] ) : $defaults['title'],
			'show_heading'         => empty( $post['show_heading'] ) ? 0 : 1,
			'source'               => $source,
			'category_id'          => isset( $post['category_id'] ) ? absint( $post['category_id'] ) : 0,
			'limit'                => isset( $post['limit'] ) ? max( 1, absint( $post['limit'] ) ) : 24,
			'date_group'           => $group,
			'date_format'          => $format,
			'date_order'           => $order,
			'thumb_ratio'          => $ratio,
			'bg_color'             => $this->hex( isset( $post['bg_color'] ) ? $post['bg_color'] : $defaults['bg_color'] ),
			'line_color'           => $this->hex( isset( $post['line_color'] ) ? $post['line_color'] : $defaults['line_color'] ),
			'node_color'           => $this->hex( isset( $post['node_color'] ) ? $post['node_color'] : $defaults['node_color'] ),
			'container_width'      => $this->sanitize_container_width(
				isset( $post['container_width'] ) ? $post['container_width'] : $defaults['container_width'],
				isset( $post['container_width_unit'] ) ? $post['container_width_unit'] : $defaults['container_width_unit']
			),
			'container_width_unit' => $this->sanitize_container_unit( isset( $post['container_width_unit'] ) ? $post['container_width_unit'] : $defaults['container_width_unit'] ),
			'line_width'           => $this->clamp_int( isset( $post['line_width'] ) ? $post['line_width'] : $defaults['line_width'], 1, 20 ),
			'hline_width'          => $this->clamp_int( isset( $post['hline_width'] ) ? $post['hline_width'] : $defaults['hline_width'], 1, 20 ),
			'node_size'            => $this->clamp_int( isset( $post['node_size'] ) ? $post['node_size'] : $defaults['node_size'], 6, 40 ),
			'thumb_width'          => $this->clamp_int( isset( $post['thumb_width'] ) ? $post['thumb_width'] : $defaults['thumb_width'], 40, 200 ),
			'thumb_border'         => empty( $post['thumb_border'] ) ? 0 : 1,
			'thumb_border_color'   => $this->hex( isset( $post['thumb_border_color'] ) ? $post['thumb_border_color'] : $defaults['thumb_border_color'] ),
			'thumb_border_width'   => $this->clamp_int( isset( $post['thumb_border_width'] ) ? $post['thumb_border_width'] : $defaults['thumb_border_width'], 0, 20 ),
			'products'             => $this->sanitize_products( isset( $post['products'] ) ? $post['products'] : array() ),
			'typo'                 => array(),
		);

		$posted_typo = isset( $post['typo'] ) && is_array( $post['typo'] ) ? $post['typo'] : array();
		foreach ( DCWT_Settings::typo_keys() as $key => $_label ) {
			$base                = $defaults['typo'][ $key ];
			$row                 = isset( $posted_typo[ $key ] ) && is_array( $posted_typo[ $key ] ) ? $posted_typo[ $key ] : array();
			$kind                = DCWT_Settings::typo_kind( $key );
			$out['typo'][ $key ] = $this->sanitize_typo( $row, $base, $kind );
		}

		return $out;
	}

	/**
	 * @param mixed $rows .
	 * @return array<int,array<string,mixed>>
	 */
	private function sanitize_products( $rows ) {
		if ( ! is_array( $rows ) ) {
			return array();
		}
		$out = array();
		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) || empty( $row['id'] ) ) {
				continue;
			}
			$date = isset( $row['date'] ) ? sanitize_text_field( $row['date'] ) : '';
			if ( $date && ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) {
				$date = '';
			}
			$out[] = array(
				'id'         => absint( $row['id'] ),
				'date'       => $date,
				'date_label' => isset( $row['date_label'] ) ? sanitize_text_field( $row['date_label'] ) : '',
			);
		}
		return $out;
	}

	/**
	 * @param array<string,mixed> $row  .
	 * @param array<string,mixed> $base .
	 * @param string              $kind .
	 * @return array<string,mixed>
	 */
	private function sanitize_typo( $row, $base, $kind ) {
		$units  = array( 'px', 'rem', 'vw', 'vh', '%' );
		$aligns = array( 'left', 'center', 'right' );
		$fonts  = array_keys( DCWT_Settings::fonts() );

		$family = isset( $row['family'] ) ? $row['family'] : $base['family'];
		if ( ! in_array( $family, $fonts, true ) ) {
			$family = $base['family'];
		}

		$unit = isset( $row['size_unit'] ) ? $row['size_unit'] : $base['size_unit'];
		if ( ! in_array( $unit, $units, true ) ) {
			$unit = 'px';
		}

		$align = isset( $row['align'] ) ? $row['align'] : $base['align'];
		if ( ! in_array( $align, $aligns, true ) ) {
			$align = 'left';
		}

		$weight = isset( $row['weight'] ) ? preg_replace( '/[^0-9]/', '', (string) $row['weight'] ) : $base['weight'];
		if ( ! in_array( $weight, array( '300', '400', '500', '600', '700', '800' ), true ) ) {
			$weight = '400';
		}

		$out = array(
			'family'    => $family,
			'size'      => isset( $row['size'] ) ? (float) $row['size'] : (float) $base['size'],
			'size_unit' => $unit,
			'weight'    => $weight,
			'uppercase' => empty( $row['uppercase'] ) ? 0 : 1,
			'color'     => $this->hex( isset( $row['color'] ) ? $row['color'] : $base['color'] ),
			'align'     => $align,
		);

		if ( 'button' === $kind ) {
			$out['bg']          = $this->hex( isset( $row['bg'] ) ? $row['bg'] : ( $base['bg'] ?? '#1fa28c' ) );
			$out['hover_color'] = $this->hex( isset( $row['hover_color'] ) ? $row['hover_color'] : ( $base['hover_color'] ?? '#ffffff' ) );
			$out['hover_bg']    = $this->hex( isset( $row['hover_bg'] ) ? $row['hover_bg'] : ( $base['hover_bg'] ?? '#178675' ) );
		}
		if ( 'link' === $kind ) {
			$out['hover_color'] = $this->hex( isset( $row['hover_color'] ) ? $row['hover_color'] : ( $base['hover_color'] ?? '#1fa28c' ) );
		}

		return $out;
	}

	/**
	 * @param string $value .
	 * @return string
	 */
	private function hex( $value ) {
		$color = sanitize_hex_color( $value );
		return $color ? $color : '#111111';
	}

	/**
	 * @param mixed $unit .
	 * @return string
	 */
	private function sanitize_container_unit( $unit ) {
		return 'px' === $unit ? 'px' : '%';
	}

	/**
	 * @param mixed $value .
	 * @param mixed $unit  .
	 * @return int
	 */
	private function sanitize_container_width( $value, $unit ) {
		$unit  = $this->sanitize_container_unit( $unit );
		$value = (int) round( (float) $value );
		$min   = 1;
		$max   = 'px' === $unit ? 4000 : 100;
		return $this->clamp_int( $value, $min, $max );
	}

	/**
	 * @param mixed $value .
	 * @param int   $min   .
	 * @param int   $max   .
	 * @return int
	 */
	private function clamp_int( $value, $min, $max ) {
		$value = (int) round( (float) $value );
		if ( $value < $min ) {
			return $min;
		}
		if ( $value > $max ) {
			return $max;
		}
		return $value;
	}

	/**
	 * @param string $hook .
	 * @return void
	 */
	public function enqueue( $hook ) {
		if ( false === strpos( $hook, 'dcwt-settings' ) ) {
			return;
		}

		wp_enqueue_style( 'dashicons' );
		wp_enqueue_script( 'jquery-ui-sortable' );

		wp_enqueue_style(
			'dcwt-font-awesome',
			DCWT_URL . 'admin/vendor/font-awesome/css/font-awesome.min.css',
			array(),
			'4.7.0'
		);

		$iface = DCWT_PATH . 'admin/css/dc-interface/dc-interface.css';
		wp_enqueue_style(
			'dcwt-interface',
			DCWT_URL . 'admin/css/dc-interface/dc-interface.css',
			array( 'dcwt-font-awesome' ),
			file_exists( $iface ) ? (string) filemtime( $iface ) : DCWT_VERSION
		);

		wp_enqueue_style(
			'dcwt-admin',
			DCWT_URL . 'admin/css/admin.css',
			array( 'dcwt-interface' ),
			file_exists( DCWT_PATH . 'admin/css/admin.css' ) ? (string) filemtime( DCWT_PATH . 'admin/css/admin.css' ) : DCWT_VERSION
		);

		wp_enqueue_script(
			'dcwt-colorpicker',
			DCWT_URL . 'admin/js/dc-interface/dc-colorpicker.js',
			array(),
			file_exists( DCWT_PATH . 'admin/js/dc-interface/dc-colorpicker.js' ) ? (string) filemtime( DCWT_PATH . 'admin/js/dc-interface/dc-colorpicker.js' ) : DCWT_VERSION,
			true
		);
		wp_enqueue_script(
			'dcwt-dimension',
			DCWT_URL . 'admin/js/dc-interface/dc-dimension.js',
			array(),
			file_exists( DCWT_PATH . 'admin/js/dc-interface/dc-dimension.js' ) ? (string) filemtime( DCWT_PATH . 'admin/js/dc-interface/dc-dimension.js' ) : DCWT_VERSION,
			true
		);
		wp_enqueue_script(
			'dcwt-interface',
			DCWT_URL . 'admin/js/dc-interface/dc-interface.js',
			array( 'dcwt-colorpicker', 'dcwt-dimension' ),
			file_exists( DCWT_PATH . 'admin/js/dc-interface/dc-interface.js' ) ? (string) filemtime( DCWT_PATH . 'admin/js/dc-interface/dc-interface.js' ) : DCWT_VERSION,
			true
		);
		wp_enqueue_script(
			'dcwt-admin',
			DCWT_URL . 'admin/js/admin.js',
			array( 'jquery', 'jquery-ui-sortable', 'dcwt-interface' ),
			file_exists( DCWT_PATH . 'admin/js/admin.js' ) ? (string) filemtime( DCWT_PATH . 'admin/js/admin.js' ) : DCWT_VERSION,
			true
		);

		wp_localize_script(
			'dcwt-admin',
			'dcwtAdmin',
			array(
				'ajax'  => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'dcwt_admin' ),
				'i18n'  => array(
					'search'     => DCWT_I18n::t( 'search_products' ),
					'add'        => DCWT_I18n::t( 'add_product' ),
					'remove'     => DCWT_I18n::t( 'remove' ),
					'date'       => DCWT_I18n::t( 'product_date' ),
					'dateLabel'  => DCWT_I18n::t( 'product_date_label' ),
					'noProducts' => DCWT_I18n::t( 'no_products' ),
					'copied'     => DCWT_I18n::t( 'copied' ),
					'confirmDel' => DCWT_I18n::t( 'confirm_delete' ),
				),
			)
		);
	}

	/**
	 * @return void
	 */
	public function render() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$timeline_id = isset( $_GET['timeline'] ) ? absint( wp_unslash( $_GET['timeline'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( $timeline_id && DCWT_Settings::exists( $timeline_id ) ) {
			$settings = DCWT_Settings::get( $timeline_id );
			include DCWT_PATH . 'admin/views/settings.php';
			return;
		}

		$timelines = DCWT_Settings::all();
		include DCWT_PATH . 'admin/views/list.php';
	}
}
