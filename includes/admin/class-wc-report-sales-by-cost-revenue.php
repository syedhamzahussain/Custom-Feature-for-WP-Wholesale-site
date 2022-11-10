<?php
/**
 * Class Reports Sales By Cost And Revenue.
 *
 * @package b2b-role-and-permission
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_Report_Sales_By_Cost_Revenue' ) && class_exists( 'WC_Admin_Report' ) ) {

	/**
	 * Class WC_Report_Sales_By_Cost_Revenue.
	 */
	class WC_Report_Sales_By_Cost_Revenue extends WC_Admin_Report {

		/**
		 * Chart colors.
		 *
		 * @var array
		 */
		public $chart_colours = array();

		/**
		 * Categories ids.
		 *
		 * @var array
		 */
		public $show_companies = array();

		/**
		 * Item sales.
		 *
		 * @var array
		 */
		private $item_sales = array();

		/**
		 * Item sales and times.
		 *
		 * @var array
		 */
		private $item_sales_and_times = array();


		/**
		 * Get all orders of company.
		 *
		 * @param  int $company Company ID.
		 * @return object
		 */
		public function get_company_orders( $company ) {
			$customers = get_users(
				array(
					'role'       => 'customer',
					'meta_key'   => 'wcb2brp_company',
					'meta_value' => $company,
				)
			);

			if ( $customers ) {
				foreach ( $customers as $customer ) {
					$ids[] = $customer->ID;
				}
				$query = new WC_Order_Query();
				$query->set( 'customer_id', $ids );
				return $query->get_orders();
			}
			return false;
		}

		/**
		 * Get the legend for the main chart sidebar.
		 *
		 * @return array
		 */
		public function get_chart_legend() {

			if ( empty( $this->show_companies ) ) {
				return array();
			}

			$legend = array();
			$index  = 0;

			foreach ( $this->show_companies as $company ) {
				$company_data = get_post( $company );
				$total        = 0;
				$orders       = $this->get_company_orders( $company );
				if ( $orders ) {
					foreach ( $orders as $order ) {
						if ( isset( $this->item_sales[ $order->id ] ) ) {
							$total += $this->item_sales[ $order->id ];
						}
					}
				}

				$legend[] = array(
					/* translators: 1: total items sold 2: category name */
					'title'            => sprintf( __( '%1$s sales in %2$s', 'wcb2brp' ), '<strong>' . wc_price( $total ) . '</strong>', $company_data->post_title ),
					'color'            => isset( $this->chart_colours[ $index ] ) ? $this->chart_colours[ $index ] : $this->chart_colours[0],
					'highlight_series' => $index,
				);

				$index++;
			}

			return $legend;
		}

		/**
		 * Output the report.
		 */
		public function output_report() {

			$ranges = array(
				'year'       => __( 'Year', 'wcb2brp' ),
				'last_month' => __( 'Last month', 'wcb2brp' ),
				'month'      => __( 'This month', 'wcb2brp' ),
				'7day'       => __( 'Last 7 days', 'wcb2brp' ),
			);

			$this->chart_colours = array( '#3498db', '#34495e', '#1abc9c', '#2ecc71', '#f1c40f', '#e67e22', '#e74c3c', '#2980b9', '#8e44ad', '#2c3e50', '#16a085', '#27ae60', '#f39c12', '#d35400', '#c0392b' );

			$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( wp_unslash( $_GET['range'] ) ) : '7day';

			if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ) ) ) {
				$current_range = '7day';
			}

			$this->check_current_range_nonce( $current_range );
			$this->calculate_current_range( $current_range );

			// Get item sales data.
			if ( ! empty( $this->show_companies ) ) {
				$orders = $this->get_order_report_data(
					array(
						'data'         => array(
							'ID'           => array(
								'type'     => 'post_data',
								'function' => '',
								'name'     => 'ID',
							),
							'_order_total' => array(
								'type'     => 'meta',
								'function' => 'SUM',
								'name'     => 'total_sales',
							),
							'post_date'    => array(
								'type'     => 'post_data',
								'function' => '',
								'name'     => 'post_date',
							),
						),
						'group_by'     => 'post_date',
						'query_type'   => 'get_results',
						'filter_range' => true,
					)
				);

				$this->item_sales           = array();
				$this->item_sales_and_times = array();

				if ( is_array( $orders ) ) {
					foreach ( $orders as $order ) {

						switch ( $this->chart_groupby ) {
							case 'day':
								$time = strtotime( current_time( 'Ymd', strtotime( $order->post_date ) ) ) * 1000;
								break;
							case 'month':
							default:
								$time = strtotime( current_time( 'Ym', strtotime( $order->post_date ) ) . '01' ) * 1000;
								break;
						}

						$this->item_sales_and_times[ $time ][ $order->ID ] = isset( $this->item_sales_and_times[ $time ][ $order->ID ] ) ? $this->item_sales_and_times[ $time ][ $order->ID ] + $order->total_sales : $order->total_sales;

						$this->item_sales[ $order->ID ] = isset( $this->item_sales[ $order->ID ] ) ? $this->item_sales[ $order->ID ] + $order->total_sales : $order->total_sales;
					}
				}
			}

			$legends = array();

			if ( ! empty( $this->show_companies ) ) {
				$legends = $this->get_chart_legend();
			}

			wc_get_template(
				'/admin/report-by-company.php',
				array(
					'ranges'                    => $ranges,
					'chart_colours'             => $this->chart_colours,
					'current_range'             => $current_range,
					'check_current_range_nonce' => $this->check_current_range_nonce( $current_range ),
					'calculate_current_range'   => $this->calculate_current_range( $current_range ),
					'show_companies'            => $this->show_companies,
					'orders'                    => $orders,
					'item_sales'                => $this->item_sales,
					'item_sales_and_times'      => $this->item_sales_and_times,
					'get_export_button'         => $get_export_button,
					'legends'                   => $legends,
					'get_chart_widgets'         => $get_chart_widgets,
					'get_chart_widgets'         => $this->get_chart_widgets(),
					'get_main_chart'            => $this->get_main_chart(),
				),
				WCB2BRP_PLUGIN_FOLDER,
				WCB2BRP_ABSPATH . '/templates/'
			);
		}

		/**
		 * Get chart widgets.
		 *
		 * @return array
		 */
		public function get_chart_widgets() {

			return array(
				array(
					'title'    => __( 'Companies', 'wcb2brp' ),
					'callback' => array( $this, 'company_widget' ),
				),
			);
		}

		/**
		 * Output category widget.
		 */
		public function company_widget() {
			$companies = wcb2brp_all_companies();
			?>
			<form method="GET">
				<div>
					<select multiple="multiple" data-placeholder="<?php esc_attr_e( 'Select Companies&hellip;', 'wcb2brp' ); ?>" class="wc-enhanced-select" id="show_companies" name="show_companies[]" style="width: 205px;">
						<?php
						if ( $companies ) {
							foreach ( $companies as $key => $value ) {
								?>
						<option value="<?php echo wp_kses_post( $value->ID ); ?>"<?php echo ( filter_input( INPUT_GET, 'show_companies' ) != null && in_array( $value->ID, filter_input( INPUT_GET, 'show_companies' ) ) ) ? esc_attr_e( ' selected' ) : ''; ?>><?php echo wp_kses_post( $value->post_title ); ?></option>
								<?php
							}
						}
						?>
					</select>
			<?php // @codingStandardsIgnoreStart ?>
			<a href="#" class="select_none"><?php esc_html_e( 'None', 'wcb2brp' ); ?></a>
			<a href="#" class="select_all"><?php esc_html_e( 'All', 'wcb2brp' ); ?></a>
			<button type="submit" class="submit button" value="<?php esc_attr_e( 'Show', 'wcb2brp' ); ?>"><?php esc_html_e( 'Show', 'wcb2brp' ); ?></button>
			<input type="hidden" name="range" value="<?php echo ( ! empty( filter_input( INPUT_GET, 'range' ) ) ) ? esc_attr( wp_unslash( filter_input( INPUT_GET, 'range' ) ) ) : ''; ?>" />
			<input type="hidden" name="start_date" value="<?php echo ( ! empty( filter_input( INPUT_GET, 'start_date' ) ) ) ? esc_attr( wp_unslash( filter_input( INPUT_GET, 'start_date' ) ) ) : ''; ?>" />
			<input type="hidden" name="end_date" value="<?php echo ( ! empty( filter_input( INPUT_GET, 'end_date' ) ) ) ? esc_attr( wp_unslash( filter_input( INPUT_GET, 'end_date' ) ) ) : ''; ?>" />
			<input type="hidden" name="page" value="<?php echo ( ! empty( filter_input( INPUT_GET, 'page' ) ) ) ? esc_attr( wp_unslash( filter_input( INPUT_GET, 'page' ) ) ) : ''; ?>" />
			<input type="hidden" name="tab" value="<?php echo ( ! empty( filter_input( INPUT_GET, 'tab' ) ) ) ? esc_attr( wp_unslash( filter_input( INPUT_GET, 'tab' ) ) ) : ''; ?>" />
			<input type="hidden" name="report" value="<?php echo ( ! empty( filter_input( INPUT_GET, 'report' ) ) ) ? esc_attr( wp_unslash( filter_input( INPUT_GET, 'report' ) ) ) : ''; ?>" />
			<?php // @codingStandardsIgnoreEnd ?>
				</div>
				<script type="text/javascript">
					jQuery(function () {
						// Select all/None
						jQuery('.chart-widget').on('click', '.select_all', function () {
							jQuery(this).closest('div').find('select option').attr('selected', 'selected');
							jQuery(this).closest('div').find('select').change();
							return false;
						});

						jQuery('.chart-widget').on('click', '.select_none', function () {
							jQuery(this).closest('div').find('select option').removeAttr('selected');
							jQuery(this).closest('div').find('select').change();
							return false;
						});
					});
				</script>
			</form>
			<?php
		}

		/**
		 * Output an export link.
		 */
		public function get_export_button() {

			$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( wp_unslash( $_GET['range'] ) ) : '7day';
			?>
			<a
				href="#"
				download="report-<?php echo esc_attr( $current_range ); ?>-<?php echo esc_attr( date_i18n( 'Y-m-d', current_time( 'timestamp' ) ) ); ?>.csv"
				class="export_csv"
				data-export="chart"
				data-xaxes="<?php esc_attr_e( 'Date', 'wcb2brp' ); ?>"
				data-groupby="<?php echo esc_attr( $this->chart_groupby ); ?>"
				>
					<?php esc_html_e( 'Export CSV', 'wcb2brp' ); ?>
			</a>
			<?php
		}

		/**
		 * Get the main chart.
		 */
		public function get_main_chart() {
			global $wp_locale;

			if ( empty( $this->show_companies ) ) {
				?>
				<div class="chart-container">
					<p class="chart-prompt"><?php esc_html_e( 'Choose an company to view stats', 'wcb2brp' ); ?></p>
				</div>
				<?php
			} else {
				$chart_data = array();
				$index      = 0;

				foreach ( $this->show_companies as $company ) {
					$company_data       = get_post( $company );
					$company_chart_data = array();

					for ( $i = 0; $i <= $this->chart_interval; $i ++ ) {

						$interval_total = 0;

						switch ( $this->chart_groupby ) {
							case 'day':
								$time = strtotime( current_time( 'Ymd', strtotime( "+{$i} DAY", $this->start_date ) ) ) * 1000;
								break;
							case 'month':
							default:
								$time = strtotime( current_time( 'Ym', strtotime( "+{$i} MONTH", $this->start_date ) ) . '01' ) * 1000;
								break;
						}
						$orders = $this->get_company_orders( $company );
						if ( $orders ) {
							foreach ( $orders as $order ) {
								if ( isset( $this->item_sales_and_times[ $time ][ $order->id ] ) ) {
									$interval_total += $this->item_sales_and_times[ $time ][ $order->id ];
								}
							}
						}

						$company_chart_data[] = array( $time, (float) wc_format_decimal( $interval_total, wc_get_price_decimals() ) );
					}

					$chart_data[ $company_data->ID ]['company'] = $company_data->post_title;
					$chart_data[ $company_data->ID ]['data']    = $company_chart_data;

					$index++;
				}
				?>
				<div class="chart-container">
					<div class="chart-placeholder main"></div>
				</div>
				<?php // @codingStandardsIgnoreStart ?>
			<script type="text/javascript">
			var main_chart;

			jQuery(function () {
				var drawGraph = function (highlight) {
				var series = [
				<?php
				$index = 0;
				foreach ( $chart_data as $data ) {
					$color = isset( $this->chart_colours[ $index ] ) ? wp_kses_post( $this->chart_colours[ $index ] ) : wp_kses_post( $this->chart_colours[0] );
					$width = $this->barwidth / count( $chart_data );
					$offset = ( $width * $index );
					$series = $data['data'];
					foreach ( $series as $key => $series_data ) {
						$series[ $key ][0] = $series_data[0] + $offset;
					}
					echo '{
label: "' . esc_js( $data['company'] ) . '",
data: jQuery.parseJSON( "' . json_encode( $series ) . '" ),
color: "' . esc_js( $color ) . '",
bars: {
fillColor: "' . esc_js( $color ) . '",
fill: true,
show: true,
lineWidth: 1,
align: "center",
barWidth: ' . esc_js( $width ) * 0.75 . ',
stack: false
},
' . wp_kses_post( $this->get_currency_tooltip() ) . ',
enable_tooltip: true,
prepend_label: true
},';
					$index++;
				}
				?>
				];

				if (highlight !== 'undefined' && series[ highlight ]) {
					highlight_series = series[ highlight ];

					highlight_series.color = '#9c5d90';

					if (highlight_series.bars) {
					highlight_series.bars.fillColor = '#9c5d90';
					}

					if (highlight_series.lines) {
					highlight_series.lines.lineWidth = 5;
					}
				}

				main_chart = jQuery.plot(
					jQuery('.chart-placeholder.main'),
					series,
						{
						legend: {
						show: false
						},
						grid: {
						color: '#aaa',
						borderColor: 'transparent',
						borderWidth: 0,
						hoverable: true
						},
						xaxes: [{
							color: '#aaa',
							reserveSpace: true,
							position: "bottom",
							tickColor: 'transparent',
							mode: "time",
							timeformat: "<?php echo ( 'day' === $this->chart_groupby ) ? '%d %b' : '%b'; ?>",
							monthNames: <?php echo json_encode( array_values( $wp_locale->month_abbrev ) ); ?>,
							tickLength: 1,
							minTickSize: [1, "<?php echo wp_kses_post( $this->chart_groupby ); ?>"],
							tickSize: [1, "<?php echo wp_kses_post( $this->chart_groupby ); ?>"],
							font: {
							color: "#aaa"
							}
						}],
						yaxes: [
						{
							min: 0,
							tickDecimals: 2,
							color: 'transparent',
							font: {color: "#aaa"}
						}
						],
					}
				);

				jQuery('.chart-placeholder').resize();

				}

				drawGraph();

				jQuery('.highlight_series').hover(
						function () {
					drawGraph(jQuery(this).data('series'));
					},
					function () {
					drawGraph();
					}
				);
			});
			</script>
				<?php // @codingStandardsIgnoreEnd ?>
				<?php
			}
		}

	}

}
