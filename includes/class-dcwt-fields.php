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
 * Pola formularza DC Interface.
 */
class DCWT_Fields {

	/**
	 * @param string $name  .
	 * @param int    $value .
	 * @param string $label .
	 * @param string $hint  .
	 * @return void
	 */
	public static function toggle( $name, $value, $label, $hint = '' ) {
		?>
		<label class="dc-toggle">
			<input class="dc-toggle__input" type="checkbox" name="<?php echo esc_attr( $name ); ?>" value="1" <?php checked( (int) $value, 1 ); ?> />
			<span class="dc-toggle__track"></span>
			<span><?php echo esc_html( $label ); ?></span>
		</label>
		<?php if ( $hint ) : ?>
			<p class="dc-hint"><?php echo esc_html( $hint ); ?></p>
		<?php endif; ?>
		<?php
	}

	/**
	 * @param string $name  .
	 * @param string $value .
	 * @param string $label .
	 * @param string $type  .
	 * @return void
	 */
	public static function input( $name, $value, $label, $type = 'text' ) {
		$id = sanitize_html_class( str_replace( array( '[', ']' ), array( '_', '' ), $name ) );
		?>
		<div class="dc-field">
			<label class="dc-label" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
			<input class="dc-input" id="<?php echo esc_attr( $id ); ?>" type="<?php echo esc_attr( $type ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( (string) $value ); ?>" />
		</div>
		<?php
	}

