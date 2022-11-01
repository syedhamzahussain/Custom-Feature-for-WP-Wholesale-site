<?php
/**
 * CFWS_LOADER loader Class File.
 *
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

			$this->includes();
			
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'front_assets' ) );

			add_shortcode('cfws_customization', array( $this, 'customization_shortcode' ) );
			
		}

		public function includes() {


		}

		public function admin_assets() {
			wp_enqueue_style( 'cfws-admin-style', CFWS_ASSETS_DIR_URL . '/css/admin/admin.css' );
			wp_enqueue_script( 'cfws-admin-script', CFWS_ASSETS_DIR_URL . '/js/admin/admin.js', array( 'jquery' ), rand() );
		}

		public function front_assets() {
			wp_enqueue_style( 'cfws-front-style', CFWS_ASSETS_DIR_URL . '/css/style.css' );
			wp_enqueue_script( 'cfws-front-script', CFWS_ASSETS_DIR_URL . '/js/script.js', array( 'jquery' ), rand() );

			// bootstrap
			wp_register_script('cfws-bootstrap-script', 
				'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js', 
				array ('jquery'), 
				false, false);

			wp_register_style( 'cfws-bootstrap-style', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css' );

			wp_enqueue_script('cfws-sweetalert2-script', 
				'//cdn.jsdelivr.net/npm/sweetalert2@11', 
				array ('jquery'), 
				false, false);

			wp_localize_script(
				'cfws-front-script',
				'cfws_obj',
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'is_logged' => is_user_logged_in(),
				)
			);
		}

		public function customization_shortcode() {

			if( is_admin() ){
				return;
			}
			
			require_once CFWS_TEMP_DIR . '/registration-shortcode.php';
		}


	}

	new CFWS_LOADER();
}