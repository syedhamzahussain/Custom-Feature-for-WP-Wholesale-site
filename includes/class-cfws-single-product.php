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
			//wc_get_template('woocommerce',[],CFWS_TEMP_DIR . '/woocommerce/cart/cart.php');
			remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

		}

		public function front_hooks() {
			// remove sidebar on cart page.
			add_action('woocommerce_before_main_content', array($this, 'remove_sidebar') );
			// remove add to cart button.
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
			// remove price.
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );

			add_action( 'woocommerce_product_meta_start', array( $this, 'show_packages_info' ), 10 );
			add_filter( 'woocommerce_locate_template', array( $this, 'cfws_cart_page_override' ), 10, 3 );
		}
		public function remove_sidebar()
		{
			if( is_checkout() || is_cart() || is_product()) { 
			 remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
		   }
		}
		public function cfws_cart_page_override( $template, $template_name, $template_path ) {
 
			if ( 'cart.php' === basename( $template ) ) {
				$template = CFWS_TEMP_DIR . '/woocommerce/cart/cart.php';
			}

			return $template;

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
		public function cfws_remove_sidebar(){
		if(is_cart() ){
			remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
		}
	}
		public function show_packages_info() {

			do_shortcode( '[cfws_single_product]' );
		}
		public function cfws_change_cart_page() {

			if ( is_admin() ) {
				return;
			}
			require_once CFWS_TEMP_DIR . '/woocommerce/cart/cart.php';
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

		// function add_custom_price( $cart_object ) {
		// 	$custom_price = 10; // This will be your custome price  
		// 	foreach ( $cart_object->cart_contents as $key => $value ) {
		// 		$value['data']->price = $custom_price;
		// 		// for WooCommerce version 3+ use: 
		// 		// $value['data']->set_price($custom_price);
		// 	}
		// }
		public function cfws_add_item_data($cart_item_data, $product_id, $variation_id ) {

			global $woocommerce;

			$offered_price = filter_input( INPUT_POST, 'offered_price' );
			print_r($offered_price); exit;
			// if ( empty( $offered_price ) ) {
			// 	return $cart_item_data;
			// }
		
			$cart_item_data['offered_price'] = $offered_price;
		
			return $cart_item_data;
		}

		public function single_product_shortcode_callback() {

			if ( is_admin() ) {
				return;
			}

			require_once CFWS_TEMP_DIR . '/single-product-packages.php';
		}

		public function cart_shortcode_callback() {

			if ( is_admin() ) {
				return;
			}

			require_once CFWS_TEMP_DIR . '/cart.php';
		}


	}

	new CFWS_SINGLE_PRODUCT();
}
