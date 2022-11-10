<?php
/**
 * CFWS_SINGLE_PRODUCT loader Class File.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;

}

if ( ! class_exists( 'CFWS_SINGLE_PRODUCT' ) ) {

	/**
	 * CFWS_SINGLE_PRODUCT class.
	 */
	class CFWS_SINGLE_PRODUCT {

		/**
		 * Function Constructor.
		 */
		public function __construct() {

			add_shortcode( 'cfws_single_product', array( $this, 'single_product_shortcode_callback' ) );
			add_action( 'wp', array( $this, 'front_hooks' ) );
			add_action( 'wp_ajax_cfws_get_price_by_quantity_ajax', 'cfws_get_price_by_quantity_ajax' );
			add_action( 'wp_ajax_nopriv_cfws_get_price_by_quantity_ajax', 'cfws_get_price_by_quantity_ajax' );
			
			add_action( 'wp_ajax_cfws_product_add_to_cart_ajax',array($this, 'cfws_product_add_to_cart_ajax' ));
			add_action( 'wp_ajax_nopriv_cfws_product_add_to_cart_ajax',array($this, 'cfws_product_add_to_cart_ajax' ));
		}
		
		public function front_hooks() {

			// remove add to cart button.
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
			// remove price.
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );

			add_action( 'woocommerce_product_meta_start', array( $this, 'show_packages_info' ), 10 );
			add_filter( 'woocommerce_locate_template', array( $this, 'cfws_cart_page_override' ), 10, 3 );

		}
		public function cfws_cart_page_override( $template, $template_name, $template_path ) {
 
			if ( 'cart.php' === basename( $template ) ) {
				$template = CFWS_TEMP_DIR . '/woocommerce/cart/cart.php';
			}
			return $template;
		}

		public function cfws_product_add_to_cart_ajax() {
			ob_start();
			$product_id   = isset( $_REQUEST['product_id'] ) ? $_REQUEST['product_id'] : 0;
			$quantity   = isset( $_REQUEST['quantity'] ) ? $_REQUEST['quantity'] : 0;
			$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
			$product_status    = get_post_status( $product_id );
			if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity ) && 'publish' === $product_status ) {
				do_action( 'woocommerce_ajax_added_to_cart', $product_id );
				wc_add_to_cart_message( $product_id );
			} else {
				// If there was an error adding to the cart, redirect to the product page to show any errors
				$data = array(
					'error'       => true,
					'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id )
				);
				wp_send_json( $data );
			}
			die();
		}

		public function show_packages_info() {
			do_shortcode( '[cfws_single_product]' );
			// global $product;
			// if($product->get_type() == 'simple'){

			// }
		}
		public function cfws_change_cart_page() {

			if ( is_admin() ) {
				return;
			}
			require_once CFWS_TEMP_DIR . '/woocommerce/cart/cart.php';
		}

		// public function cfws_add_item_data($cart_item_data, $product_id, $variation_id ) {

		// 	global $woocommerce;

		// 	$offered_price = filter_input( INPUT_POST, 'offered_price' );

		// 	$cart_item_data['offered_price'] = $offered_price;
		
		// 	return $cart_item_data;
		// }

		public function single_product_shortcode_callback() {

			if ( is_admin() ) {
				return;
			}

			require_once CFWS_TEMP_DIR . '/single-product-packages.php';
		}

	}

	new CFWS_SINGLE_PRODUCT();
}
