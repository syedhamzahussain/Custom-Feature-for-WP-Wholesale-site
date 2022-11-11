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
		 * Item revenue.
		 *
		 * @var array
		 */
		private $revenue_sales = array();

		/**
		 * Item revenue and times.
		 *
		 * @var array
		 */
		private $revenue_sales_and_times = array();

		/**
		 * Item revenue.
		 *
		 * @var array
		 */
		private $cost_sales = array();

		/**
		 * Item revenue and times.
		 *
		 * @var array
		 */
		private $cost_sales_and_times = array();

		
		public function get_revenue_total($order_id = null){
			$args_completed = array(
                'status' => array('wc-completed'),
                'limit' => -1,
            );
			$total        	  = 0;
			if($order_id == null){
				$completed_orders = wc_get_orders( $args_completed );
			
			
				if($completed_orders){
					foreach ($completed_orders as $key => $order) {
						foreach($order->get_items() as $item){
							$cost = get_post_meta($item['product_id'],'cfws_unit_cost',true) ? get_post_meta($item['product_id'],'cfws_unit_cost',true) : 0;
							$product = wc_get_product( $item['product_id'] );
							
							$total += ($product->get_price() - floatval( $cost ) ) *$item['quantity'];
						}
						
					}
				}
			}else{
				$order = wc_get_order( $order_id );
				foreach($order->get_items() as $item){
					$cost = get_post_meta($item['product_id'],'cfws_unit_cost',true) ? get_post_meta($item['product_id'],'cfws_unit_cost',true) : 0;
					
					$product = wc_get_product( $item['product_id'] );
					
					$total += ( $product->get_price() - floatval($cost) ) * $item['quantity'];
				}
			}
            
			// var_dump($total); die();
			return $total;
		}

		public function get_cost_total($order_id = null){
			$args_completed = array(
                'status' => array('wc-completed'),
                'limit' => -1,
            );
			$total        	  = 0;
			if($order_id == null){
				$completed_orders = wc_get_orders( $args_completed );
				
				// var_dump($completed_orders); die();
				if($completed_orders){
					foreach ($completed_orders as $key => $order) {

						foreach ($order->get_items() as $item) {
							$cost = get_post_meta($item['product_id'],'cfws_unit_cost',true) ? get_post_meta($item['product_id'],'cfws_unit_cost',true) : 0;
							$total +=  floatval( $cost ) * $item['quantity'];
						}
						
						
					}
				}
			}else{
				$order = wc_get_order( $order_id );
				foreach ($order->get_items() as $item) {
					$cost = get_post_meta($item['product_id'],'cfws_unit_cost',true) ? get_post_meta($item['product_id'],'cfws_unit_cost',true) : 0;
					$total +=  floatval($cost) * $item['quantity'];
				}
			}
           
			
			
			return $total;
		}
		
		/**
		 * Get the legend for the main chart sidebar.
		 *
		 * @return array
		 */
		public function get_chart_legend() {


			$legend = array();
			

			
			
			$revenue = $this->get_revenue_total();
			$cost = $this->get_cost_total();

			$legend[] = array(
				/* translators: 1: total Revenue  */
				'title'            => sprintf( __( '%1$s %2$s', 'cfws' ), '<strong>' . wc_price( $revenue ) . '</strong>', "Total revenue of completed orders" ),
				'color'            => $this->chart_colours[0],
				'highlight_series' => 0,
			);

			$legend[] = array(
				/* translators: 1: total cost  */
				'title'            => sprintf( __( '%1$s %2$s', 'cfws' ), '<strong>' . wc_price( $cost ) . '</strong>', "Total cost of completed orders" ),
				'color'            => $this->chart_colours[1],
				'highlight_series' => 1,
			);

			

			return $legend;
		}

		/**
		 * Output the report.
		 */
		public function output_report() {

			$ranges = array(
				'year'       => __( 'Year', 'cfws' ),
				'last_month' => __( 'Last month', 'cfws' ),
				'month'      => __( 'This month', 'cfws' ),
				'7day'       => __( 'Last 7 days', 'cfws' ),
			);

			$this->chart_colours = array( '#3498db', '#34495e' );

			$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( wp_unslash( $_GET['range'] ) ) : '7day';

			if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ) ) ) {
				$current_range = '7day';
			}

			$this->check_current_range_nonce( $current_range );
			$this->calculate_current_range( $current_range );

			// Get item sales data.
			
			$args_completed = array(
                'status' => array('wc-completed'),
                'limit' => -1,
            );
            $orders = wc_get_orders( $args_completed );
			

			if ( is_array( $orders ) ) {
				foreach ( $orders as $order ) {
					// $formated_date = $order->get_date_completed();
					switch ( $this->chart_groupby ) {
						case 'day':
							$time = date( strtotime( $order->post_date ) );
							break;
						case 'month':
						default:
							$time = date( strtotime( $order->post_date ) );
							break;
					}

					$this->revenue_sales_and_times[ $time ][ $order->get_id() ] = isset( $this->revenue_sales_and_times[ $time ][ $order->get_id() ] ) ? $this->revenue_sales_and_times[ $time ][ $order->get_id() ] + $this->get_revenue_total($order->get_id()) : $this->get_revenue_total($order->get_id());
					$this->cost_sales_and_times[ $time ][ $order->get_id() ] = isset( $this->cost_sales_and_times[ $time ][ $order->get_id() ] ) ? $this->cost_sales_and_times[ $time ][ $order->get_id() ] + $this->get_cost_total($order->get_id()) : $this->get_revenue_total($order->get_id());

					$this->revenue_sales[ $order->get_id() ] = isset( $this->revenue_sales[ $order->get_id() ] ) ? $this->revenue_sales[ $order->get_id() ] + $this->get_revenue_total($order->get_id()) : $this->get_revenue_total($order->get_id());
					$this->cost_sales[ $order->get_id() ] = isset( $this->cost_sales[ $order->get_id() ] ) ? $this->cost_sales[ $order->get_id() ] + $this->get_cost_total($order->get_id()) : $this->get_cost_total($order->get_id());
				}
			}
			

			$legends = array();

			
			$legends = $this->get_chart_legend();
			
			// var_dump($this->revenue_sales); die();
			wc_get_template(
				'/admin/report-by-cost-revenue.php',
				array(
					'ranges'                    => $ranges,
					'chart_colours'             => $this->chart_colours,
					'current_range'             => $current_range,
					'check_current_range_nonce' => $this->check_current_range_nonce( $current_range ),
					'calculate_current_range'   => $this->calculate_current_range( $current_range ),
					// 'show_companies'            => $this->show_companies,
					'orders'                    => $orders,
					'item_sales'                => $this->revenue_sales,
					'item_sales_and_times'      => $this->revenue_sales_and_times,
					// 'get_export_button'         => $get_export_button,
					'legends'                   => $legends,
					// 'get_chart_widgets'         => $get_chart_widgets,
					// 'get_chart_widgets'         => $this->get_chart_widgets(),
					'get_main_chart'            => $this->get_main_chart(),
				),
				CFWS_PLUGIN_DIR,
				CFWS_TEMP_DIR . '/'
			);
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
				data-xaxes="<?php esc_attr_e( 'Date', 'cfws' ); ?>"
				data-groupby="<?php echo esc_attr( $this->chart_groupby ); ?>"
				>
					<?php esc_html_e( 'Export CSV', 'cfws' ); ?>
			</a>
			<?php
		}

		/**
		 * Get the main chart.
		 */
		public function get_main_chart() {
			global $wp_locale;

			$chart_data = array();
			$index      = 0;
			
			
			
				$revenue_chart_data = array();
				$cost_chart_data = array();

				for ( $i = 0; $i <= $this->chart_interval; $i ++ ) {

					$interval_total_revenue = 0;
					$interval_total_cost = 0;

					switch ( $this->chart_groupby ) {
						case 'day':
							$time = strtotime( current_time( 'Ymd', strtotime( "+{$i} DAY", $this->start_date ) ) ) * 1000;
							break;
						case 'month':
						default:
							$time = strtotime( current_time( 'Ym', strtotime( "+{$i} MONTH", $this->start_date ) ) . '01' ) * 1000;
							break;
					}
					$args_completed = array(
						'status' => array('wc-completed'),
						'limit' => -1,
					);
					$completed_orders = wc_get_orders( $args_completed );
					if ( $completed_orders ) {
						foreach ( $completed_orders as $order ) {
							if ( isset( $this->revenue_sales_and_times[ $time ][ $order->get_id() ] ) ) {
								$interval_total_revenue += $this->revenue_sales_and_times[ $time ][ $order->get_id() ];
							}

							if ( isset( $this->revenue_sales_and_times[ $time ][ $order->get_id() ] ) ) {
								$interval_total_cost += $this->cost_sales_and_times[ $time ][ $order->get_id() ];
							}

						}
					}

					$revenue_chart_data[] = array( $time, (float) wc_format_decimal( $interval_total_revenue, wc_get_price_decimals() ) );
					$cost_chart_data[] = array( $time, (float) wc_format_decimal( $interval_total_cost, wc_get_price_decimals() ) );
				}

				$chart_data[ 'revenue' ]['title'] = "Revenue";
				$chart_data[ 'revenue' ]['data']    = $revenue_chart_data;

				$chart_data[ 'cost' ]['title'] = "Cost";
				$chart_data[ 'cost' ]['data']    = $cost_chart_data;

				
				// var_dump($chart_data); die();
				
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
						label: "' . esc_js( $data['title'] ) . '",
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
