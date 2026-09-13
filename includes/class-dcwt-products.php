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
 * Zapytania o produkty WooCommerce i grupowanie po dacie.
 */
class DCWT_Products {

	/**
	 * @param array<string,mixed>|null $settings .
	 * @return array<int,array<string,mixed>>
	 */
	public static function resolve( $settings = null ) {
		$settings = is_array( $settings ) ? $settings : DCWT_Settings::get();
		$meta     = self::meta_map( isset( $settings['products'] ) ? $settings['products'] : array() );
		$limit    = max( 1, (int) $settings['limit'] );
		$source   = isset( $settings['source'] ) ? $settings['source'] : 'selected';

		$listed = array();
		foreach ( (array) $settings['products'] as $row ) {
			if ( ! empty( $row['id'] ) ) {
				$listed[] = (int) $row['id'];
			}
		}
		$listed = array_values( array_unique( array_filter( $listed ) ) );

		if ( 'category' === $source ) {
			$ids = self::query_ids(
				array(
					'limit'    => $limit,
					'category' => array( (int) $settings['category_id'] ),
				)
			);
		} elseif ( 'sale' === $source ) {
			$sale_ids = wc_get_product_ids_on_sale();
			$ids      = $sale_ids ? self::query_ids(
				array(
					'limit'   => $limit,
					'include' => $sale_ids,
				)
			) : array();
		} else {
			$ids = array_slice( $listed, 0, $limit );
		}

		if ( ! $ids && $listed ) {
			$ids = array_slice( $listed, 0, $limit );
		}

		$out = array();
		foreach ( $ids as $id ) {
			$product = wc_get_product( $id );
			if ( ! $product || ! $product->is_visible() ) {
				continue;
			}
			$extra = isset( $meta[ $id ] ) ? $meta[ $id ] : array(
				'date'       => '',
				'date_label' => '',
			);
			$out[] = self::normalize( $product, $extra );
		}
		return $out;
	}

	/**
	 * @param array<string,mixed>|null $settings .
	 * @return array<int,array<string,mixed>>
	 */
	public static function grouped( $settings = null ) {
		$settings = is_array( $settings ) ? $settings : DCWT_Settings::get();
		$products = self::resolve( $settings );
		$format   = isset( $settings['date_format'] ) ? $settings['date_format'] : 'F Y';
		$order    = isset( $settings['date_order'] ) && 'asc' === $settings['date_order'] ? 'asc' : 'desc';
		$allowed  = array_keys( DCWT_Settings::date_formats() );
		if ( ! in_array( $format, $allowed, true ) ) {
			$format = 'F Y';
		}
		$group_by = DCWT_Settings::group_from_format( $format );

		$groups = array();
		foreach ( $products as $product ) {
			$ts    = isset( $product['timestamp'] ) ? (int) $product['timestamp'] : time();
			$label = isset( $product['date_label'] ) ? trim( (string) $product['date_label'] ) : '';

			if ( $label ) {
				$key     = 'l:' . $label;
				$display = $label;
			} else {
				if ( 'year' === $group_by ) {
					$key = wp_date( 'Y', $ts );
				} elseif ( 'day' === $group_by ) {
					$key = wp_date( 'Y-m-d', $ts );
				} else {
					$key = wp_date( 'Y-m', $ts );
				}
				$display = wp_date( $format, $ts );
			}

			if ( ! isset( $groups[ $key ] ) ) {
				$groups[ $key ] = array(
					'key'       => $key,
					'label'     => $display,
					'timestamp' => $ts,
					'products'  => array(),
				);
			}

			$groups[ $key ]['products'][] = $product;
			$groups[ $key ]['timestamp']  = ( 'asc' === $order )
				? min( $groups[ $key ]['timestamp'], $ts )
				: max( $groups[ $key ]['timestamp'], $ts );
		}

		uasort(
			$groups,
			static function ( $a, $b ) use ( $order ) {
				if ( $a['timestamp'] === $b['timestamp'] ) {
					return 0;
				}
				$cmp = ( $a['timestamp'] < $b['timestamp'] ) ? -1 : 1;
				return ( 'asc' === $order ) ? $cmp : -$cmp;
			}
		);

		return array_values( $groups );
	}

	/**
	 * @param array<int,array<string,mixed>> $rows .
	 * @return array<int,array<string,string>>
	 */
	public static function meta_map( $rows ) {
		$map = array();
		foreach ( (array) $rows as $row ) {
			if ( empty( $row['id'] ) ) {
				continue;
			}
			$map[ (int) $row['id'] ] = array(
				'date'       => isset( $row['date'] ) ? (string) $row['date'] : '',
				'date_label' => isset( $row['date_label'] ) ? (string) $row['date_label'] : '',
			);
		}
		return $map;
	}

