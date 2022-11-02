<?php

global $post, $product;
$packages    = get_post_meta( $post->ID, 'cfws_packages', true );
$costPerItem = get_post_meta( $post->ID, 'cfws_unit_cost', true );
$unitQunatity = get_post_meta( $post->ID, 'cfws_unit_quantity', true );
$profit = $product->regular_price-$costPerItem;
$maxValue = $packages[count($packages)-1]['max'];
$price = $product->get_price();
?>
<div class="cfws_wrapper">
	<div class="cfws_sale_unit">
		<div class="cfws_div_1">SALE UNIT</div>
		<div class="cfws_div_2">Package</div>
		<div class="cfws_div_1">Quantity per unit</div>
		<div class="cfws_div_2"><strong>= <?php echo $unitQunatity ?> pieces</strong></div>
		<hr>
	</div>
	<div class="cfws_price_packages">
		<h4>Price</h4>
		<input type="hidden" id="max_package" name="max_package" value="<?php echo $maxValue ?>" />
		<?php foreach($packages as $package){ ?>
			<div class="cfws_div_1"><?php echo $package['min'] ?> - <?php echo $package['max'] ?></div>
			<?php
				
				if($package['discount_type'] == 'percent'){
					$discount_price = $price-(($profit/100)*$package['discount']);
				}
				else{ 	
					$discount_price = $price-($profit-$package['discount']);
				}
			?>
			<div class="cfws_div_2"><strong><?php echo $discount_price ?></strong> <?php echo get_option('woocommerce_currency') ?><br/>Per Package</div>
			<hr class="mb-0" />
		<?php } ?>
		<div class="cfws_div_1">More than <?php echo $maxValue ?></div>
		<div class="cfws_div_2">REQUEST QUOTATION</div>
	</div>
	<div class="cfws_quantity">
		<div class="cfws_offered_price">
			<h4>Quantity</h4>
			<div class="cfws_div_1">Unit quantity Package</div>
			<div class="cfws_div_2"><strong class="cfws_unit_quantity"><?php echo $unitQunatity ?></strong></div>
			<hr />
			<div class="cfws_div_1">Quantity</div>
			<div class="cfws_div_2"><strong id="cfws_product_quantity">1</strong></div>
			<hr class="mb-0" />
			<div class="cfws_div_1">Unit price</div>
			<div class="cfws_div_2"><strong id="cfws_product_unit_price"><?php echo $price ?></strong></div>
			<hr class="mb-0" />
			<div class="cfws_div_1">Total</div>
			<div class="cfws_div_2"><strong id="cfws_product_total_price"><?php echo $price ?></strong></div>
			<hr class="mb-0" />
		</div>
	</div>
	<div class="cfws_final total">
		
	</div>
</div>