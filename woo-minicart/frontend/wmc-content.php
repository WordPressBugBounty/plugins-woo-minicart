<?php
/**
 * Frontend minicart content template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Bail if running in admin.
if ( is_admin() ) {
	return;
}

$items      = array();
$cart_count = 0;
$item_text  = __( 'items', 'woo-minicart' );

if ( function_exists( 'WC' ) && WC()->cart ) {
	$items      = (array) WC()->cart->get_cart();
	$cart_count = (int) WC()->cart->get_cart_contents_count();
	$item_text  = ( 1 === $cart_count ) ? __( 'item', 'woo-minicart' ) : __( 'items', 'woo-minicart' );
}
?>

<?php if ( ! empty( $items ) ) : ?>
	<div class="wmc-content">
		<h3>
			<?php
			printf(
				/* translators: 1: cart item count, 2: item/items */
				esc_html__( 'You have %1$d %2$s in cart', 'woo-minicart' ),
				(int) $cart_count,
				esc_html( $item_text )
			);
			?>
		</h3>

		<ul class="wmc-products">
			<?php foreach ( $items as $cart_item_key => $values ) :
				if ( empty( $values['data'] ) || ! $values['data'] instanceof WC_Product ) {
					continue;
				}

				/** @var WC_Product $_product */
				$_product   = $values['data'];
				$product_id = $_product->get_id();
				?>
				<li class="woocommerce-mini-cart-item mini_cart_item">
					<div class="wmc-remove">
						<?php
						$remove_link = sprintf(
							'<a href="%s" class="remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s">&times;</a>',
							esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
							esc_attr__( 'Remove this item', 'woo-minicart' ),
							esc_attr( $product_id ),
							esc_attr( $cart_item_key ),
							esc_attr( $_product->get_sku() )
						);

						// Allow 3rd parties/WooCommerce to filter the remove link HTML.
						echo apply_filters( 'woocommerce_cart_item_remove_link', $remove_link, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</div>

					<div class="wmc-image">
						<a href="<?php echo esc_url( $_product->get_permalink() ); ?>">
							<?php
							// Thumbnail HTML is already escaped by WooCommerce; allow filtered output.
							echo apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $values, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
						</a>
					</div>

					<div class="wmc-details">
						<a class="wmc-product-title" href="<?php echo esc_url( $_product->get_permalink() ); ?>">
							<h4><?php echo esc_html( $_product->get_name() ); ?></h4>
						</a>

						<p>
							<?php
							$price_html = $_product->get_price_html();
							echo '<span class="wmc-price">' . wp_kses_post( $price_html ) . '</span> x ' . esc_html( (string) ( $values['quantity'] ?? 1 ) );
							?>
						</p>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>

		<div class="wmc-subtotal">
			<h5>
				<?php
				esc_html_e( 'Subtotal:', 'woo-minicart' );
				echo ' ';
				// Woo subtotal HTML is escaped/filtered by WooCommerce; allow as HTML.
				echo wp_kses_post( WC()->cart->get_cart_subtotal() );
				?>
			</h5>
		</div>

		<div class="wmc-bottom-buttons">
			<a href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php esc_html_e( 'View Cart', 'woo-minicart' ); ?></a>
			<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>"><?php esc_html_e( 'Checkout', 'woo-minicart' ); ?></a>
		</div>
	</div>
<?php else : ?>
	<div class="wmc-content wmc-empty">
		<h3><?php esc_html_e( 'Your cart is empty.', 'woo-minicart' ); ?></h3>
	</div>
<?php endif; ?>