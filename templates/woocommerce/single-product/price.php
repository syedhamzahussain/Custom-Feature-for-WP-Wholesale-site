<?php

/**
 * Single Product Price
 */

global $post, $product;
$packages    = get_post_meta( $post->ID, 'cfws_packages', true );
$costPerItem = get_post_meta( $post->ID, 'cfws_unit_cost', true );
// print_r($costPerItem); exit;

// $costPerItem = 31;
$profit = $product->regular_price - $costPerItem;
// $total = [
// ["min"=>1,"max"=>30,"discount_type"=>'percent',"discount"=>5],
// ["min"=>31,"max"=>60,"discount_type"=>'percent',"discount"=>10],
// ["min"=>61,"max"=>90,"discount_type"=>'fixed',"discount"=>2]
// ];
$lastvalue = count( $packages ) - 1;
$maxValue  = $packages[ $lastvalue ]['max'];
?> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<p itemprop="price" class="price"><?php echo $product->get_price_html(); ?></p> 
<input type="hidden" id="max_package" name="max_package" value="<?php echo $maxValue; ?>" />
<table>
	<?php foreach ( $packages as $package ) { ?>
	<tr>
		<td><?php echo $package['min']; ?> - <?php echo $package['max']; ?></td>
		<?php
		$salePrice = $product->regular_price;
		if ( $package['discount_type'] == 'percent' ) {
			$salePrice = $product->regular_price - ( ( $profit / 100 ) * $package['discount'] );
		} else {
			$salePrice = $product->regular_price - ( $profit - $package['discount'] );
		}
		?>
		<td><?php echo $salePrice; ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td>Above <?php echo $maxValue; ?></td>
		<td>Request Quotation</td>
	</tr>
</table>
<input type="text" id="your_price" placeholder="Quote your price" name="your_price" value="" style="display:none" />
<script>
$(document).ready(function(){
	$(".qty").change(function(){
		var max = $("#max_package").val();
		if($(this).val() > max){
			$("#your_price").show();
		}
		else{
			$("#your_price").hide();
		}
	});
})
</script>
