<?php
/**
 * Edit address form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-edit-address.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

$page_title       = ( 'billing' === $load_address ) ? esc_html__( 'Billing address', 'woocommerce' ) : esc_html__( 'Shipping address', 'woocommerce' );
$customer_id      = get_current_user_id();
$filtered_address = [];
if ( 'billing' === $load_address ) {
	if ( isset($_GET['id']) ) {

		$billings = get_user_meta( $customer_id, 'billing_address' );

		foreach ( $billings[0] as $billing ) {
			if ( $billing['id'] == $_GET['id'] ) {
				$filtered_address = $billing;
			}
		}
	}
} 

do_action( 'woocommerce_before_edit_account_address_form' ); ?>

<?php if ( ! $load_address ) : ?>
	<?php wc_get_template( 'myaccount/my-address.php' ); ?>
<?php else : ?>

	<form method="post" >

		<h3><?php echo apply_filters( 'woocommerce_my_account_edit_address_title', $page_title, $load_address ); ?></h3><?php // @codingStandardsIgnoreLine ?>

		<div class="woocommerce-address-fields">
			<?php do_action( "woocommerce_before_edit_address_form_{$load_address}" ); ?>

			<div class="woocommerce-address-fields__field-wrapper">
				<p class="form-row form-row-wide" id="billing_address_name" data-priority="10">
					<label for="billing_address_name" class=""><?php echo esc_html__( 'Address Title', 'woocommerce' ); ?></label>
					<span class="woocommerce-input-wrapper"><input type="text" class="input-text " name="billing_address_name" id="billing_address_name" placeholder="" value="<?php (!empty($filtered_address) ?  $filtered_address['address_name'] : '' ) ?>">
						<div data-lastpass-icon-root="true" style="position: relative !important; height: 0px !important; width: 0px !important; float: left !important;"></div>
					</span>
				</p>
				<?php
				foreach ( $address as $key => $field ) {
					
					if ( ! empty( $filtered_address ) ) {
							$value = $filtered_address[ $key ];

					} else {
						$value = '';
					}
					woocommerce_form_field( $key, $field, wc_get_post_data_by_key( $key, $value ) );
				}
				?>
			</div>

			<?php do_action( "woocommerce_after_edit_address_form_{$load_address}" ); ?>

			<p>
				<button type="submit" class="button" name="save_address" value="<?php esc_attr_e( 'Save address', 'woocommerce' ); ?>"><?php esc_html_e( 'Save address', 'woocommerce' ); ?></button>
				<?php wp_nonce_field( 'woocommerce-edit_address', 'woocommerce-edit-address-nonce' ); ?>
				<input type="hidden" name="action" value="edit_address" />
				<?php
				if ( isset( $_GET['id'] ) ) {
					?>
						<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
					<?php
				}
				?>
			</p>
		</div>

	</form>

<?php endif; ?>

<?php do_action( 'woocommerce_after_edit_account_address_form' ); ?>
