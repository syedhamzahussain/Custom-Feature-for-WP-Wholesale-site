<?php
/**
 * CFWS_ACCOUNT_ORDERS loader Class File.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;

}

if ( ! class_exists( 'CFWS_ACCOUNT_ORDERS' ) ) {

	/**
	 * CFWS_ACCOUNT_ORDERS class.
	 */
	class CFWS_ACCOUNT_ORDERS {

		/**
		 * Function Constructor.
		 */
		public function __construct() {

			add_action( 'woocommerce_before_account_orders', array( $this, 'before_account_orders_action' ) );
			add_action( 'woocommerce_before_account_orders', array( $this, 'before_account_orders_action' ) );

			add_action( 'wp_ajax_cfws_place_order', array( $this, 'cfws_place_order' ) );
			add_action( 'wp_ajax_nopriv_cfws_place_order', array( $this, 'cfws_place_order' ) );

			// add_action( 'wp_ajax_cfws_place_order_with_slip', array( $this, 'cfws_place_order_with_slip' ) );
			// add_action( 'wp_ajax_nopriv_cfws_place_order_with_slip', array( $this, 'cfws_place_order_with_slip' ) );

			// Add order item meta.
			add_action( 'woocommerce_add_order_item_meta', array( $this, 'add_order_item_meta' ),10, 3 );
			
			
			// Add checkout process validation.
			// add_action( 'woocommerce_checkout_process', array( $this, 'cfws_place_order_slip_validation' ),10, 3 );
			add_action('woocommerce_checkout_process', 'my_custom_checkout_field_process');

			add_filter( 'woocommerce_locate_template', array( $this, 'cfws_pay_for_order_override' ), 10, 3 );
		}
		public function cfws_pay_for_order_override( $template, $template_name, $template_path ) {

			if ( 'form-pay.php' === basename( $template ) ) {
				$template = CFWS_TEMP_DIR . '/woocommerce/checkout/form-pay.php';
			}
			return $template;
		}
		public function add_order_item_meta ( $item_id, $cart_item, $cart_item_key ) {
			if ( isset( $cart_item[ 'offered_price' ] ) && $cart_item[ 'offered_price' ] != 'false' ) {
				wc_add_order_item_meta( $item_id, __( "Offered Unit Price", "cfws"), $cart_item[ 'offered_price' ] );
			}
		}

		/**
		 * Function for `woocommerce_before_account_orders` action-hook.
		 *
		 * @param  $has_orders
		 *
		 * @return void
		 */
		function before_account_orders_action( $has_orders ) {

			$orders = wc_get_orders(
				array(
					'limit'       => -1,
					'customer_id' => get_current_user_id(),
				)
			);

			$total_amount = 0;

			if ( $orders ) {
				foreach ( $orders as $order ) {
					$total_amount += $order->get_total();
				}
			}

			$args_quotes     = array(
				'status'      => array( 'wc-pending-review' ),
				'limit'       => -1,
				'customer_id' => get_current_user_id(),
			);
			$args_pending    = array(
				'status'      => array( 'wc-pending' ),
				'customer_id' => get_current_user_id(),
				'limit'       => -1,
			);
			$args_completed  = array(
				'status'      => array( 'wc-completed' ),
				'customer_id' => get_current_user_id(),
				'limit'       => -1,
			);
			$total_quotes    = count( wc_get_orders( $args_quotes ) );
			$total_pending   = count( wc_get_orders( $args_pending ) );
			$total_completed = count( wc_get_orders( $args_completed ) );

			require_once CFWS_TEMP_DIR . '/my-orders-cards.php';

		}
		public function cfws_place_order_slip_validation() {
			$order_id   = $_REQUEST['order_id']; 	
			$order    = wc_get_order( $order_id );

			if (empty( $_REQUEST['cfws_payment_file']['name'] ) ) {
				wc_add_notice( __( 'Your phone number is wrong.' ), 'error' );
			}
			// return wp_send_json($_REQUEST['cfws_payment_file']['name']);

			if ( ! empty( $_FILES['cfws_payment_file']['name'] ) ) {
				require_once ABSPATH . 'wp-admin/includes/image.php';
				require_once ABSPATH . 'wp-admin/includes/file.php';
				require_once ABSPATH . 'wp-admin/includes/media.php';
				$file_id = media_handle_upload( 'cfws_payment_file', 0 );
				$order->update_post_meta( 'cfws_payment_file', $file_id );
				$order->save();

				// update_post_meta( $order_id, 'cfws_payment_file', $file_id );
				return wp_send_json(1);
			}
			return wp_send_json(0);
		}
		public function cfws_place_order() {

			$user_id = get_current_user_id();
			// wp_send_json($user_id);
			// get billing and shipping addresses
			$billing_address   = $_REQUEST['billing_address'];
			$shippping_address = $_REQUEST['shippping_address'];
			// set default billing and shipping addresses
			cfws_set_default_address( 'billing', $_REQUEST['billing_address'] );
			cfws_set_default_address( 'shippping', $_REQUEST['shippping_address'] );

			$cart     = WC()->cart;
			$checkout = WC()->checkout();
			$order_id = $checkout->create_order( array() );
			$order    = wc_get_order( $order_id );

			update_post_meta( $order_id, '_customer_user', get_current_user_id() );
			$order->set_payment_method( 'bacs' );
			// $order->payment_complete();
			$default_billing_address  = get_user_meta( $user_id, 'billing_default_address', true );
			$default_shipping_address = get_user_meta( $user_id, 'shipping_default_address', true );
			$order->set_address( $default_billing_address, 'billing' );
			$order->set_address( $default_shipping_address, 'shipping' );
			$order->set_status( 'wc-pending-review' );
			$order->calculate_totals();
			$order->save();
			$cart->empty_cart();
			wp_send_json( $order->get_checkout_order_received_url() );
		}



	}

	new CFWS_ACCOUNT_ORDERS();
}
