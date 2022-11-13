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
