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
 * Domyślne i zapisane ustawienia.
 */
class DCWT_Settings {

	const TIMELINES = 'dcwt_timelines';
	const NEXT_ID   = 'dcwt_timeline_next_id';

	/**
	 * @return array<string,mixed>
	 */
	public static function defaults() {
		$typo_text = array(
			'family'    => 'inherit',
			'size'      => 14,
			'size_unit' => 'px',
			'weight'    => '600',
			'uppercase' => 0,
			'color'     => '#111111',
			'align'     => 'center',
		);

		return array(
			'title'                => 'Timeline 1',
			'show_heading'         => 1,
			'source'               => 'selected',
			'category_id'          => 0,
			'limit'                => 24,
			'date_group'           => 'month',
			'date_format'          => 'F Y',
			'date_order'           => 'desc',
			'thumb_ratio'          => '1-1',
			'bg_color'             => '#ffffff',
			'line_color'           => '#1fa28c',
			'node_color'           => '#1fa28c',
			'container_width'      => 1100,
			'container_width_unit' => 'px',
			'line_width'           => 2,
			'hline_width'          => 2,
			'node_size'            => 14,
			'thumb_width'          => 110,
			'thumb_border'         => 0,
			'thumb_border_color'   => '#1fa28c',
			'thumb_border_width'   => 1,
			'products'             => array(),
			'typo'                 => array(
				'heading'      => array_merge(
					$typo_text,
					array(
						'family'    => 'Playfair Display',
						'size'      => 32,
						'weight'    => '400',
						'uppercase' => 0,
						'align'     => 'center',
					)
				),
				'date'         => array_merge(
					$typo_text,
					array(
						'size'      => 15,
						'weight'    => '700',
						'uppercase' => 1,
						'color'     => '#1fa28c',
						'align'     => 'center',
					)
				),
				'product_name' => array_merge(
					$typo_text,
					array(
						'size'        => 12,
						'weight'      => '500',
						'uppercase'   => 0,
						'color'       => '#111111',
						'hover_color' => '#1fa28c',
						'align'       => 'center',
					)
				),
			),
		);
	}

	/**
	 * @return array<string,string>
	 */
	public static function typo_keys() {
		return array(
			'heading'      => 'typo_heading',
			'date'         => 'typo_date',
			'product_name' => 'typo_product_name',
		);
	}

	/**
	 * @param string $key .
	 * @return string
	 */
	public static function typo_kind( $key ) {
		if ( 'product_name' === $key ) {
			return 'link';
		}
		return 'text';
	}

	/**
	 * @return array<string,string>
	 */
	public static function ratios() {
		return array(
			'1-1'    => '1:1',
			'3-2'    => '3:2',
			'2-3'    => '2:3',
			'4-3'    => '4:3',
			'3-4'    => '3:4',
			'16-9'   => '16:9',
			'4-5'    => '4:5',
			'5-4'    => '5:4',
			'circle' => DCWT_I18n::t( 'ratio_circle' ),
		);
	}

	/**
	 * @return array<string,string>
	 */
	public static function date_formats() {
		return array(
			'Y'      => DCWT_I18n::t( 'format_year' ),
			'F Y'    => DCWT_I18n::t( 'format_month_year' ),
			'm.Y'    => DCWT_I18n::t( 'format_month_num' ),
			'd.m.Y'  => DCWT_I18n::t( 'format_day' ),
			'j F Y'  => DCWT_I18n::t( 'format_day_long' ),
			'Y-m-d'  => 'Y-m-d',
		);
	}

	/**
	 * Grupowanie wynika z formatu daty: rok / rok+miesiąc / rok+miesiąc+dzień.
	 *
	 * @param string $format .
	 * @return string year|month|day
	 */
	public static function group_from_format( $format ) {
		if ( 'Y' === $format ) {
			return 'year';
		}
		if ( in_array( $format, array( 'd.m.Y', 'j F Y', 'Y-m-d' ), true ) ) {
			return 'day';
		}
		return 'month';
	}

	/**
	 * @return array<string,string>
	 */
	public static function fonts() {
		return array(
			'inherit'                      => DCWT_I18n::t( 'font_theme' ),
			'system-ui, sans-serif'        => 'System UI',
			'Georgia, serif'               => 'Georgia',
			'"Times New Roman", serif'     => 'Times New Roman',
			'Playfair Display'             => 'Playfair Display',
			'Cormorant Garamond'           => 'Cormorant Garamond',
			'Cinzel'                       => 'Cinzel',
			'Bodoni Moda'                  => 'Bodoni Moda',
			'Libre Baskerville'            => 'Libre Baskerville',
			'DM Serif Display'             => 'DM Serif Display',
			'Abril Fatface'                => 'Abril Fatface',
			'Oswald'                       => 'Oswald',
			'Montserrat'                   => 'Montserrat',
			'Inter'                        => 'Inter',
			'Poppins'                      => 'Poppins',
			'Roboto'                       => 'Roboto',
			'Lora'                         => 'Lora',
			'Raleway'                      => 'Raleway',
			'Nunito'                       => 'Nunito',
			'Bebas Neue'                   => 'Bebas Neue',
			'Archivo'                      => 'Archivo',
			'Great Vibes'                  => 'Great Vibes',
		);
	}

