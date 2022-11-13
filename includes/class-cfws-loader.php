<?php
/**
 * CFWS_LOADER loader Class File.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;

}

if ( ! class_exists( 'CFWS_LOADER' ) ) {

	/**
	 * CFWS_LOADER class.
	 */
	class CFWS_LOADER {

		/**
		 * Function Constructor.
		 */
		public function __construct() {

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'front_assets' ) );
			add_action( 'plugin_loaded', array( $this, 'load_plugin_languages' ) );
			$this->includes();

		}

		public function includes() {
			if ( is_admin() ) {
				require_once CFWS_PLUGIN_DIR . '/includes/admin/class-cfws-admin-product.php';
				require_once CFWS_PLUGIN_DIR . '/includes/admin/class-cfws-admin-report.php';
				require_once CFWS_PLUGIN_DIR . '/includes/admin/class-cfws-admin-orders.php';
			}
				require_once CFWS_PLUGIN_DIR . '/includes/class-cfws-address.php';
				require_once CFWS_PLUGIN_DIR . '/includes/class-cfws-orders.php';
				require_once CFWS_PLUGIN_DIR . '/includes/class-cfws-single-product.php';
				require_once CFWS_PLUGIN_DIR . '/includes/class-cfws-cart.php';

		}

		public function load_plugin_languages() {
			load_plugin_textdomain( 'cfws', false, CFWS_PLUGIN_DIR . '/languages' );
		}

		public function admin_assets() {
			wp_enqueue_style( 'cfws-admin-style', CFWS_ASSETS_DIR_URL . '/css/admin/admin.css' );
			wp_enqueue_script( 'cfws-admin-script', CFWS_ASSETS_DIR_URL . '/js/admin/admin.js', array( 'jquery' ), rand() );
			// wp_enqueue_style( 'cfws-boxicons-style', 'https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css' );

			global $post;
			$is_product = false;

			if ( 'product' == get_post_type() ) {
				$is_product = true;
			}
			wp_localize_script(
				'cfws-admin-script',
				'cfws_obj',
				array(
					'is_product' => $is_product,

				)
			);
		}

		public function front_assets() {
			wp_enqueue_style( 'cfws-front-style', CFWS_ASSETS_DIR_URL . '/css/style.css' );
			wp_enqueue_style( 'cfws-boxicons-style', 'https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css' );

			wp_enqueue_script( 'cfws-front-script', CFWS_ASSETS_DIR_URL . '/js/script.js', array( 'jquery' ), rand() );

			// bootstrap
			wp_register_script(
				'cfws-bootstrap-script',
				'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js',
				array( 'jquery' ),
				false,
				false
			);

			wp_register_style( 'cfws-bootstrap-style', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css' );

			wp_enqueue_script(
				'cfws-sweetalert2-script',
				'//cdn.jsdelivr.net/npm/sweetalert2@11',
				array( 'jquery' ),
				false,
				false
			);
			global $post;
			$product_id = isset( $post ) ? $post->ID : false;

			$cart_page_id  = wc_get_page_id( 'cart' );
			$cart_page_url = $cart_page_id ? get_permalink( $cart_page_id ) : '';

			wp_localize_script(
				'cfws-front-script',
				'cfws_obj',
				array(
					'ajaxurl'        => admin_url( 'admin-ajax.php' ),
					'is_logged'      => is_user_logged_in(),
					'product_id'     => $product_id,
					'cart_page_url'  => $cart_page_url,
					'is_single_page' => is_single(),
				)
			);
		}



	}

	new CFWS_LOADER();
}
