<?php
/**
 * Design Cart Woo Timelines
 *
 * Author: Paweł Nosko https://www.designcart.pl/pawel-nosko.html
 * Company: Design Cart https://www.designcart.pl/
 *
 * @package Design_Cart_Woo_Timelines
 *
 * @var array<string,mixed>            $settings
 * @var array<int,array<string,mixed>> $groups
 * @var int                            $timeline_id
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals -- View included from plugin class.

$timeline_id = isset( $timeline_id ) ? (int) $timeline_id : 0;
$ratio       = isset( $settings['thumb_ratio'] ) ? $settings['thumb_ratio'] : '1-1';
$show_title  = ! empty( $settings['show_heading'] );
$heading     = isset( $settings['title'] ) ? $settings['title'] : '';
?>
<div
	class="dcwt dcwt--ratio-<?php echo esc_attr( $ratio ); ?>"
	data-dcwt
	data-dcwt-id="<?php echo esc_attr( (string) $timeline_id ); ?>"
	id="dcwt-<?php echo esc_attr( (string) $timeline_id ); ?>"
>
	<?php if ( $show_title && $heading ) : ?>
		<h2 class="dcwt__heading"><?php echo esc_html( $heading ); ?></h2>
	<?php endif; ?>

	<div class="dcwt__track">
		<div class="dcwt__spine" aria-hidden="true"></div>

		<?php foreach ( $groups as $index => $group ) : ?>
			<?php
			$side  = ( 0 === $index % 2 ) ? 'left' : 'right';
			$label = isset( $group['label'] ) ? $group['label'] : '';
			?>
			<article class="dcwt__branch dcwt__branch--<?php echo esc_attr( $side ); ?>">
				<div class="dcwt__arm">
					<h3 class="dcwt__date"><?php echo esc_html( $label ); ?></h3>
					<div class="dcwt__rail-row">
						<span class="dcwt__node" aria-hidden="true"></span>
						<span class="dcwt__rail" aria-hidden="true"></span>
					</div>
					<ul class="dcwt__thumbs">
						<?php foreach ( (array) $group['products'] as $item ) : ?>
							<li class="dcwt__item">
								<a class="dcwt__product" href="<?php echo esc_url( $item['permalink'] ); ?>">
									<span class="dcwt__thumb" role="img" aria-hidden="true">
										<span class="dcwt__photo" style="background-image:url('<?php echo esc_url( $item['image'] ); ?>')"></span>
									</span>
									<span class="dcwt__name"><?php echo esc_html( $item['name'] ); ?></span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
</div>
