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
 * Shortcode i zasoby frontu.
 */
class DCWT_Frontend {

	/**
	 * @var DCWT_Frontend|null
	 */
	private static $instance = null;

	/**
	 * @var bool
	 */
	private $needs_assets = false;

	/**
	 * @var bool
	 */
	private $enqueued = false;

	/**
	 * @var array<int,bool>
	 */
	private $timeline_ids = array();

	/**
	 * @return DCWT_Frontend
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_shortcode( 'design_cart_woo_timelines', array( $this, 'shortcode' ) );
		add_shortcode( 'design_cart_woo_timeline', array( $this, 'shortcode' ) );
		add_shortcode( 'dcwt_timeline', array( $this, 'shortcode' ) );
		add_filter( 'the_posts', array( $this, 'detect_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'maybe_enqueue' ), 20 );
		add_action( 'wp_footer', array( $this, 'maybe_print_assets' ), 5 );
	}

	/**
	 * @return void
	 */
	public function register_assets() {
		wp_register_style(
			'dcwt-front',
			DCWT_URL . 'public/css/frontend.css',
			array(),
			file_exists( DCWT_PATH . 'public/css/frontend.css' ) ? (string) filemtime( DCWT_PATH . 'public/css/frontend.css' ) : DCWT_VERSION
		);
		wp_register_script(
			'dcwt-front',
			DCWT_URL . 'public/js/frontend.js',
			array(),
			file_exists( DCWT_PATH . 'public/js/frontend.js' ) ? (string) filemtime( DCWT_PATH . 'public/js/frontend.js' ) : DCWT_VERSION,
			true
		);
	}

