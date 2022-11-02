<?php

global $post, $product;
$packages    = get_post_meta( $post->ID, 'cfws_packages', true );
$costPerItem = get_post_meta( $post->ID, 'cfws_unit_cost', true );
$unitQunatity = get_post_meta( $post->ID, 'cfws_unit_quantity', true );
$profit = $product->regular_price-$costPerItem;
$maxValue = $packages[count($packages)-1]['max'];
?>
<div class="cfws_wrapper">
	<div class="cfws_sale_unit">
		<div class="cfws_div_1">SALE UNIT</div>
		<div class="cfws_div_2">Package</div>
		<div class="cfws_div_1">Quantity per unit</div>
		<div class="cfws_div_2"><strong>= <?= $unitQunatity ?> pieces</strong></div>
		<hr>
	</div>
	<div class="cfws_price_packages">
		<div class="cfws_div_1">Price</div>
		<input type="hidden" id="max_package" name="max_package" value="<?= $maxValue ?>" />
		<?php foreach($packages as $package){ ?>
		<div class="cfws_div_1"><?= $package['min'] ?> - <?= $package['max'] ?></div>
		<div class="cfws_div_2">Price</div>
		<?php
				$salePrice = $product->regular_price;
				if($package['discount_type'] == 'percent'){
					$salePrice = $product->regular_price-(($profit/100)*$package['discount']);
				}
				else{
					$salePrice = $product->regular_price-($profit-$package['discount']);
				}
				?>
		<table>
			<tr>
				<td><?= $package['min'] ?> - <?= $package['max'] ?></td>

				<td><?= $salePrice ?></td>
			</tr>
			<?php } ?>
			<tr>
				<td>Above <?= $maxValue ?></td>
				<td>Request Quotation</td>
			</tr>
		</table>
	</div>
	<div class="cfws_quantity">
		<div class="cfws_offered_price">
			
		</div>
	</div>
	<div class="cfws_final total">
		
	</div>
</div>