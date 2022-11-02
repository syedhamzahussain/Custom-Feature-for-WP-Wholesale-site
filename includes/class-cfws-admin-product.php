<?php
/**
 * CFWS_ADMIN_PRODUCT loader Class File.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;

}

if ( ! class_exists( 'CFWS_ADMIN_PRODUCT' ) ) {

	/**
	 * CFWS_ADMIN_PRODUCT class.
	 */
	class CFWS_ADMIN_PRODUCT {

		/**
		 * Function Constructor.
		 */
		public function __construct() {

			add_action( 'woocommerce_product_options_general_product_data', array( $this, 'custom_fields' ) );
			add_action( 'woocommerce_process_product_meta', array( $this, 'save_custom_fields' ) );
            add_action( 'woocommerce_variation_options_pricing', array( $this,'cfws_add_custom_field_to_variations'), 10, 3 );
            add_action( 'woocommerce_save_product_variation', array( $this,'cfws_save_custom_field_variations'), 10, 2 );
            add_filter( 'woocommerce_available_variation', array( $this,'cfws_add_custom_field_variation_data') );
			
            add_action( 'woocommerce_product_data_tabs', array( $this, 'cfws_packages_tab' ) );
			add_filter( 'woocommerce_product_data_panels', array( $this, 'cfws_packages_tab_content' ) );
			add_action( 'woocommerce_process_product_meta', array( $this, 'cfws_packages_tab_fields' ) );
		}

        
 
        function cfws_add_custom_field_to_variations( $loop, $variation_data, $variation ) {
            woocommerce_wp_text_input(
                array(
                    'id'                => 'cfws_unit_quantity['.$loop.']',
                    'label'             => __( 'Quantity per Unit', 'cfws_wc_product_page_enhancement' ),
                    'placeholder'       => __( 'Set quantity per unit', 'cfws_wc_product_page_enhancement' ),
                    'desc_tip'          => 'true',
                    'value'             => get_post_meta( $variation->ID, 'cfws_unit_quantity', true ),
                    'custom_attributes' => array(
                        'step' => 'any',
                        'min'  => '0',
                    ),
                )
            );

            woocommerce_wp_text_input(
                array(
                    'id'                => 'cfws_unit_cost['.$loop.']',
                    'label'             => __( 'Unit Cost', 'cfws_wc_product_page_enhancement' ),
                    'placeholder'       => __( 'Unit Cost', 'cfws_wc_product_page_enhancement' ),
                    'desc_tip'          => 'true',
                    'value'             => get_post_meta( $variation->ID, 'cfws_unit_cost', true ),
                    'custom_attributes' => array(
                        'step' => 'any',
                        'min'  => '0',
                    ),
                )
            );
        }
        
        // -----------------------------------------
        // 2. Save custom field on product variation save
        

        
        function cfws_save_custom_field_variations( $variation_id, $i ) {
            $cost = $_POST['cfws_unit_cost'][$i];
            $unit = $_POST['cfws_unit_quantity'][$i];
            if ( isset( $cost ) ) update_post_meta( $variation_id, 'cfws_unit_cost', esc_attr( $cost ) );
            if ( isset( $unit ) ) update_post_meta( $variation_id, 'cfws_unit_quantity', esc_attr( $unit ) );
        }
        
        // -----------------------------------------
        // 3. Store custom field value into variation data
        

        
        function cfws_add_custom_field_variation_data( $variations ) {
            $variations['cfws_unit_cost'] = '<div class="woocommerce_custom_field">Unit Cost: <span>' . get_post_meta( $variations[ 'variation_id' ], 'cfws_unit_cost', true ) . '</span></div>';
            $variations['cfws_unit_quantity'] = '<div class="woocommerce_custom_field">Unit Quantity: <span>' . get_post_meta( $variations[ 'variation_id' ], 'cfws_unit_quantity', true ) . '</span></div>';
            return $variations;
        }

        /**
		 * Function to show new tab Variation Table Under in Edit Product Page.
		 *
		 * @param object  $product Prod Array.
		 * @param string $taxo Taxonomy.
		 */
		public function cfws_get_varitaions( $product, $taxo = '' ) {

			foreach ( $product->get_variation_attributes() as $taxonomy => $terms_slug ) {
				// To get the attribute label (in WooCommerce 3+).
				$taxonomy_label = wc_attribute_label( $taxonomy, $product );

				$options = array();

				foreach ( $terms_slug as $key => $value ) {
					array_push( $options, $value );
				}

				// Setting some data in an array.
				$variations_attributes_and_values[ $taxonomy ] = array(
					'label'   => $taxonomy_label,
					'options' => $options,
				);
			}

			if ( $taxo ) {
				return $variations_attributes_and_values[ $taxo ];
			}

			return $variations_attributes_and_values;
		}

		/**
		 * Function to show new tab Variation Table Under in Edit Product Page
		 *
		 * @param array $product_data_tabs Tabs Array.
		 */
		public function cfws_packages_tab( $product_data_tabs ) {

			global $post;
			$product = wc_get_product( $post->ID );

			
				$product_data_tabs['cfws-tab-4'] = array(
					'label'  => __( 'Packages Table', 'cfws_wc_product_page_enhancement' ),
					'target' => 'packages_tab',
				);
			

			return $product_data_tabs;
		}

		/**
		 * Function to show Frontend of edit product
		 */
		public function cfws_packages_tab_content() {

			global $post;

			$product = wc_get_product( $post->ID );
			
				?>
				<div id='packages_tab' class='panel '>
					<div class='packages_table_group'>
						<?php

						$variations_array = array();


                        $packages = get_post_meta( $post->ID, 'cfws_packages', true );
                        ?>
                        <div>
                            <label for="cfws_min_qty">Min Unit Qty</label>
                            <input type="number" name="" id="cfws_min_qty" required>
                        </div>
                        <br>
                        <div>
                            <label for="cfws_max_qty">Max Unit Qty</label>
                            <input type="number" name="" id="cfws_max_qty" required>
                        </div>
                        <br>
                        <div>
                            <label for="cfws_discount_type">Discount Type</label>
                            <select name="" id="cfws_discount_type">
                                <option value="" disabled selected >Choose Discount Type</option>
                                <option value="percent"  >Percent</option>
                                <option value="fixed"  >Fixed Value</option>
                            </select>
                        </div>
                        <br>
                        <div>
                            <label for="cfws_discount">Discount</label>
                            <input type="number" name="" id="cfws_discount" required>
                        </div>
                        <br>
                        <div>
                            <button type="button" id="cfws_add_package" onclick="addPackage()">Add Package</button>
                        </div>
						
					</div>

                    <div class="cfws_package_table">

                        <table border="1">
                            <thead>
                                <th>Min Unit</th>
                                <th>Max Unit</th>
                                <th>Type</th>
                                <th>Discount</th>
                            </thead>
                            <tbody id="cfws_package_table">
                            <?php
                                if( $packages !== null && !empty($packages)  ){ 
                                    
                                    foreach($packages as $package){ ?>
                                        <tr>
                                            <td><?php echo $package['min'];  ?></td>
                                            <td><?php echo $package['max'];  ?></td>
                                            <td><?php echo $package['discount_type'];  ?></td>
                                            <td><?php echo $package['discount'];  ?></td>
                                        </tr>
                            <?php   }
                                

                                   
                             }
                            ?>
                            </tbody>
                        </table>
                    </div>
					<input type="hidden" value="<?php echo wp_kses_post( wp_create_nonce( 'cfwsnonce' ) ); ?>" id="cfwsnonce" name="cfwsnonce">
				</div>
				</div>
				<?php
                
			
		}

		/**
		 * Save sample product id value
		 *
		 * @param int $post_id Postid.
		 */
		public function cfws_packages_tab_fields( $post_id ) {

			if ( isset( $_POST['cfwsnonce'] ) ) {
				$nonce = sanitize_text_field( wp_unslash( $_POST['cfwsnonce'] ) );
				if ( ! wp_verify_nonce( $nonce, 'cfwsnonce' ) ) {
					return;
				}
			}

			if ( isset( $_POST['cfws_min_unit'] ) ) {
                $packages = [];
                foreach ($_POST['cfws_min_unit'] as $key => $value) {
                    $packages[] = ['min' => $value, 'max' => $_POST['cfws_max_unit'][$key], 'discount_type' => $_POST['cfws_discount_type'][$key], 'discount' => $_POST['cfws_discount'][$key]];
                }
				// $select_primary_attribute = sanitize_text_field( wp_unslash( $_POST['cfws_primary_attribute'] ) );
				update_post_meta( $post_id, 'cfws_packages', $packages );
			}

		}


		/**
		 * Function to show Inputs Under General Tab in Edit
		 * Product Page for unit quantity and unit cost
		 *
		 * @return void
		 */
		public function custom_fields() {
			global $post;
			$_product = wc_get_product( $post->ID );

			// if product is simple.
			if ( $_product->get_type() === 'simple' ) {
				echo '<div class="options_group">';
				woocommerce_wp_text_input(
					array(
						'id'                => 'cfws_unit_quantity',
						'label'             => __( 'Quantity per Unit', 'cfws_wc_product_page_enhancement' ),
						'placeholder'       => __( 'Set quantity per unit', 'cfws_wc_product_page_enhancement' ),
						'desc_tip'          => 'true',
						'description'       => '',
						'custom_attributes' => array(
							'step' => 'any',
							'min'  => '0',
						),
					)
				);

				woocommerce_wp_text_input(
					array(
						'id'                => 'cfws_unit_cost',
                        'label'             => __( 'Unit Cost', 'cfws_wc_product_page_enhancement' ),
						'placeholder'       => __( 'Unit Cost', 'cfws_wc_product_page_enhancement' ),
						'desc_tip'          => 'true',
						'description'       => __( 'Set the cost for a unit.', 'cfws_wc_product_page_enhancement' ),
						'custom_attributes' => array(
							'step' => 'any',
							'min'  => '0',
						),
					)
				);
				?>
				<input type="hidden" value="<?php echo wp_kses_post( wp_create_nonce( 'cfwsnonce' ) ); ?>" id="cfwsnonce" name="cfwsnonce">
				</div>
				<?php
			}
		}

		/**
		 * This function will save the value set to Minimum Quantity and Maximum Quantity options.
		 *
		 * @param int $post_id Id.
		 */
		public function save_custom_fields( $post_id ) {
			if ( isset( $_POST['cfwsnonce'] ) ) {
				$nonce = sanitize_text_field( wp_unslash( $_POST['cfwsnonce'] ) );
				if ( ! wp_verify_nonce( $nonce, 'cfwsnonce' ) ) {
					return;
				}
			}

			$unit_quantity = trim( get_post_meta( $post_id, 'cfws_unit_quantity', true ) );
			if ( isset( $_POST['cfws_unit_quantity'] ) ) {
				$new_unit_quantity = sanitize_text_field( wp_unslash( $_POST['cfws_unit_quantity'] ) );
			}

			$unit_cost = trim( get_post_meta( $post_id, 'cfws_unit_cost', true ) );
			if ( isset( $_POST['cfws_unit_cost'] ) ) {
				$new_unit_cost = sanitize_text_field( wp_unslash( $_POST['cfws_unit_cost'] ) );
			}

			if ( $unit_quantity !== $new_unit_quantity ) {
				update_post_meta( $post_id, 'cfws_unit_quantity', $new_unit_quantity );
			}

			if ( $unit_cost !== $new_max ) {
				update_post_meta( $post_id, 'cfws_unit_cost', $new_unit_cost );
			}
		}

		


	}

	new CFWS_ADMIN_PRODUCT();
}