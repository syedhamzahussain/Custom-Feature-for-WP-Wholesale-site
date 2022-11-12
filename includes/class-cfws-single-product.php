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
			
			add_action( 'wp_ajax_cfws_add_to_cart_ajax', 'cfws_add_to_cart_ajax' );
			add_action( 'wp_ajax_nopriv_cfws_add_to_cart_ajax', 'cfws_add_to_cart_ajax' );
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

		public function show_packages_info() {
			do_shortcode( '[cfws_single_product]' );
		}
		public function cfws_change_cart_page() {

			if ( is_admin() ) {
				return;
			}
			require_once CFWS_TEMP_DIR . '/woocommerce/cart/cart.php';
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
