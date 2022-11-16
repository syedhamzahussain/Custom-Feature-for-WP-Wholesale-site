<?php
/**
 * Class Admin Order.
 *
 * @package cfws
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'CFWS_ADMIN_ORDER' ) ) {

	/**
	 * Class CFWS_ADMIN_ORDER.
	 */
	class CFWS_ADMIN_ORDER {

		/**
		 * Action for add new status .
		 */
		public function __construct() {
			add_action( 'admin_init', array( $this, 'cfws_pending_review_order_status' ) );
			add_filter( 'wc_order_is_editable', array($this, 'cfws_wc_order_is_editable'), 10, 2);
			add_action('woocommerce_admin_order_item_headers', array($this,'cfws_admin_order_item_custom_header'));
			add_action('woocommerce_admin_order_item_values', array($this, 'cfws_admin_order_item_custom_value'), 10, 3);
			add_action( 'wp_ajax_cfws_update_order_item_unit_cost', 'cfws_update_order_item_unit_cost' );
		}


		
		public function cfws_admin_order_item_custom_header() {
			// set the column name
			$column_name = 'Unit Cost';
		
			echo '<th>' . $column_name . '</th>';
		}

		public function cfws_admin_order_item_custom_value($_product, $item, $item_id = null) {
			// print_r($item->get_meta('unit_cost')); die();
			if(!empty($item->get_meta('unit_cost'))){
				$value = $item->get_meta('unit_cost');
				echo '<td>' . $value . '</td>';
			}else{

				$value = get_post_meta( $_product->post->ID, 'cfws_unit_cost', true );
				echo '<td><input name="cfws_custom_unit_cost_of_' . $_product->post->ID . '" type="number" step="0.01" value="' . $value . '"  id="custom_value_with_edit"/></td>';
			}
		
			
		}

		public function cfws_wc_order_is_editable( $editable, $order ) {
			// Compare
			// print_r($order->get_status()); die();
			if ( $order->get_status() == 'pending-review' ) {
				$editable = true;
			}
			
			return $editable;
		}


		public function cfws_pending_review_order_status() {
			register_post_status(
				'wc-pending-review',
				array(
					'label'                     => 'Pending Review',
					'public'                    => true,
					'show_in_admin_status_list' => true,
					'show_in_admin_all_list'    => true,
					'exclude_from_search'       => false,
					'label_count'               => _n_noop( 'Pending Review <span class="count">(%s)</span>', 'Pending Review <span class="count">(%s)</span>' ),
				)
			);

			add_filter( 'wc_order_statuses', array( $this, 'cfws_add_status_to_list' ) );
		}

		public function cfws_add_status_to_list( $order_statuses ) {
			$order_statuses['wc-pending-review'] = 'Pending Review';
			return $order_statuses;
		}


	}
}

new CFWS_ADMIN_ORDER();
