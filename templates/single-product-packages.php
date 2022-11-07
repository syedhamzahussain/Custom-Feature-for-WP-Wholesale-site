<?php

global $post, $product;
$packages     = get_post_meta( $post->ID, 'cfws_packages', true );
$costPerItem  = get_post_meta( $post->ID, 'cfws_unit_cost', true );
$unitQunatity = get_post_meta( $post->ID, 'cfws_unit_quantity', true );
$heading      = get_post_meta( $post->ID, 'cfws_heading', true );
if(isset($packages ) &&  $packages != ''){
	$maxValue     = $packages[ count( $packages ) - 1 ]['max'];
	$price        = $product->get_price();
	$profit       = $price - $costPerItem;
}
else{

	$maxValue     = '';
	$price        = '';
	$profit       = '';
}
?>
<div class="cfws_wrapper">
	<!-- <form class="cart" action="http://localhost/wordpress/product/hoodie-with-logo/" method="post" enctype="multipart/form-data"> -->
		<div class="cfws_sale_unit">
			<div class="cfws_div_1 cfws_sale">SALE UNIT</div>
			<div class="cfws_div_2 cfws_gray_color"><?php echo $heading; ?></div>
			<div class="cfws_div_1 cfws_gray_color">Quantity per unit</div>
			<div class="cfws_div_2"><strong class="cfws_font_bold">= <?php echo $unitQunatity; ?> pieces</strong></div>
			<hr>
		</div>
		<div class="cfws_price_packages">
			<h4 class="cfws_sale">Price</h4>
			<input type="hidden" id="max_package" name="max_package" value="<?php echo $maxValue; ?>" />
			<?php if(isset($packages ) &&  $packages != ''){ ?>
				<?php foreach ( $packages as $package ) { ?>
					<div class="cfws_div_1"><?php echo $package['min']; ?> - <?php echo $package['max']; ?></div>
					<?php

					if ( $package['discount_type'] == 'percent' ) {
						$discount_price = $price - ( ( $profit / 100 ) * $package['discount'] );
					} else {
						$discount_price = $price - ( $profit - $package['discount'] );
					}
						$discount_price = number_format( (float) $discount_price, 2, '.', '' );
					?>
					<div class="cfws_div_2 cfws_fs12"><strong class="cfws_font_bold cfws_fs18"><?php echo $discount_price; ?></strong> <?php echo get_option( 'woocommerce_currency' ); ?><br/>Per Package</div>
					<div class="cfws_border_dot"></div>

				<?php } ?>
			<div class="cfws_div_1">More than <?php echo $maxValue; ?></div>
			<div class="cfws_div_2">REQUEST QUOTATION</div>
			<?php } ?>
		</div>
		<div><input type="hidden" name="cfws_offered_price" id="cfws_offered_price" value=""></div>

		<div>
			<?php
			woocommerce_quantity_input(
				array(
					'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
					'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
					'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
				)
			);
			?>
		</div>
		<div class="cfws_quantity">
			<h4 class="cfws_d_inline">Quantity</h4> 
			<br>
			<br>
			<div class="cfws_offered_price">
				<h4 class="cfws_d_inline" for="offered_price">Offer your Price</h4>
				<input type="number" name="offered_price" id="offered_price"   />
				<br>
				<br>
			</div>
			<div class="cfws_div_1">Unit quantity Package</div>
			<div class="cfws_div_2"><strong class="cfws_unit_quantity"><?php echo $unitQunatity; ?></strong></div>
			<hr  class="mt10" />
			<div class="cfws_div_1">Quantity</div>
			<div class="cfws_div_2"><strong class="cfws_font_bold" id="cfws_product_quantity">1</strong>x</div>
			<hr class="mb-0" />
			<div class="cfws_div_1">Unit price</div>
			<div class="cfws_div_2"><strong class="cfws_font_bold" id="cfws_product_unit_price"><?php echo cfws_get_price_by_quantity( 1, $post->ID ); ?></strong>  <?php echo get_option( 'woocommerce_currency' ); ?></div>
			<hr class="mb-0" />
			<div class="cfws_div_1">Total</div>
			<div class="cfws_div_2"><strong class="cfws_font_bold" id="cfws_product_total_price"><?php echo cfws_get_price_by_quantity( 1, $post->ID ); ?></strong>  <?php echo get_option( 'woocommerce_currency' ); ?></div>
			<hr class="mb-0" />
		</div>
		<br>
		<div>
			<!-- <button type="submit" name="add-to-cart"  value="<?php //echo $post->ID ?>" class="single_add_to_cart_button button alt add_to_cart_button">Add to cart</button> -->
			<?php echo do_shortcode( '[add_to_cart id=' . $post->ID . ' style=”border:none; padding: 10px;”  show_price=false]' ); ?>
		</div>
		<div><input type="hidden" name="cfws_get_product_price" value="<?php echo $product->get_price(); ?>"></div>
	<!-- </form> -->
</div>