	/**
	 * @param string               $name    .
	 * @param string               $value   .
	 * @param string               $label   .
	 * @param array<string,string> $options .
	 * @param string               $class   .
	 * @return void
	 */
	public static function select( $name, $value, $label, $options, $class = '' ) {
		$id = sanitize_html_class( str_replace( array( '[', ']' ), array( '_', '' ), $name ) );
		?>
		<div class="dc-field">
			<label class="dc-label" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
			<select class="dc-select <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>">
				<?php foreach ( $options as $opt_value => $opt_label ) : ?>
					<option value="<?php echo esc_attr( (string) $opt_value ); ?>" <?php selected( (string) $value, (string) $opt_value ); ?>">
						<?php echo esc_html( $opt_label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php
	}

	/**
	 * @param string               $name    .
	 * @param string               $value   .
	 * @param string               $label   .
	 * @param array<string,string> $options .
	 * @return void
	 */
	public static function switch_group( $name, $value, $label, $options ) {
		?>
		<div class="dc-field">
			<span class="dc-label"><?php echo esc_html( $label ); ?></span>
			<div class="dc-switch-group dc-switch-group--solid" role="radiogroup">
				<?php foreach ( $options as $opt_value => $opt_label ) : ?>
					<label class="dc-switch-btn">
						<input class="dc-switch-btn__input" type="radio" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( (string) $opt_value ); ?>" <?php checked( (string) $value, (string) $opt_value ); ?> />
						<span class="dc-switch-btn__label"><?php echo esc_html( $opt_label ); ?></span>
					</label>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * @param string $name  .
	 * @param string $value .
	 * @param string $label .
	 * @return void
	 */
	public static function color( $name, $value, $label ) {
		?>
		<div class="dc-field">
			<label class="dc-label"><?php echo esc_html( $label ); ?></label>
			<div class="dc-colorpicker" data-dc-colorpicker data-name="<?php echo esc_attr( $name ); ?>" data-value="<?php echo esc_attr( $value ? $value : '#111111' ); ?>" data-label="<?php echo esc_attr( $label ); ?>"></div>
		</div>
		<?php
	}

	/**
	 * Wymiar DC Interface (range + input + px/%).
	 *
	 * @param array<string,mixed> $args .
	 * @return void
	 */
	public static function dimension( $args ) {
		$name       = isset( $args['name'] ) ? (string) $args['name'] : '';
		$unit_name  = isset( $args['unit_name'] ) ? (string) $args['unit_name'] : $name . '_unit';
		$label      = isset( $args['label'] ) ? (string) $args['label'] : '';
		$value      = isset( $args['value'] ) ? $args['value'] : 100;
		$unit       = isset( $args['unit'] ) ? (string) $args['unit'] : 'px';
		$min        = isset( $args['min'] ) ? (int) $args['min'] : 1;
		$max_px     = isset( $args['max_px'] ) ? (int) $args['max_px'] : 2000;
		$max_pct    = isset( $args['max_pct'] ) ? (int) $args['max_pct'] : 100;
		$fixed_unit = isset( $args['fixed_unit'] ) ? (string) $args['fixed_unit'] : '';
		?>
		<div class="dc-field">
			<div
				class="dc-dimension"
				data-dc-dimension
				data-name="<?php echo esc_attr( $name ); ?>"
				<?php if ( ! $fixed_unit ) : ?>
					data-unit-name="<?php echo esc_attr( $unit_name ); ?>"
					data-unit="<?php echo esc_attr( 'px' === $unit ? 'px' : '%' ); ?>"
				<?php else : ?>
					data-fixed-unit="<?php echo esc_attr( $fixed_unit ); ?>"
				<?php endif; ?>
				data-label="<?php echo esc_attr( $label ); ?>"
				data-value="<?php echo esc_attr( (string) $value ); ?>"
				data-min="<?php echo esc_attr( (string) $min ); ?>"
				data-max-px="<?php echo esc_attr( (string) $max_px ); ?>"
				data-max-pct="<?php echo esc_attr( (string) $max_pct ); ?>"
			></div>
		</div>
		<?php
	}

	/**
	 * @param string              $prefix .
	 * @param array<string,mixed> $typo   .
	 * @param string              $kind   text|button|link.
	 * @return void
	 */
	public static function typo( $prefix, $typo, $kind = 'text' ) {
		$typo = is_array( $typo ) ? $typo : array();
		$size = isset( $typo['size'] ) ? $typo['size'] : 14;
		$unit = isset( $typo['size_unit'] ) ? $typo['size_unit'] : 'px';
		?>
		<div class="dc-row dc-row--2">
			<?php
			self::select(
				$prefix . '[family]',
				isset( $typo['family'] ) ? $typo['family'] : 'inherit',
				DCWT_I18n::t( 'font_family' ),
				DCWT_Settings::fonts()
			);
			?>
			<div class="dc-field">
				<span class="dc-label"><?php echo esc_html( DCWT_I18n::t( 'font_size' ) ); ?></span>
				<div class="dcwt-size">
					<input class="dc-input" type="number" step="0.1" min="0" name="<?php echo esc_attr( $prefix . '[size]' ); ?>" value="<?php echo esc_attr( (string) $size ); ?>" />
					<div class="dc-switch-group dc-switch-group--solid dcwt-size__units" role="radiogroup">
						<?php foreach ( array( 'px', 'rem', 'vw', 'vh', '%' ) as $u ) : ?>
							<label class="dc-switch-btn">
								<input class="dc-switch-btn__input" type="radio" name="<?php echo esc_attr( $prefix . '[size_unit]' ); ?>" value="<?php echo esc_attr( $u ); ?>" <?php checked( $unit, $u ); ?> />
								<span class="dc-switch-btn__label"><?php echo esc_html( $u ); ?></span>
							</label>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
		<?php
		self::switch_group(
			$prefix . '[weight]',
			isset( $typo['weight'] ) ? $typo['weight'] : '400',
			DCWT_I18n::t( 'font_weight' ),
			array(
				'300' => '300',
				'400' => '400',
				'500' => '500',
				'600' => '600',
				'700' => '700',
				'800' => '800',
			)
		);
		self::switch_group(
			$prefix . '[align]',
			isset( $typo['align'] ) ? $typo['align'] : 'left',
			DCWT_I18n::t( 'align' ),
			array(
				'left'   => DCWT_I18n::t( 'align_left' ),
				'center' => DCWT_I18n::t( 'align_center' ),
				'right'  => DCWT_I18n::t( 'align_right' ),
			)
		);
		self::toggle( $prefix . '[uppercase]', ! empty( $typo['uppercase'] ) ? 1 : 0, DCWT_I18n::t( 'uppercase' ) );

		echo '<div class="dc-row dc-row--2">';
		self::color( $prefix . '[color]', isset( $typo['color'] ) ? $typo['color'] : '#111111', DCWT_I18n::t( 'color' ) );
		if ( 'button' === $kind ) {
			self::color( $prefix . '[bg]', isset( $typo['bg'] ) ? $typo['bg'] : '#1fa28c', DCWT_I18n::t( 'bg' ) );
		} elseif ( 'link' === $kind ) {
			self::color( $prefix . '[hover_color]', isset( $typo['hover_color'] ) ? $typo['hover_color'] : '#1fa28c', DCWT_I18n::t( 'hover_color' ) );
		}
		echo '</div>';

		if ( 'button' === $kind ) {
			echo '<div class="dc-row dc-row--2">';
			self::color( $prefix . '[hover_color]', isset( $typo['hover_color'] ) ? $typo['hover_color'] : '#ffffff', DCWT_I18n::t( 'hover_color' ) );
			self::color( $prefix . '[hover_bg]', isset( $typo['hover_bg'] ) ? $typo['hover_bg'] : '#178675', DCWT_I18n::t( 'hover_bg' ) );
			echo '</div>';
		}
	}

	/**
	 * @param array<string,mixed> $item .
	 * @param int                 $idx  .
	 * @return void
	 */
	public static function product_row( $item, $idx ) {
		$id         = isset( $item['id'] ) ? (int) $item['id'] : 0;
		$date       = isset( $item['date'] ) ? (string) $item['date'] : '';
		$date_label = isset( $item['date_label'] ) ? (string) $item['date_label'] : '';
		$title      = $id ? get_the_title( $id ) : '';
		$thumb      = $id ? get_the_post_thumbnail_url( $id, 'thumbnail' ) : '';
		?>
		<li class="dcwt-product" data-product-row>
			<span class="dashicons dashicons-move dcwt-product__handle" aria-hidden="true"></span>
			<?php if ( $thumb ) : ?>
				<img class="dcwt-product__thumb" src="<?php echo esc_url( $thumb ); ?>" alt="" />
			<?php else : ?>
				<span class="dcwt-product__thumb dcwt-product__thumb--empty"></span>
			<?php endif; ?>
			<div class="dcwt-product__body">
				<input type="hidden" name="products[<?php echo esc_attr( (string) $idx ); ?>][id]" value="<?php echo esc_attr( (string) $id ); ?>" />
				<strong class="dcwt-product__title"><?php echo esc_html( $title ? $title : ( '#' . $id ) ); ?></strong>
				<div class="dc-row dc-row--2">
					<div class="dc-field">
						<label class="dc-label"><?php echo esc_html( DCWT_I18n::t( 'product_date' ) ); ?></label>
						<input class="dc-input" type="date" name="products[<?php echo esc_attr( (string) $idx ); ?>][date]" value="<?php echo esc_attr( $date ); ?>" />
					</div>
					<div class="dc-field">
						<label class="dc-label"><?php echo esc_html( DCWT_I18n::t( 'product_date_label' ) ); ?></label>
						<input class="dc-input" type="text" name="products[<?php echo esc_attr( (string) $idx ); ?>][date_label]" value="<?php echo esc_attr( $date_label ); ?>" />
					</div>
				</div>
			</div>
			<button type="button" class="button-link-delete dcwt-product__remove" data-remove-product aria-label="<?php echo esc_attr( DCWT_I18n::t( 'remove' ) ); ?>">
				<span class="dashicons dashicons-trash"></span>
			</button>
		</li>
		<?php
	}
}