	/**
	 * @return array<int,string>
	 */
	public static function google_fonts() {
		return array(
			'Playfair Display',
			'Cormorant Garamond',
			'Cinzel',
			'Bodoni Moda',
			'Libre Baskerville',
			'DM Serif Display',
			'Abril Fatface',
			'Oswald',
			'Montserrat',
			'Inter',
			'Poppins',
			'Roboto',
			'Lora',
			'Raleway',
			'Nunito',
			'Bebas Neue',
			'Archivo',
			'Great Vibes',
		);
	}

	/**
	 * @return void
	 */
	public static function migrate() {
		$timelines = get_option( self::TIMELINES, false );
		if ( false !== $timelines && is_array( $timelines ) ) {
			return;
		}

		$first          = self::defaults();
		$first['title'] = self::defaults()['title'];

		update_option( self::TIMELINES, array( 1 => $first ), false );
		update_option( self::NEXT_ID, 2, false );
	}

	/**
	 * @return array<int,array<string,mixed>>
	 */
	public static function all() {
		self::migrate();
		$stored = get_option( self::TIMELINES, array() );
		if ( ! is_array( $stored ) ) {
			$stored = array();
		}
		$out = array();
		foreach ( $stored as $id => $row ) {
			$out[ (int) $id ] = self::merge( self::defaults(), is_array( $row ) ? $row : array() );
		}
		ksort( $out, SORT_NUMERIC );
		return $out;
	}

	/**
	 * @return int
	 */
	public static function first_id() {
		$all = self::all();
		if ( ! $all ) {
			return 0;
		}
		$ids = array_keys( $all );
		return (int) $ids[0];
	}

	/**
	 * @param int $id .
	 * @return bool
	 */
	public static function exists( $id ) {
		$all = self::all();
		return isset( $all[ (int) $id ] );
	}

	/**
	 * @param int $id 0 = pierwsza instancja.
	 * @return array<string,mixed>
	 */
	public static function get( $id = 0 ) {
		$all = self::all();
		$id  = (int) $id;
		if ( $id && isset( $all[ $id ] ) ) {
			return $all[ $id ];
		}
		if ( $all ) {
			return reset( $all );
		}
		return self::defaults();
	}

	/**
	 * @param int                 $id   .
	 * @param array<string,mixed> $data .
	 * @return void
	 */
	public static function save( $id, $data ) {
		$id         = (int) $id;
		$all        = self::all();
		$all[ $id ] = $data;
		update_option( self::TIMELINES, $all, false );
	}

	/**
	 * @param string $title .
	 * @return int
	 */
	public static function create( $title = '' ) {
		$next = (int) get_option( self::NEXT_ID, 1 );
		if ( $next < 1 ) {
			$next = 1;
		}
		while ( self::exists( $next ) ) {
			$next++;
		}

		$row          = self::defaults();
		$row['title'] = $title ? $title : sprintf( 'Timeline %d', $next );
		self::save( $next, $row );
		update_option( self::NEXT_ID, $next + 1, false );
		return $next;
	}

	/**
	 * @param int $id .
	 * @return int
	 */
	public static function duplicate( $id ) {
		$src = self::get( $id );
		if ( ! self::exists( $id ) ) {
			return 0;
		}
		$src['title'] = $src['title'] . ' ' . DCWT_I18n::t( 'copy_suffix' );
		return self::create_from( $src );
	}

	/**
	 * @param array<string,mixed> $data .
	 * @return int
	 */
	public static function create_from( $data ) {
		$next = (int) get_option( self::NEXT_ID, 1 );
		if ( $next < 1 ) {
			$next = 1;
		}
		while ( self::exists( $next ) ) {
			$next++;
		}
		self::save( $next, self::merge( self::defaults(), $data ) );
		update_option( self::NEXT_ID, $next + 1, false );
		return $next;
	}

	/**
	 * @param int $id .
	 * @return void
	 */
	public static function delete( $id ) {
		$id  = (int) $id;
		$all = self::all();
		unset( $all[ $id ] );
		update_option( self::TIMELINES, $all, false );
	}

	/**
	 * @param array<string,mixed> $base .
	 * @param array<string,mixed> $over .
	 * @return array<string,mixed>
	 */
	public static function merge( $base, $over ) {
		foreach ( $over as $key => $value ) {
			if ( is_array( $value ) && isset( $base[ $key ] ) && is_array( $base[ $key ] ) && self::is_assoc( $base[ $key ] ) ) {
				$base[ $key ] = self::merge( $base[ $key ], $value );
			} else {
				$base[ $key ] = $value;
			}
		}
		return $base;
	}

	/**
	 * @param array<mixed> $arr .
	 * @return bool
	 */
	private static function is_assoc( $arr ) {
		if ( array() === $arr ) {
			return true;
		}
		return array_keys( $arr ) !== range( 0, count( $arr ) - 1 );
	}
}
