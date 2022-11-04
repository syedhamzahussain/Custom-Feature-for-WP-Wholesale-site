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

$customer_id = get_current_user_id();

$billing = get_user_meta( $customer_id, 'billing_address' );



?>
<div style="float:left">
	<p>
		<?php echo apply_filters( 'woocommerce_my_account_my_address_description', esc_html__( 'The following addresses will be used on the checkout page by default.', 'woocommerce' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</p>
	<button><a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', 'billing' ) ); ?>" class="add"><?php echo esc_html__( 'Add Address', 'woocommerce' ); ?></a></button>
	
</div>

<?php if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) : ?>
<div class="u-columns woocommerce-Addresses col2-set addresses">
<?php endif; ?>
<div class="u-column-2 col-1 woocommerce-Address">
	

<?php
if ( isset( $billing ) && ! empty( $billing ) ) :
	
	foreach ( $billing[0] as $key => $address ) :
		?>
	
				<header class="woocommerce-Address-title title">
					<h3>Address <?php echo $key + 1; ?></h3>
					<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', 'billing' ) ); ?>?id=<?php echo $address['id']; ?>" class="edit"><?php echo esc_html__( 'Edit', 'woocommerce' ); ?></a>
				</header>
				<address>
				<?php
					unset( $address['id'] );
					$formatted_address = implode( '<br>', array_filter( $address ) );
					echo wp_kses_post( $formatted_address );
				?>
				</address>
			

		<?php
		endforeach;
	else :
		?>
	
		 <h4><?php echo esc_html_e( 'You have not set up this type of address yet.', 'woocommerce' ); ?></h4>
	
		<?php
	endif;
	?>
</div>


</div>

<?php if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) : ?>
	</div>
	<?php
endif;
