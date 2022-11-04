<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function cfws_get_price_by_quantity( $quantity, $product_id ) {

	$packages     = get_post_meta( $product_id, 'cfws_packages', true );
	$costPerItem  = get_post_meta( $product_id, 'cfws_unit_cost', true );
	$unitQunatity = get_post_meta( $product_id, 'cfws_unit_quantity', true );
	$product      = wc_get_product( $product_id );
	$price        = $product->get_price();
	$profit       = $price - $costPerItem;

	foreach ( $packages as $package ) {

		if ( $package['min'] <= $quantity && $package['max'] >= $quantity ) {

			if ( $package['discount_type'] == 'percent' ) {
				$price = $price - ( ( $profit / 100 ) * $package['discount'] );
			} else {
				$price = $price - ( $profit - $package['discount'] );
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
