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

			add_action( 'woocommerce_before_account_orders', array($this,'before_account_orders_action') );



		}

		

        /**
         * Function for `woocommerce_before_account_orders` action-hook.
         * 
         * @param  $has_orders 
         *
         * @return void
         */
        function before_account_orders_action( $has_orders ){
            $query = new WC_Order_Query( array(
                'limit' => -1,
                'return' => 'total',
            ) );
            $total_amount = 0;
            $orders = $query->get_orders();
            
            if(count($orders) > 0){
                foreach($orders as $order){
                    $total_amount += $order['total'];
                }
            }
            

            $args_quotes = array(
                'status' => array('wc-pending-review'),
            );
            $args_pending = array(
                'status' => array('wc-pending'),
            );
            $args_completed = array(
                'status' => array('wc-completed'),
            );
            $total_quotes = count(wc_get_orders( $args_quotes ));
            $total_pending = count(wc_get_orders( $args_pending ));
            $total_completed = count(wc_get_orders( $args_completed ));

            require_once(CFWS_TEMP_DIR . '/my-orders-cards.php');
          
        }


	}

	new CFWS_ACCOUNT_ORDERS();
}
