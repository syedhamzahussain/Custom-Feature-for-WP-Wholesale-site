<div id='packages_tab' class='panel '>
					
    <span>
        <label for="cfws_heading">Heading</label>
        <input type="text" name="cfws_heading" id="cfws_heading" value="<?php echo $cfws_heading; ?>"  >
    </span>

        <div class='packages_table_group'>
            <?php

            $variations_array = array();

            $packages = get_post_meta( $post->ID, 'cfws_packages', true );
            ?>
            <p>
                <span>
                    <label for="cfws_min_qty">Min Unit Qty</label>
                    <input type="number" name="" id="cfws_min_qty" min="1">
                </span>
                
                <span>
                    <label for="cfws_max_qty">Max Unit Qty</label>
                    <input type="number" name="" id="cfws_max_qty" min="1">
                </span>
                
                <span>
                    <label for="cfws_discount_type">Discount Type</label>
                    <select name="" id="cfws_discount_type">
                        <option value="" disabled selected >Choose Discount Type</option>
                        <option value="percent"  >Percent</option>
                        <option value="fixed"  >Fixed Value</option>
                    </select>
                </span>
                
                <span>
                    <label for="cfws_discount">Discount</label>
                    <input type="number" name="" id="cfws_discount" >
                </span>
                
                <span>
                    <button type="button" id="cfws_add_package" class="button"  onclick="addPackage()">Add Package</button>
                </span>
            </p>
            
        </div>
        <br>
        <div class="cfws_package_table">

            <table border="1" width="50%">
                <thead>
                    <th>Min Unit</th>
                    <th>Max Unit</th>
                    <th>Type</th>
                    <th>Discount</th>
                    <th>Action</th>
                </thead>
                <tbody id="cfws_package_table">
                <?php
                if ( $packages !== null && ! empty( $packages ) ) {

                    foreach ( $packages as $package ) {
                        ?>
                        
                            <tr>
                                <input type='hidden' name='cfws_min_unit[]' value='<?php echo $package['min']; ?>'/>
                                <input type='hidden' name='cfws_max_unit[]' value='<?php echo $package['max']; ?>'/>
                                <input type='hidden' name='cfws_discount_type[]' value='<?php echo $package['discount_type']; ?>'/>
                                <input type='hidden' name='cfws_discount[]' value='<?php echo $package['discount']; ?>'/>

                                <td><?php echo $package['min']; ?></td>
                                <td><?php echo $package['max']; ?></td>
                                <td><?php echo $package['discount_type']; ?></td>
                                <td><?php echo $package['discount']; ?></td>
                                <td>
                                    <button id="cfws_edit_button" type="button" onclick="editPackage(this)">Edit</button>
                                    <button id="cfws_delete_button" type="button" onclick="deletePackage(this)">Delete</button>
                                </td>
                            </tr>
                        <?php
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
        <input type="hidden" value="<?php echo wp_kses_post( wp_create_nonce( 'cfwsnonce' ) ); ?>" id="cfwsnonce" name="cfwsnonce">
    </div>
    </div>