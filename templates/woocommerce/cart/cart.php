<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.8.0
 */

defined( 'ABSPATH' ) || exit;
$user_id = get_current_user_id();
$addresses = get_user_meta( $user_id, 'billing_address' );
$default_billing_address = get_user_meta( $user_id, 'billing_default_address' );
$default_shipping_address = get_user_meta( $user_id, 'shipping_default_address' );
?>
<div class="cfws_cart_wrapper">
	<div class="cfws_cart_addresses" dir="rtl">
		<div class="cfws_cart_billing">
			<div class="card">
				<div class="card-header">Billing Address</div>
				<div class="card-body">
				<?php if ( isset( $addresses ) && ! empty( $addresses ) ) : ?>
					<?php foreach ( $addresses[0] as $key => $address ) :?>
						<div class="form-check">
							<?php 
							if(!empty($default_billing_address) && $address['id'] == $default_billing_address[0]['id']){ ?>
									<input class="form-check-input" type="radio" value="<?= $address['id'] ?>" name="billing_address" id="billing_address_<?= $address['id'] ?>" checked >
							<?php } elseif($key == 0){ ?>
								<input class="form-check-input" type="radio" value="<?= $address['id'] ?>" name="billing_address" id="billing_address_<?= $address['id'] ?>" checked >

							<?php } else{ ?>
								<input class="form-check-input" type="radio" value="<?= $address['id'] ?>" name="billing_address" id="billing_address_<?= $address['id'] ?>" >
							<?php } ?>
							<label class="form-check-label" for="billing_address_<?= $address['id'] ?>">
								<?= $address['address_name'] ?>
							</label>
						</div>
					<?php endforeach ?>
				<?php else: ?>
					<h4><?php echo esc_html_e( 'You have not set up this type of address yet.', 'woocommerce' ); ?></h4>
				<?php endif ?>
				</div>
			</div>
		</div>
		<div class="cfws_cart_shipping">
			<div class="card">
				<div class="card-header">Shipping Address</div>
				<div class="card-body">
				<?php if ( isset( $addresses ) && ! empty( $addresses ) ) : ?>
					<?php foreach ( $addresses[0] as $key => $address ) :?>
						<div class="form-check">
							<?php 
							// print_r($default_shipping_address[0]['id']); exit;
							if(!empty($default_shipping_address) && $address['id'] == $default_shipping_address[0]['id']){ ?>
									<input class="form-check-input" type="radio" value="<?= $address['id'] ?>" name="shipping_address" id="shipping_address_<?= $address['id'] ?>" checked >
							<?php } elseif($key == 0){ ?>
								<input class="form-check-input" type="radio" value="<?= $address['id'] ?>" name="shipping_address" id="shipping_address_<?= $address['id'] ?>" checked >

							<?php } else{ ?>
								<input class="form-check-input" type="radio" value="<?= $address['id'] ?>" name="shipping_address" id="shipping_address_<?= $address['id'] ?>" >
							<?php } ?>
							<label class="form-check-label" for="shipping_address_<?= $address['id'] ?>">
								<?= $address['address_name'] ?>
							</label>
						</div>
					<?php endforeach ?>
				<?php else: ?>
					<h4><?php echo esc_html_e( 'You have not set up this type of address yet.', 'woocommerce' ); ?></h4>
				<?php endif ?>
				</div>
			</div>
		</div>
		<div class="cfws_cart_collaterals">
			<div class="card">
				<div class="card-header">Totals</div>
				<div class="card-body">
					<div class="cfws_div_2 boxes">Subtotal</div>
					<div class="cfws_div_1 boxes text-left"><?= WC()->cart->subtotal_ex_tax; ?></div>
					<div class="cfws_div_2 boxes">Tax/Vat</div>
					<div class="cfws_div_1 boxes text-left"><?= WC()->cart->get_taxes_total(); ?></div>
					<div class="cfws_div_2">Totals</div>
					<div class="cfws_div_1 text-left"><?= WC()->cart->total; ?></div>
				<!-- </div> -->
				<!-- <div class="card-footer"> -->
					<div class="form-row place-order">
						<button type="button" class="cfws_place_order" id="cfws_place_order">Place order</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="cfws_cart_form">
		<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
			<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
				<thead>
					<tr>
						<th class="product-remove">&nbsp;</th>
						<th class="product-subtotal"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
						<th class="product-price"><?php esc_html_e( 'Unit Price / Offered Price', 'cfws' ); ?></th>
						<th class="product-quantity"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th>
						<th class="product-name"><?php esc_html_e( 'Product Title', 'woocommerce' ); ?></th>
						<th class="product-name"><?php esc_html_e( 'Image', 'woocommerce' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php do_action( 'woocommerce_before_cart_contents' ); ?>

					<?php
					foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
						$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
						$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

						if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
							$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
							?>
							<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

								<td class="product-remove">
									<?php
								echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									'woocommerce_cart_item_remove_link',
									sprintf(
										'<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
										esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
										esc_html__( 'Remove this item', 'woocommerce' ),
										esc_attr( $product_id ),
										esc_attr( $_product->get_sku() )
									),
									$cart_item_key
								);
								?>
							</td>

							<td class="product-subtotal" data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>">
								<?php
								echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
								?>
							</td>


							<td class="product-price" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">
								<?php
								echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
								?>
							</td>

							<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>">
								<?php
								if ( $_product->is_sold_individually() ) {
									$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
								} else {
									$product_quantity = woocommerce_quantity_input(
										array(
											'input_name'   => "cart[{$cart_item_key}][qty]",
											'input_value'  => $cart_item['quantity'],
											'max_value'    => $_product->get_max_purchase_quantity(),
											'min_value'    => '0',
											'product_name' => $_product->get_name(),
										),
										$_product,
										false
									);
								}

						echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // PHPCS: XSS ok.
						?>
					</td>

					<td class="product-name" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>">
						<?php
						if ( ! $product_permalink ) {
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
						} else {
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
						}

						do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

						// Meta data.
						echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.

						// Backorder notification.
						if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
						}
						?>
					</td>

					<td class="product-thumbnail">
						<?php
						$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

						if ( ! $product_permalink ) {
								echo $thumbnail; // PHPCS: XSS ok.
							} else {
								printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
							}
							?>
						</td>
					</tr>
					<?php
				}
			}
			?>

			<?php do_action( 'woocommerce_cart_contents' ); ?>

			<tr>
				<td colspan="6" class="actions">

					<button type="submit" class="button" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>"><?php esc_html_e( 'Update cart', 'woocommerce' ); ?></button>

					<?php do_action( 'woocommerce_cart_actions' ); ?>

					<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
				</td>
				
			</tr>
		</tbody>
	</table>
	
</form>
</div>
</div>