	/**
	 * @param array<string,mixed> $atts .
	 * @return string
	 */
	public function shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'id' => 0,
			),
			$atts,
			'design_cart_woo_timeline'
		);

		$timeline_id = absint( $atts['id'] );
		if ( ! $timeline_id || ! DCWT_Settings::exists( $timeline_id ) ) {
			$timeline_id = DCWT_Settings::first_id();
		}
		if ( ! $timeline_id ) {
			return '';
		}

		$settings = DCWT_Settings::get( $timeline_id );
		$groups   = DCWT_Products::grouped( $settings );
		if ( empty( $groups ) ) {
			return '';
		}

		$this->needs_assets                 = true;
		$this->timeline_ids[ $timeline_id ] = true;

		ob_start();
		include DCWT_PATH . 'public/views/timeline.php';
		return (string) ob_get_clean();
	}

	/**
	 * @param array<int,WP_Post> $posts .
	 * @return array<int,WP_Post>
	 */
	public function detect_shortcode( $posts ) {
		if ( $this->needs_assets || empty( $posts ) || is_admin() ) {
			return $posts;
		}
		foreach ( $posts as $post ) {
			if ( ! isset( $post->post_content ) ) {
				continue;
			}
			if (
				has_shortcode( $post->post_content, 'design_cart_woo_timelines' )
				|| has_shortcode( $post->post_content, 'design_cart_woo_timeline' )
				|| has_shortcode( $post->post_content, 'dcwt_timeline' )
			) {
				$this->needs_assets = true;
				$this->collect_ids_from_content( $post->post_content );
			}
		}
		return $posts;
	}

	/**
	 * @param string $content .
	 * @return void
	 */
	private function collect_ids_from_content( $content ) {
		$regex = get_shortcode_regex( array( 'design_cart_woo_timelines', 'design_cart_woo_timeline', 'dcwt_timeline' ) );
		if ( ! preg_match_all( '/' . $regex . '/s', $content, $matches, PREG_SET_ORDER ) ) {
			return;
		}
		foreach ( $matches as $match ) {
			$atts = shortcode_parse_atts( $match[3] );
			$id   = ( is_array( $atts ) && ! empty( $atts['id'] ) ) ? absint( $atts['id'] ) : DCWT_Settings::first_id();
			if ( $id ) {
				$this->timeline_ids[ $id ] = true;
			}
		}
	}

	/**
	 * @return void
	 */
	public function maybe_enqueue() {
		if ( $this->needs_assets ) {
			$this->enqueue();
		}
	}

	/**
	 * @return void
	 */
	public function maybe_print_assets() {
		if ( ! $this->needs_assets ) {
			return;
		}
		$this->enqueue();
	}

	/**
	 * @return void
	 */
	private function enqueue() {
		if ( $this->enqueued ) {
			return;
		}
		$this->enqueued = true;

		$ids = array_keys( $this->timeline_ids );
		if ( ! $ids ) {
			$first = DCWT_Settings::first_id();
			if ( $first ) {
				$ids = array( $first );
			}
		}

		$fonts = array();
		$css   = '';
		foreach ( $ids as $id ) {
			$settings = DCWT_Settings::get( $id );
			$css     .= $this->css_vars( $settings, (int) $id );
			$fonts    = array_merge( $fonts, $this->used_google_fonts( $settings ) );
		}

		$font_url = $this->google_fonts_url( $fonts );
		if ( $font_url ) {
			wp_enqueue_style( 'dcwt-fonts', $font_url, array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		}
		wp_enqueue_style( 'dcwt-front' );

		if ( $css ) {
			wp_add_inline_style( 'dcwt-front', $css );
		}
		wp_enqueue_script( 'dcwt-front' );
	}

	/**
	 * @param array<string,mixed> $settings .
	 * @return array<int,string>
	 */
	private function used_google_fonts( $settings ) {
		$known = DCWT_Settings::google_fonts();
		$out   = array();
		foreach ( (array) $settings['typo'] as $typo ) {
			if ( ! is_array( $typo ) || empty( $typo['family'] ) ) {
				continue;
			}
			if ( in_array( $typo['family'], $known, true ) ) {
				$out[] = $typo['family'];
			}
		}
		return $out;
	}

	/**
	 * @param array<int,string> $families .
	 * @return string
	 */
	private function google_fonts_url( $families ) {
		$families = array_values( array_unique( array_filter( $families ) ) );
		if ( ! $families ) {
			return '';
		}
		$parts = array();
		foreach ( $families as $family ) {
			$parts[] = 'family=' . rawurlencode( $family ) . ':wght@300;400;500;600;700;800';
		}
		return 'https://fonts.googleapis.com/css2?' . implode( '&', $parts ) . '&display=swap';
	}

	/**
	 * @param array<string,mixed> $settings    .
	 * @param int                 $timeline_id .
	 * @return string
	 */
	private function css_vars( $settings, $timeline_id ) {
		$id    = absint( $timeline_id );
		$sel   = '.dcwt[data-dcwt-id="' . $id . '"]';
		$lines = array( $sel . '{' );

		$width = isset( $settings['container_width'] ) ? (float) $settings['container_width'] : 1100;
		$unit  = ( isset( $settings['container_width_unit'] ) && '%' === $settings['container_width_unit'] ) ? '%' : 'px';
		if ( $width < 1 ) {
			$width = 1;
		}
		$css_width = ( 'px' === $unit ) ? $width . 'px' : $width . '%';

		$lines[] = '--dcwt-bg:' . $this->css_color( isset( $settings['bg_color'] ) ? $settings['bg_color'] : '#ffffff' ) . ';';
		$lines[] = '--dcwt-line:' . $this->css_color( isset( $settings['line_color'] ) ? $settings['line_color'] : '#1fa28c' ) . ';';
		$lines[] = '--dcwt-node:' . $this->css_color( isset( $settings['node_color'] ) ? $settings['node_color'] : '#1fa28c' ) . ';';
		$lines[] = '--dcwt-container-width:' . $css_width . ';';
		$lines[] = '--dcwt-line-w:' . (int) ( isset( $settings['line_width'] ) ? $settings['line_width'] : 2 ) . 'px;';
		$lines[] = '--dcwt-hline-w:' . (int) ( isset( $settings['hline_width'] ) ? $settings['hline_width'] : 2 ) . 'px;';
		$lines[] = '--dcwt-node-size:' . (int) ( isset( $settings['node_size'] ) ? $settings['node_size'] : 14 ) . 'px;';
		$lines[] = '--dcwt-thumb-w:' . (int) ( isset( $settings['thumb_width'] ) ? $settings['thumb_width'] : 80 ) . 'px;';
		$border_on = ! empty( $settings['thumb_border'] );
		$border_w  = $border_on ? (int) ( isset( $settings['thumb_border_width'] ) ? $settings['thumb_border_width'] : 1 ) : 0;
		$lines[]   = '--dcwt-thumb-border-w:' . $border_w . 'px;';
		$lines[]   = '--dcwt-thumb-border:' . $this->css_color( isset( $settings['thumb_border_color'] ) ? $settings['thumb_border_color'] : '#1fa28c' ) . ';';

		$allowed_typo = DCWT_Settings::typo_keys();
		foreach ( (array) $settings['typo'] as $key => $typo ) {
			if ( ! isset( $allowed_typo[ $key ] ) || ! is_array( $typo ) ) {
				continue;
			}
			$p      = '--dcwt-' . str_replace( '_', '-', $key );
			$family = $this->css_family( isset( $typo['family'] ) ? $typo['family'] : 'inherit' );
			$lines[] = $p . '-family:' . $family . ';';
			$lines[] = $p . '-size:' . (float) $typo['size'] . ( isset( $typo['size_unit'] ) ? $typo['size_unit'] : 'px' ) . ';';
			$lines[] = $p . '-weight:' . ( isset( $typo['weight'] ) ? $typo['weight'] : '400' ) . ';';
			$lines[] = $p . '-transform:' . ( ! empty( $typo['uppercase'] ) ? 'uppercase' : 'none' ) . ';';
			$lines[] = $p . '-color:' . $this->css_color( $typo['color'] ) . ';';
			$lines[] = $p . '-align:' . ( isset( $typo['align'] ) ? $typo['align'] : 'left' ) . ';';
			if ( isset( $typo['bg'] ) ) {
				$lines[] = $p . '-bg:' . $this->css_color( $typo['bg'] ) . ';';
			}
			if ( isset( $typo['hover_color'] ) ) {
				$lines[] = $p . '-hover-color:' . $this->css_color( $typo['hover_color'] ) . ';';
			}
			if ( isset( $typo['hover_bg'] ) ) {
				$lines[] = $p . '-hover-bg:' . $this->css_color( $typo['hover_bg'] ) . ';';
			}
		}

		$lines[] = '}';
		return implode( '', $lines );
	}

	/**
	 * @param string $family .
	 * @return string
	 */
	private function css_family( $family ) {
		if ( 'inherit' === $family || false !== strpos( $family, ',' ) ) {
			return $family;
		}
		return '"' . $family . '", serif';
	}

	/**
	 * @param string $color .
	 * @return string
	 */
	private function css_color( $color ) {
		$hex = sanitize_hex_color( $color );
		return $hex ? $hex : '#111111';
	}
}