	/**
	 * @param array<string,mixed> $args .
	 * @return array<int,int>
	 */
	public static function query_ids( $args ) {
		$defaults = array(
			'status'  => 'publish',
			'limit'   => 24,
			'return'  => 'ids',
			'orderby' => 'date',
			'order'   => 'DESC',
		);
		if ( isset( $args['category'] ) ) {
			$cat_ids = array_map( 'intval', (array) $args['category'] );
			if ( empty( $cat_ids ) || array( 0 ) === $cat_ids ) {
				return array();
			}
			$slugs = array();
			foreach ( $cat_ids as $cat_id ) {
				$term = get_term( $cat_id, 'product_cat' );
				if ( $term && ! is_wp_error( $term ) ) {
					$slugs[] = $term->slug;
				}
			}
			if ( empty( $slugs ) ) {
				return array();
			}
			$args['category'] = $slugs;
		}
		$ids = wc_get_products( wp_parse_args( $args, $defaults ) );
		return is_array( $ids ) ? array_map( 'intval', $ids ) : array();
	}

	/**
	 * @param WC_Product           $product .
	 * @param array<string,string> $extra   .
	 * @return array<string,mixed>
	 */
	public static function normalize( $product, $extra ) {
		$image_id = $product->get_image_id();
		$thumb    = $image_id ? wp_get_attachment_image_url( $image_id, 'woocommerce_thumbnail' ) : '';
		if ( ! $thumb ) {
			$thumb = $image_id ? wp_get_attachment_image_url( $image_id, 'medium' ) : '';
		}
		if ( ! $thumb ) {
			$thumb = wc_placeholder_img_src( 'woocommerce_thumbnail' );
		}

		$created = $product->get_date_created();
		$ts      = $created ? $created->getTimestamp() : time();
		$date    = isset( $extra['date'] ) ? trim( (string) $extra['date'] ) : '';
		if ( $date ) {
			$parsed = strtotime( $date . ' 12:00:00' );
			if ( $parsed ) {
				$ts = $parsed;
			}
		}

		return array(
			'id'         => $product->get_id(),
			'name'       => $product->get_name(),
			'permalink'  => $product->get_permalink(),
			'image'      => $thumb,
			'timestamp'  => $ts,
			'date'       => $date,
			'date_label' => isset( $extra['date_label'] ) ? (string) $extra['date_label'] : '',
		);
	}

	/**
	 * @param string $term .
	 * @return array<int,array<string,mixed>>
	 */
	public static function search( $term ) {
		$ids = wc_get_products(
			array(
				'status' => 'publish',
				'limit'  => 20,
				's'      => $term,
				'return' => 'ids',
			)
		);
		$rows = array();
		foreach ( (array) $ids as $id ) {
			$product = wc_get_product( $id );
			if ( ! $product ) {
				continue;
			}
			$thumb   = get_the_post_thumbnail_url( $id, 'thumbnail' );
			$created = $product->get_date_created();
			$rows[]  = array(
				'id'         => $id,
				'name'       => $product->get_name(),
				'thumb'      => $thumb ? $thumb : '',
				'date'       => $created ? $created->date( 'Y-m-d' ) : '',
				'date_label' => '',
			);
		}
		return $rows;
	}

	/**
	 * @param string                         $source      .
	 * @param int                            $category_id .
	 * @param int                            $limit       .
	 * @param array<int,array<string,mixed>> $existing    .
	 * @return array<int,array<string,mixed>>
	 */
	public static function listing_for_source( $source, $category_id, $limit, $existing ) {
		$meta  = self::meta_map( $existing );
		$limit = max( 1, (int) $limit );
		$ids   = array();

		if ( 'category' === $source ) {
			$ids = self::query_ids(
				array(
					'limit'    => $limit,
					'category' => array( (int) $category_id ),
				)
			);
		} elseif ( 'sale' === $source ) {
			$sale_ids = wc_get_product_ids_on_sale();
			$ids      = $sale_ids ? self::query_ids(
				array(
					'limit'   => $limit,
					'include' => $sale_ids,
				)
			) : array();
		} else {
			foreach ( (array) $existing as $row ) {
				if ( ! empty( $row['id'] ) ) {
					$ids[] = (int) $row['id'];
				}
			}
		}

		$out = array();
		foreach ( $ids as $id ) {
			$product = wc_get_product( $id );
			if ( ! $product ) {
				continue;
			}
			$thumb   = get_the_post_thumbnail_url( $id, 'thumbnail' );
			$created = $product->get_date_created();
			$saved   = isset( $meta[ $id ] ) ? $meta[ $id ] : array();
			$out[]   = array(
				'id'         => $id,
				'name'       => $product->get_name(),
				'thumb'      => $thumb ? $thumb : '',
				'date'       => ! empty( $saved['date'] ) ? $saved['date'] : ( $created ? $created->date( 'Y-m-d' ) : '' ),
				'date_label' => isset( $saved['date_label'] ) ? $saved['date_label'] : '',
			);
		}
		return $out;
	}
}
