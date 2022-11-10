<?php
/**
 * Plugin Name: Custom Feature for WP Wholesale site
 * Plugin URI:  Solcoders.com
 * Description: Provide Wholesale site price contribution.
 * Version:     1.1.1.0
 * Author:      Solcoders
 * Author URI:  https://www.upwork.com/freelancers/syedhamzahussain
 * Text Domain: cfws
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'CFWS_PLUGIN_DIR' ) ) {
	define( 'CFWS_PLUGIN_DIR', __DIR__ );
}

if ( ! defined( 'CFWS_BASENAME' ) ) {
	define( 'CFWS_BASENAME', dirname( __FILE__ ) );
}

if ( ! defined( 'CFWS_PLUGIN_DIR_URL' ) ) {
	define( 'CFWS_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'CFWS_TEMP_DIR' ) ) {
	define( 'CFWS_TEMP_DIR', CFWS_PLUGIN_DIR . '/templates' );
}

if ( ! defined( 'CFWS_ASSETS_DIR_URL' ) ) {
	define( 'CFWS_ASSETS_DIR_URL', CFWS_PLUGIN_DIR_URL . 'assets' );
}

require_once CFWS_PLUGIN_DIR . '/helpers.php';
require_once CFWS_PLUGIN_DIR . '/includes/class-cfws-loader.php';

add_action( 'admin_init', 'cfws_pending_review_order_status');

function cfws_pending_review_order_status() {
            register_post_status( 'wc-pending-review', array(
                'label'                     => 'Pending Review',
                'public'                    => true,
                'show_in_admin_status_list' => true,
                'show_in_admin_all_list'    => true,
                'exclude_from_search'       => false,
                'label_count'               => _n_noop( 'Pending Review <span class="count">(%s)</span>', 'Pending Review <span class="count">(%s)</span>' )
            ) );

            add_filter( 'wc_order_statuses', 'cfws_add_status_to_list' );
}

function cfws_add_status_to_list( $order_statuses ) {
    $order_statuses[ 'wc-pending-review' ] = 'Pending Review';
    return $order_statuses;
}
