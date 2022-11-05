<?php
/**
 * CFWS_ADDRESS_PRODUCT loader Class File.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;

}

if ( ! class_exists( 'CFWS_ADDRESS_PRODUCT' ) ) {

	/**
	 * CFWS_ADDRESS_PRODUCT class.
	 */
	class CFWS_ADDRESS_PRODUCT {

		/**
		 * Function Constructor.
		 */
		public function __construct() {

			add_action( 'woocommerce_customer_save_address', array( $this, 'custom_validation' ), 10, 2 );
			add_filter( 'woocommerce_locate_template', array( $this, 'intercept_wc_template' ), 10, 3 );
			add_action( 'wp_ajax_cfws_set_default_address_ajax', 'cfws_set_default_address_ajax' );

		}

		function intercept_wc_template( $template, $template_name, $template_path ) {

			if ( 'my-address.php' === basename( $template ) ) {
				$template = CFWS_TEMP_DIR . '/woocommerce/myaccount/my-address.php';
			} elseif ( 'form-edit-address.php' === basename( $template ) ) {
				$template = CFWS_TEMP_DIR . '/woocommerce/myaccount/form-edit-address.php';
			}

			return $template;

		}

		public function update_fields( $post_req, $slug ) {
			$updated_address                          = array();
			$updated_address['id']                    = ( isset( $post_req['id'] ) ? $post_req['id'] : uniqid() );
			$updated_address[ $slug . '_address_name' ]= $post_req[ $slug . '_address_name' ];
			$updated_address[ $slug . '_first_name' ] = $post_req[ $slug . '_first_name' ];
			$updated_address[ $slug . '_last_name' ]  = $post_req[ $slug . '_last_name' ];
			$updated_address[ $slug . '_company' ]    = $post_req[ $slug . '_company' ];
			$updated_address[ $slug . '_address_1' ]  = $post_req[ $slug . '_address_1' ];
			$updated_address[ $slug . '_address_2' ]  = $post_req[ $slug . '_address_2' ];
			$updated_address[ $slug . '_city' ]       = $post_req[ $slug . '_city' ];
			$updated_address[ $slug . '_state' ]      = $post_req[ $slug . '_state' ];
			$updated_address[ $slug . '_postcode' ]   = $post_req[ $slug . '_postcode' ];
			$updated_address[ $slug . '_country' ]    = $post_req[ $slug . '_country' ];
			$updated_address[ $slug . '_email' ]      = $post_req[ $slug . '_email' ];
			$updated_address[ $slug . '_phone' ]      = $post_req[ $slug . '_phone' ];

			return $updated_address;
		}


		function custom_validation( $user_id, $load_address ) {
			if ( $user_id <= 0 ) {
				return;
			}

			if ( isset( $_POST['id'] ) ) {
				$user_id = get_current_user_id();
				if ( $load_address == 'billing' ) {

					$billing = get_user_meta( $user_id, 'billing_address' );

					$index = 0;
					foreach ( $billing[0] as $key => $b ) {
						if ( $b['id'] == $_POST['id'] ) {

							$updated_address      = $this->update_fields( $_POST, 'billing' );
							$billing[0][ $index ] = $updated_address;

							delete_user_meta( $user_id, 'billing_address' );
							update_user_meta( $user_id, 'billing_address', $billing[0] );

						}
						$index++;
					}
				} else {
					$shipping = get_user_meta( $user_id, 'shipping_address' );
					$index    = 0;
					foreach ( $shipping[0] as $key => $b ) {
						if ( $b['id'] == $_POST['id'] ) {

							$updated_address = $this->update_fields( $_POST, 'shipping' );

							$shipping[0][ $index ] = $updated_address;
							delete_user_meta( $user_id, 'shipping_address' );
							update_user_meta( $user_id, 'shipping_address', $shipping[0] );

						}

						$index++;
					}
				}
			} else {
				if ( $load_address == 'billing' ) {
					$billing = get_user_meta( $user_id, 'billing_address' );

					$updated_address = $this->update_fields( $_POST, 'billing' );

					if ( count( $billing ) == 0 ) {

						$billing[] = $updated_address;
						update_user_meta( $user_id, 'billing_address', $billing );

					} else {

						$new_arr = array();
						foreach ( $billing[0] as $bil ) {
							  $new_arr[] = $bil;
						}
						$new_arr[] = $updated_address;

						delete_user_meta( $user_id, 'billing_address' );
						update_user_meta( $user_id, 'billing_address', $new_arr );

					}
				} else {
					$shipping = get_user_meta( $user_id, 'shipping_address' );

					$updated_address = $this->update_fields( $_POST, 'shipping' );

					if ( count( $shipping ) == 0 ) {

						$shipping[] = $updated_address;
						update_user_meta( $user_id, 'shipping_address', $shipping );

					} else {

						$new_arr = array();
						foreach ( $shipping[0] as $bil ) {
							$new_arr[] = $bil;
						}
						$new_arr[] = $updated_address;

						delete_user_meta( $user_id, 'shipping_address' );
						update_user_meta( $user_id, 'shipping_address', $new_arr );

					}
				}
			}
		}





	}

	new CFWS_ADDRESS_PRODUCT();
}
