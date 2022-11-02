<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function getPriceByQuantity($quantity,$product_id){
	
	$packages    = get_post_meta( $product_id, 'cfws_packages', true );
	$costPerItem = get_post_meta( $product_id, 'cfws_unit_cost', true );
	$unitQunatity = get_post_meta( $product_id, 'cfws_unit_quantity', true );
	$product = wc_get_product($product_id);
	$price = $product->get_price();
	$profit = $price-$costPerItem;

	foreach($packages as $package){

		if($package['min'] <= $quantity && $package['max'] >= $quantity){
			
			if($package['discount_type'] == 'percent'){
				$price = $price-(($profit/100)*$package['discount']);
			}
			else{ 	
				$price = $price-($profit-$package['discount']);
			}
		}
	}
	return $price;
}