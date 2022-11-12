<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
function cfws_add_item_data($cart_item_data, $product_id, $price) {

    global $woocommerce;
    $new_value = array();
    $new_value['_custom_options'] = $price;

    if(empty($cart_item_data)) {
        return $new_value;
    } else {
        return array_merge($cart_item_data, $new_value);
    }
}
function cfws_add_to_cart_ajax() {
	ob_start();
	$product_id   = isset( $_REQUEST['product_id'] ) ? $_REQUEST['product_id'] : 0;
	$quantity   = isset( $_REQUEST['qty'] ) ? $_REQUEST['qty'] : 1;
	$price   = isset( $_REQUEST['price'] ) ? $_REQUEST['price'] : 0;
	$offered_price   = isset( $_REQUEST['offered_price'] ) ? $_REQUEST['offered_price'] : false;
	$meta = [];
	$meta['offered_price'] = $offered_price;
	
	$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
	$product_status    = get_post_status( $product_id );
	if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity,0, array(),$meta ) && 'publish' === $product_status ) {
		do_action( 'woocommerce_ajax_added_to_cart', $product_id );
		
		return;
		
	} else {
		// If there was an error adding to the cart, redirect to the product page to show any errors
		$data = array(
			'error'       => true,
			'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id )
		);
		wp_send_json( $data );
		
	}
	wp_die();
}

function cfws_get_price_by_quantity( $quantity, $product_id ) {

	$product      = wc_get_product( $product_id );
	$packages     = !empty( get_post_meta( $product_id, 'cfws_packages', true )) ?  get_post_meta( $product_id, 'cfws_packages', true ) : array();
	$costPerItem  = !empty( get_post_meta( $product_id, 'cfws_unit_cost', true )) ?  get_post_meta( $product_id, 'cfws_unit_cost', true ) : $product->get_price();
	$unitQunatity = !empty( get_post_meta( $product_id, 'cfws_unit_quantity', true )) ?  get_post_meta( $product_id, 'cfws_unit_quantity', true ) : 1;
	$price        = $product->get_price();
	$profit       = $price - $costPerItem;
	if(!empty($packages) ){
		foreach ( $packages as $package ) {

			if ( $package['min'] <= $quantity && $package['max'] >= $quantity ) {

				if ( $package['discount_type'] == 'percent' ) {
					$price = $price - ( ( $profit / 100 ) * $package['discount'] );
				} else {
					$price = $price - ( $profit - $package['discount'] );
				}
			}
		}
	}
	$price = number_format( (float) $price, 2, '.', '' );

	return $price;

	wp_die();
}
function cfws_get_price_by_quantity_ajax() {
	$quantity   = isset( $_REQUEST['qty'] ) ? $_REQUEST['qty'] : 1;
	$product_id = isset( $_REQUEST['product_id'] ) ? $_REQUEST['product_id'] : false;
	$price      = 0;
	if ( $product_id ) {
		$packages     = get_post_meta( $product_id, 'cfws_packages', true );
		$costPerItem  = get_post_meta( $product_id, 'cfws_unit_cost', true );
		$unitQunatity = get_post_meta( $product_id, 'cfws_unit_quantity', true );
		$product      = wc_get_product( $product_id );
		$price        = $product->get_price();
		$profit       = $price - $costPerItem;
		$allPkgMax    = array();
		foreach ( $packages as $package ) {
			$allPkgMax[] = $package['max'];
			if ( $package['min'] <= $quantity && $package['max'] >= $quantity ) {

				if ( $package['discount_type'] == 'percent' ) {
					$price = $price - ( ( $profit / 100 ) * $package['discount'] );
				} else {
					$price = $price - ( $profit - $package['discount'] );
				}
			}
		}
	}
	$price    = number_format( (float) $price, 2, '.', '' );
	$response = array(
		'price'       => $price,
		'max_package' => max( $allPkgMax ),
	);
	wp_send_json( $response );

	wp_die();
}

function cfws_set_default_address($slug,$id){
	$user_id = get_current_user_id();

	$address = get_user_meta( $user_id, "billing_address" );

	$index = 0;
	foreach ( $address[0] as $key => $b ) {
		if ( $b['id'] == $id ) {
	
			update_user_meta( $user_id, $slug.'_default_address', $b );

		}
		$index++;
	}
	return;
	
}
function cfws_set_default_address_ajax(){
	$slug = $_REQUEST['slug'];
	$id = $_REQUEST['id'];
	$user_id = get_current_user_id();

	$address = get_user_meta( $user_id, "billing_address" );

	$index = 0;
	foreach ( $address[0] as $key => $b ) {
		if ( $b['id'] == $id ) {
	
			update_user_meta( $user_id, $slug.'_default_address', $b );

		}
		$index++;
	}

	wp_send_json( 1 );

	wp_die();
	
}
function cfws_delete_address_ajax(){
	
	$id = $_REQUEST['id'];
	$user_id = get_current_user_id();

	$address = get_user_meta( $user_id, "billing_address" );
	$default_billing_address = get_user_meta( $user_id, "billing_default_address" );
	$default_shipping_address = get_user_meta( $user_id, "shipping_default_address" );

	$new_arr = array();
	foreach ( $address[0] as $bil ) {
		if ($bil['id'] !== $id) {
			$new_arr[] = $bil;
		}
	}
	

	delete_user_meta( $user_id, 'billing_address' );
	update_user_meta( $user_id, 'billing_address', $new_arr );

	

	if($id == $default_billing_address[0]['id']){
		delete_user_meta( $user_id, 'billing_default_address' );
	}
	if($id == $default_shipping_address[0]['id']){
		delete_user_meta( $user_id, 'shipping_default_address' );
	}

	wp_send_json( 1 );

	wp_die();
	
}