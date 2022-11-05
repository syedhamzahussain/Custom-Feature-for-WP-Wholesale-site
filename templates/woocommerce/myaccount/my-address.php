<?php
/**
 * My Addresses
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-address.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.6.0
 */

defined( 'ABSPATH' ) || exit;

$user_id = get_current_user_id();

$billing = get_user_meta( $user_id, 'billing_address' );
$default_billing_address = get_user_meta( $user_id, 'billing_default_address' );
$default_shipping_address = get_user_meta( $user_id, 'shipping_default_address' );

?>
<div>
	<p>
<?php echo apply_filters( 'woocommerce_my_account_my_address_description', esc_html__( 'The following addresses will be used on the checkout page by default.', 'woocommerce' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</p>
	<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', 'billing' ) ); ?>" class="add"><button class="btn btn-success"><?php echo esc_html__( 'Add Address', 'woocommerce' ); ?></button></a>
	
</div>
<?php
if ( isset( $billing ) && ! empty( $billing ) ) : 
?>
	<div class="accordion">
<?php
	 foreach ( $billing[0] as $key => $address ) :
?>
		<h1 id="accordionhead_<?= $key+1 ?>"><?= $address['billing_address_name'] ?></h1>
		<div id="accordioncontent_<?= $key+1 ?>" class="accordion-content">
				
			<address>
			<?php
				$formatted_address = implode( '<br>', array_slice(array_filter( $address ), 2 ));
				echo wp_kses_post( $formatted_address );
			?>
			</address>

			<section>
				<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', 'billing' ) ); ?>?id=<?php echo $address['id']; ?>" class="edit"><button><?php echo esc_html__( 'Edit Address', 'woocommerce' ); ?></button></a>
<?php			if($address['id'] !== $default_billing_address[0]['id']){ ?>
					<a href="#" class="set_as_default_billing_address" onclick="defaultAddressSet('<?php echo $address['id']; ?>','billing')"><button class="btn btn-primary"><?php echo esc_html__( 'Set as Default Billing Address', 'woocommerce' ); ?></button></a>
<?php			}				
				if($address['id'] !== $default_shipping_address[0]['id']){ ?>
					<a href="#" class="set_as_default_shipping_address" onclick="defaultAddressSet('<?php echo $address['id']; ?>','shipping')"><button class="btn btn-primary"><?php echo esc_html__( 'Set as Default Shipping Address', 'woocommerce' ); ?></button></a>
<?php			}
?>			
			</section>
		</div>
				
			

<?php
	 endforeach; ?>
	</div>
<?php
 else :
?>
	<h4><?php echo esc_html_e( 'You have not set up this type of address yet.', 'woocommerce' ); ?></h4>
	
<?php
endif;
?>
<br><br>
<div class="row" style="margin: 10px">
	<div class="card col-md-5">
		<div class="card-header">Default Billing Address</div>
		<div class="card-body">
<?php 	if( isset( $default_billing_address ) && ! empty( $default_billing_address )) : ?>
			<address>
<?php
			$formatted_address = implode( '<br>', array_slice(array_filter( $default_billing_address[0] ), 1 ));
			echo wp_kses_post( $formatted_address );
?>
			</address>
<?php 	else :
?>
	
		 <h4><?php echo esc_html_e( 'You have not set default billing address yet.', 'woocommerce' ); ?></h4>
	
<?php
		endif; 
?>
		</div>
	
	</div>
	
	<div class="card col-md-5" style="margin-right:10px">
		<div class="card-header">Default Shipping Address</div>
		<div class="card-body">
<?php if( isset( $default_shipping_address ) && ! empty( $default_shipping_address )) : ?>
			
			<address>
<?php
			$formatted_address = implode( '<br>', array_slice(array_filter( $default_shipping_address[0] ), 1 ));
			echo wp_kses_post( $formatted_address );
?>
			</address>
<?php else :
?>
	
		 <h4><?php echo esc_html_e( 'You have not set default shipping address yet.', 'woocommerce' ); ?></h4>
	
<?php
	endif;
?>
		</div>
	
	</div>
</div>




