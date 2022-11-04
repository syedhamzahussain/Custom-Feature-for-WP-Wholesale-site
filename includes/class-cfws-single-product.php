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
			add_action( 'wp_ajax_cfws_save_offered_price',  array( $this,  'cfws_save_offered_price' ));
			add_action( 'wp_ajax_nopriv_cfws_save_offered_price',  array( $this,  'cfws_save_offered_price' ));

			add_filter( 'woocommerce_add_cart_item_data', 'cfws_add_item_data', 10, 3 );


			add_action( 'woocommerce_before_calculate_totals', array( $this, 'cfws_save_offered_price' ) );
			add_filter('woocommerce_add_cart_item_data','cfws_add_item_data',1,10);


		}

		public function front_hooks() {
			// remove add to cart button.
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
			// remove price.
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );

			add_action( 'woocommerce_product_meta_start', array( $this, 'show_packages_info' ), 10 );
		}

		// public function cfws_update_price( $cart_object ) {
		// 	$cart_items = $cart_object->cart_contents;
		// 	if ( ! empty( $cart_items ) ) {
		// 	  $price = 100;
		// 	  foreach ( $cart_items as $key => $value ) {
		// 		$value['data']->set_price( $price );
		// 	  }
		// 	}
		//   }
		public function show_packages_info() {

			do_shortcode( '[cfws_single_product]' );
		}

		public function cfws_save_offered_price( $cart_object ) {
					
			$quantity   = isset( $_REQUEST['qty'] ) ? $_REQUEST['qty'] : 1;
			$product_id = isset( $_REQUEST['product_id'] ) ? $_REQUEST['product_id'] : false;
			$offered_price = isset( $_REQUEST['offered_price'] ) ? $_REQUEST['offered_price'] : false;
			$cart_items = $cart_object->cart_contents;
			if ( ! empty( $cart_items ) ) {
			  
			  foreach ( $cart_items as $key => $value ) {
				if($value['data']->id == $product_id){
					$value['data']->set_price( $offered_price );
				}
			  }
			}
		}

		public function cfws_add_item_data($cart_item_data, $product_id, $variation_id ) {

			global $woocommerce;

			$offered_price = filter_input( INPUT_POST, 'offered_price' );

			if ( empty( $offered_price ) ) {
				return $cart_item_data;
			}
		
			$cart_item_data['offered_price'] = $offered_price;
		
			return $cart_item_data;
		
			$new_value = array();
			$new_value['_custom_options'] = $_POST['custom_options'];
		
			if(empty($cart_item_data)) {
				return $new_value;
			} else {
				return array_merge($cart_item_data, $new_value);
			}
		}

		public function single_product_shortcode_callback() {

			if ( is_admin() ) {
				return;
			}

			require_once CFWS_TEMP_DIR . '/single-product-packages.php';
		}


	}

	new CFWS_SINGLE_PRODUCT();
}
