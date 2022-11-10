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
			add_action( 'wp_ajax_cfws_place_order', 'cfws_place_order' );
			add_action( 'wp_ajax_nopriv_cfws_place_order', 'cfws_place_order' );
			// add_filter( 'wc_order_statuses', array($this, 'cfws_add_status_to_list' ));
			

			add_action( 'init', array( $this, 'cfws_pending_review_order_status' ));
		}

		public function front_hooks() {

			// remove add to cart button.
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
			// remove price.
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );

			add_action( 'woocommerce_product_meta_start', array( $this, 'show_packages_info' ), 10 );
			add_filter( 'woocommerce_locate_template', array( $this, 'cfws_cart_page_override' ), 10, 3 );

			add_action( 'init', array( $this, 'cfws_pending_review_order_status' ));

		}
		public function cfws_cart_page_override( $template, $template_name, $template_path ) {
 
			if ( 'cart.php' === basename( $template ) ) {
				$template = CFWS_TEMP_DIR . '/woocommerce/cart/cart.php';
			}
			return $template;
		}
		// Add registered status to list of WC Order statuses

		// public function cfws_add_status_to_list( $order_statuses ) {




		// 	$order_statuses[ 'wc-pending-review' ] = 'Pending Review';
		// 	return $order_statuses;

		// }
		public function cfws_pending_review_order_status() {
			register_post_status( 'wc-pending-review', array(
				'label'                     => 'Pending Review',
				'public'                    => true,
				'show_in_admin_status_list' => true,
				'show_in_admin_all_list'    => true,
				'exclude_from_search'       => false,
				'label_count'               => _n_noop( 'Pending Review <span class="count">(%s)</span>', 'Pending Review <span class="count">(%s)</span>' )
			) );
		}


		public function show_packages_info() {
			global $product;
			if($product->get_type() == 'simple'){

				do_shortcode( '[cfws_single_product]' );
			}
		}
		public function cfws_change_cart_page() {

			if ( is_admin() ) {
				return;
			}
			require_once CFWS_TEMP_DIR . '/woocommerce/cart/cart.php';
		}

		public function cfws_add_item_data($cart_item_data, $product_id, $variation_id ) {

			global $woocommerce;

			$offered_price = filter_input( INPUT_POST, 'offered_price' );
			// print_r($offered_price); exit;
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
