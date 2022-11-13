<?php
/**
 * CFWS_CART Class File.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;

}

if ( ! class_exists( 'CFWS_CART' ) ) {

	/**
	 * CFWS_ACCOUNT_ORDERS class.
	 */
	class CFWS_CART {

		/**
		 * Function Constructor.
		 */
		public function __construct() {
			add_action( 'wp', array( $this, 'front_hooks' ) );
			add_filter( 'woocommerce_update_cart_action_cart_updated', array( $this, 'recalculate_price_on_update_cart' ), 10, 1 );
		}

		public function front_hooks() {
			// changing html of unit price on cart page.
			add_filter( 'woocommerce_cart_item_price', array( $this, 'change_product_price_display' ), 99, 3 );
			// Recalculate totals if offered price found.
			add_filter( 'woocommerce_before_calculate_totals', array( $this, 'add_custom_price_in_totals_calculations' ), 99, 1 );
			// changing html of unit price on mini cart.
			add_filter( 'woocommerce_widget_cart_item_quantity', array( $this, 'change_product_price_display_min_cart' ), 99, 3 );

			add_filter( 'woocommerce_locate_template', array( $this, 'cfws_cart_page_override' ), 10, 3 );

		}

		public function cfws_cart_page_override( $template, $template_name, $template_path ) {

			if ( 'cart.php' === basename( $template ) ) {
				$template = CFWS_TEMP_DIR . '/woocommerce/cart/cart.php';
			}
			return $template;
		}

		public function change_product_price_display( $price, $cart_item, $cart_item_key ) {
			global  $woocommerce;

			if ( isset( $cart_item['offered_price'] ) && ( ! empty( $cart_item['offered_price'] ) && $cart_item['offered_price'] != 'false' ) ) {
				$price = get_woocommerce_currency_symbol() . "<input name='" . 'cart[' . $cart_item_key . "][offered_price]' class='cfws_custom_cart_price' value='" . $cart_item['offered_price'] . "'/>";
			}

			return $price;
		}

		public function change_product_price_display_min_cart( $html, $cart_item, $cart_item_key ) {

			if ( isset( $cart_item['offered_price'] ) && ( ! empty( $cart_item['offered_price'] ) && $cart_item['offered_price'] != 'false' ) ) {
				$html = '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'], wc_price( $cart_item['offered_price'] ) ) . '</span>';
			}

			return $html;
		}

		public function add_custom_price_in_totals_calculations( $cart_object ) {

			foreach ( $cart_object->get_cart() as $item ) {

				if ( isset( $item['offered_price'] ) && ( ! empty( $item['offered_price'] ) && $item['offered_price'] != 'false' ) ) {

					// when offered price applicable.
					$item['data']->set_price( $item['offered_price'] );
				} else {
					// get price based on range if no offered price applicable.
					$product_id = $item['product_id'];
					$quantity   = $item['quantity'];
					$item['data']->set_price( cfws_get_price_by_quantity( $quantity, $product_id ) );
				}
			}

		}

		public function recalculate_price_on_update_cart( $cart_updated ) {
			global $woocommerce;

			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				if ( isset( $_POST['cart'][ $cart_item_key ]['offered_price'] ) && $_POST['cart'][ $cart_item_key ]['offered_price'] != 'false' ) {
					$cart_item['offered_price'] = $_POST['cart'][ $cart_item_key ]['offered_price'];

				} else {
					unset( $cart_item['offered_price'] );
				}

				WC()->cart->cart_contents[ $cart_item_key ] = $cart_item;
			}

			return $cart_updated;
		}

	}
	new CFWS_CART();
}
