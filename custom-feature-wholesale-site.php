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
require_once CFWS_PLUGIN_DIR . '/includes/class-cfws-admin-product.php';
require_once CFWS_PLUGIN_DIR . '/includes/class-cfws-loader.php';
require_once CFWS_PLUGIN_DIR . '/includes/class-cfws-single-product.php';
