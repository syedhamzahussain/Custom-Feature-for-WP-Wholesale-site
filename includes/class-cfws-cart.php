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
		}

		public function front_hooks() {
			//  changing html of unit price on cart page.
			add_filter( 'woocommerce_cart_item_price', array( $this, 'change_product_price_display' ),99,3 );
			// Recalculate totals if offered price found.
			add_filter( 'woocommerce_before_calculate_totals', array( $this, 'add_custom_price_in_totals_calculations' ),99,1 );
		}

		public function change_product_price_display( $price , $cart_item, $cart_item_key ) {
			global  $woocommerce;
			if( array_key_exists( 'offered_price', $cart_item ) &&  !empty( $cart_item['offered_price'] ) ) {
				$price = get_woocommerce_currency_symbol()."<input name='cfws_custom_cart_price' class='cfws_custom_cart_price' value='".$cart_item[ 'offered_price' ]."'/>";
			}

			return $price;
		}

		public function add_custom_price_in_totals_calculations( $cart_object ) {

			foreach ( $cart_object->get_cart() as $item ) {

				if( array_key_exists( 'offered_price', $item ) &&  !empty( $item['offered_price'] ) ) {
					$item[ 'data' ]->set_price( $item[ 'offered_price' ] );
				}

			}

		}

	}
	new CFWS_CART();
}