<?php
/**
 * Class Admin Report.
 *
 * @package cfws
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'CFWS_ADMIN_REPORT' ) ) {

	/**
	 * Class CFWS_ADMIN_REPORT.
	 */
	class CFWS_ADMIN_REPORT {

		/**
		 * Action for report filter.
		 */
		public function __construct() {
			add_filter( 'woocommerce_admin_reports', array( $this, 'filter_report_company' ), 10, 1 );
			// add_filter( 'wc_admin_reports_path', array( $this, 'include_report_company_class' ), 10 );
		}

		/**
		 * Inject company report into WooCommerce reports.
		 *
		 * @since 1.1.1.0
		 * @param array $reports filter report.
		 * @return array
		 */
		public function filter_report_company( $reports ) {

			$custom_report = __( 'Revenue/Cost Report', 'cfws' );

			
			// $modify_sales_by_company_menu = apply_filters( 'wcb2brp_modify_sales_by_company_menu', $modify_sales_by_company_menu );

			$reports['orders']['reports']['sales_by_cost_revenue'] = array(
				'title'       => $custom_report,
				'description' => '',
				'hide_title'  => '1',
				'callback'    => array( 'WC_Admin_Reports', 'get_report' ),
			);
			return $reports;
		}

		/**
		 * Include report by company main file.
		 *
		 * @since 1.1.1.0
		 * @param string $path include path.
		 * @return string
		 */
		// public function include_report_company_class( $path ) {
		// 	if ( 'reports/class-wc-report-sales-by-company.php' == $path ) {
		// 		$path = WCB2BRP_ABSPATH . '/includes/admin/class-wc-report-sales-by-company.php';
		// 	}
		// 	return $path;
		// }

	}
}

new CFWS_ADMIN_REPORT();
