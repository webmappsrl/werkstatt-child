<?php
    wp_enqueue_script( 'wc-add-to-cart-variation' );

    function mostraPrezzo($post_id){
    echo '<h2 id="adottaora">Adotta per un anno!</h2>';
    //woocommerce_template_single_price();
    echo '<p>Scegli fra le nostre tre opzioni di adozione, a partire da €9 all\'anno. Questo versamento permetterà di fornire le cure necessarie alla pianta e di mantenere in vita il progetto.</p>';
    // woocommerce_template_single_add_to_cart();
    // echo do_shortcode('[products id="6889"]');
    // echo do_shortcode('[add_to_cart id="6896"]');
    // echo wc_attribute_label( $attribute_name );
    $product = wc_get_product( '6896');
    $attributes_main = $product->get_attributes();
    $available_variations = $product->get_available_variations();
    echo '<br>';
    $attributes = array();
    // print_r($available_variations);
    foreach ( $attributes_main as $attribute ):
        $attributes['Modalità'] = $attribute->get_options();
        // testing output
        print_r($attributes);
    endforeach;
    $attribute_keys  = array_keys( $attributes );
    $variations_json = wp_json_encode( $available_variations );
    $variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );
    ?>
        <form class="variations_form cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo $variations_attr; // WPCS: XSS ok. ?>">
        <?php do_action( 'woocommerce_before_variations_form' ); ?>

        <?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
            <p class="stock out-of-stock"><?php echo esc_html( apply_filters( 'woocommerce_out_of_stock_message', __( 'This product is currently out of stock and unavailable.', 'woocommerce' ) ) ); ?></p>
        <?php else : ?>
            <table class="variations" cellspacing="0">
                <tbody>
                    <?php foreach ( $attributes as $attribute_name => $options ) : ?>
                        <tr>
                            <td class="label"><label for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>"><?php echo wc_attribute_label( $attribute_name ); // WPCS: XSS ok. ?></label></td>
                            <td class="value">
                                <?php
                                    wc_dropdown_variation_attribute_options( array(
                                        'options'   => $options,
                                        'attribute' => $attribute_name,
                                        'product'   => $product,
                                    ) );
                                    echo end( $attribute_keys ) === $attribute_name ? wp_kses_post( apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . esc_html__( 'Clear', 'woocommerce' ) . '</a>' ) ) : '';
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="single_variation_wrap">
                <div class="woocommerce-variation single_variation">
                    <script type="text/template" id="tmpl-variation-template">
                    <div class="woocommerce-variation-description">{{{ data.variation.variation_description }}}</div>
                    <div class="woocommerce-variation-price">{{{ data.variation.price_html }}}</div>
                    <div class="woocommerce-variation-availability">{{{ data.variation.availability_html }}}</div>
                    </script>
                    <script type="text/template" id="tmpl-unavailable-variation-template">
                    <p><?php esc_html_e( 'Sorry, this product is unavailable. Please choose a different combination.', 'woocommerce' ); ?></p>
                    </script>
                </div>
                <div class="woocommerce-variation-add-to-cart variations_button">
                    <?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

                    <?php
                    do_action( 'woocommerce_before_add_to_cart_quantity' );

                    woocommerce_quantity_input( array(
                        'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
                        'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
                        'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
                    ) );

                    do_action( 'woocommerce_after_add_to_cart_quantity' );
                    ?>

                    <button type="submit" data-poi="<?= $post_id?>" class="single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>

                    <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

                    <input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>" />
                    <input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>" />
                    <input type="hidden" name="variation_id" class="variation_id" value="0" />
                </div>
            </div>
        <?php endif; ?>

        <?php do_action( 'woocommerce_after_variations_form' ); ?>
    </form>
    <?php
    echo '<div class="back-to-map"><a href="https://montepisanotree.org/mappa">o torna alla mappa</a></div>';
}


-----------------------------------------------------------------------------------------


function mostraPrezzo($post_id){
    echo '<h2 id="adottaora">Adotta per un anno!</h2>';
    //woocommerce_template_single_price();
    echo '<p>Scegli fra le nostre tre opzioni di adozione, a partire da €9 all\'anno. Questo versamento permetterà di fornire le cure necessarie alla pianta e di mantenere in vita il progetto.</p>';
    // woocommerce_template_single_add_to_cart();
    echo do_shortcode('[product id="6889"]');
    echo do_shortcode('[product id="6888"]');
    echo do_shortcode('[product id="6887"]');
    // echo do_shortcode('[add_to_cart id="6896"]');
    // echo wc_attribute_label( $attribute_name );
    $product = wc_get_product( '6889');
    ?>
    <form class="cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>
		<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

		<?php
		do_action( 'woocommerce_before_add_to_cart_quantity' );

		woocommerce_quantity_input( array(
			'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
			'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
			'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
		) );

		do_action( 'woocommerce_after_add_to_cart_quantity' );
		?>

        <button type="submit" data-poi="<?= $post_id?>" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>

		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
	</form>
    <?php
    
    echo '<div class="back-to-map"><a href="https://montepisanotree.org/mappa">o torna alla mappa</a></div>';
}

-----------------------------------------------------------------------------------------------------


function mostraPrezzo($post_id)
{
    echo '<h2 id="adottaora">Adotta per un anno!</h2>';
    //woocommerce_template_single_price();
    echo '<p>Scegli fra le nostre tre opzioni di adozione, a partire da €9 all\'anno. Questo versamento permetterà di fornire le cure necessarie alla pianta e di mantenere in vita il progetto.</p>';
    // woocommerce_template_single_add_to_cart();
    // echo do_shortcode('[product id="6889"]');
    // echo do_shortcode('[product id="6888"]');
    // echo do_shortcode('[product id="6887"]');
    // echo do_shortcode('[add_to_cart id="6896"]');
    // echo wc_attribute_label( $attribute_name );
    $product_1 = wc_get_product('6887');
    $product_2 = wc_get_product('6888');
    $product_3 = wc_get_product('6889');
    ?>

    <form class="variations_form cart" action="/mpt/carrello/" method="post" data-product_id="">

        <table class="variations" cellspacing="0">
            <tbody>
                <tr>
                    <td class="label"><label for="modalita">Tipo di Adozione</label></td>
                    <td class="value">
                        <select id="modalita" class="" name="attribute_modalita" data-attribute_name="attribute_modalita" data-show_option_none="yes">
                            <option value="">Scegli un'opzione</option>
                            <option value="6887" class="attached enabled"><?php echo $product_1->get_name(); ?></option>
                            <option value="6888" class="attached enabled"><?php echo $product_2->get_name(); ?></option>
                            <option value="6889" class="attached enabled"><?php echo $product_3->get_name(); ?></option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="single_variation_wrap">
            <div class="woocommerce-variation single_variation" style="display: block;">
               
            </div>
            <div class="woocommerce-variation-add-to-cart variations_button woocommerce-variation-add-to-cart-enabled">
                <button type="submit" class="single_add_to_cart_button button alt">Adotta ora!</button>
                <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($product_1->get_id()); ?>">
            </div>
        </div>

    </form>
<?php 

if(isset($_POST['submit'])){
    $selected_val = $_POST['attribute_modalita'];  // Storing Selected Value In Variable
    echo "You have selected :" .$selected_val;  // Displaying Selected Value
    }
?>

    <?php
    echo $product_1->get_name();
    echo $product_1->get_description();
    echo wc_price($product_1->get_price());
    ?>
    <form class="cart" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product_1->get_permalink())); ?>" method="post" enctype='multipart/form-data'>
        <?php do_action('woocommerce_before_add_to_cart_button'); ?>

        <?php
        do_action('woocommerce_before_add_to_cart_quantity');

        woocommerce_quantity_input(array(
            'min_value'   => apply_filters('woocommerce_quantity_input_min', $product_1->get_min_purchase_quantity(), $product_1),
            'max_value'   => apply_filters('woocommerce_quantity_input_max', $product_1->get_max_purchase_quantity(), $product_1),
            'input_value' => isset($_POST['quantity']) ? wc_stock_amount(wp_unslash($_POST['quantity'])) : $product_1->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
        ));

        do_action('woocommerce_after_add_to_cart_quantity');
        ?>

        <button type="submit" data-poi="<?= $post_id ?>" name="add-to-cart" value="<?php echo esc_attr($product_1->get_id()); ?>" class="single_add_to_cart_button button alt"><?php echo esc_html($product_1->single_add_to_cart_text()); ?></button>

        <?php do_action('woocommerce_after_add_to_cart_button'); ?>
    </form>
    <?php
    echo $product_2->get_name();
    echo $product_2->get_description();
    echo wc_price($product_2->get_price());
    ?>
    <form class="cart" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product_2->get_permalink())); ?>" method="post" enctype='multipart/form-data'>
        <?php do_action('woocommerce_before_add_to_cart_button'); ?>

        <?php
        do_action('woocommerce_before_add_to_cart_quantity');

        woocommerce_quantity_input(array(
            'min_value'   => apply_filters('woocommerce_quantity_input_min', $product_2->get_min_purchase_quantity(), $product_2),
            'max_value'   => apply_filters('woocommerce_quantity_input_max', $product_2->get_max_purchase_quantity(), $product_2),
            'input_value' => isset($_POST['quantity']) ? wc_stock_amount(wp_unslash($_POST['quantity'])) : $product_2->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
        ));

        do_action('woocommerce_after_add_to_cart_quantity');
        ?>

        <button type="submit" data-poi="<?= $post_id ?>" name="add-to-cart" value="<?php echo esc_attr($product_2->get_id()); ?>" class="single_add_to_cart_button button alt"><?php echo esc_html($product_2->single_add_to_cart_text()); ?></button>

        <?php do_action('woocommerce_after_add_to_cart_button'); ?>
    </form>
    <?php
    echo $product_3->get_name();
    echo $product_3->get_description();
    echo wc_price($product_3->get_price());
    ?>
    <form class="cart" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product_3->get_permalink())); ?>" method="post" enctype='multipart/form-data'>
        <?php do_action('woocommerce_before_add_to_cart_button'); ?>

        <?php
        do_action('woocommerce_before_add_to_cart_quantity');

        woocommerce_quantity_input(array(
            'min_value'   => apply_filters('woocommerce_quantity_input_min', $product_3->get_min_purchase_quantity(), $product_3),
            'max_value'   => apply_filters('woocommerce_quantity_input_max', $product_3->get_max_purchase_quantity(), $product_3),
            'input_value' => isset($_POST['quantity']) ? wc_stock_amount(wp_unslash($_POST['quantity'])) : $product_3->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
        ));

        do_action('woocommerce_after_add_to_cart_quantity');
        ?>

        <button type="submit" data-poi="<?= $post_id ?>" name="add-to-cart" value="<?php echo esc_attr($product_3->get_id()); ?>" class="single_add_to_cart_button button alt"><?php echo esc_html($product_3->single_add_to_cart_text()); ?></button>

        <?php do_action('woocommerce_after_add_to_cart_button'); ?>
    </form>
    <?php

    echo '<div class="back-to-map"><a href="https://montepisanotree.org/mappa">o torna alla mappa</a></div>';
}
Array
(
    [2275eed839609aee68127bbb3de93f31] => Array
        (
            [idpoi1] => 5833
            [key] => 2275eed839609aee68127bbb3de93f31
            [product_id] => 6887
            [variation_id] => 0
            [variation] => Array
                (
                )

            [quantity] => 1
            [data_hash] => b5c1d5ca8bae6d4896cf1807cdf763f0
            [line_tax_data] => Array
                (
                    [subtotal] => Array
                        (
                        )

                    [total] => Array
                        (
                        )

                )

            [line_subtotal] => 9
            [line_subtotal_tax] => 0
            [line_total] => 9
            [line_tax] => 0
            [data] => WC_Product_Simple Object
                (
                    [object_type:protected] => product
                    [post_type:protected] => product
                    [cache_group:protected] => products
                    [data:protected] => Array
                        (
                            [name] => Friendship
                            [slug] => friendship
                            [date_created] => WC_DateTime Object
                                (
                                    [utc_offset:protected] => 0
                                    [date] => 2020-03-25 09:32:56.000000
                                    [timezone_type] => 3
                                    [timezone] => Europe/Rome
                                )

                            [date_modified] => WC_DateTime Object
                                (
                                    [utc_offset:protected] => 0
                                    [date] => 2020-03-31 16:28:53.000000
                                    [timezone_type] => 3
                                    [timezone] => Europe/Rome
                                )

                            [status] => publish
                            [featured] => 
                            [catalog_visibility] => visible
                            [description] =>  Aggiornamento periodico Visibilità sul sito
                            [short_description] =>  Aggiornamento periodico Visibilità sul sito
                            [sku] => 
                            [price] => 9
                            [regular_price] => 9
                            [sale_price] => 
                            [date_on_sale_from] => 
                            [date_on_sale_to] => 
                            [total_sales] => 0
                            [tax_status] => taxable
                            [tax_class] => 
                            [manage_stock] => 
                            [stock_quantity] => 
                            [stock_status] => instock
                            [backorders] => no
                            [low_stock_amount] => 
                            [sold_individually] => 
                            [weight] => 
                            [length] => 
                            [width] => 
                            [height] => 
                            [upsell_ids] => Array
                                (
                                )

                            [cross_sell_ids] => Array
                                (
                                )

                            [parent_id] => 0
                            [reviews_allowed] => 
                            [purchase_note] => 
                            [attributes] => Array
                                (
                                )

                            [default_attributes] => Array
                                (
                                )

                            [menu_order] => 0
                            [post_password] => 
                            [virtual] => 
                            [downloadable] => 
                            [category_ids] => Array
                                (
                                    [0] => 124
                                )

                            [tag_ids] => Array
                                (
                                )

                            [shipping_class_id] => 0
                            [downloads] => Array
                                (
                                )

                            [image_id] => 
                            [gallery_image_ids] => Array
                                (
                                )

                            [download_limit] => -1
                            [download_expiry] => -1
                            [rating_counts] => Array
                                (
                                )

                            [average_rating] => 0
                            [review_count] => 0
                        )

                    [supports:protected] => Array
                        (
                            [0] => ajax_add_to_cart
                        )

                    [id:protected] => 6887
                    [changes:protected] => Array
                        (
                        )

                    [object_read:protected] => 1
                    [extra_data:protected] => Array
                        (
                        )

                    [default_data:protected] => Array
                        (
                            [name] => 
                            [slug] => 
                            [date_created] => 
                            [date_modified] => 
                            [status] => 
                            [featured] => 
                            [catalog_visibility] => visible
                            [description] => 
                            [short_description] => 
                            [sku] => 
                            [price] => 
                            [regular_price] => 
                            [sale_price] => 
                            [date_on_sale_from] => 
                            [date_on_sale_to] => 
                            [total_sales] => 0
                            [tax_status] => taxable
                            [tax_class] => 
                            [manage_stock] => 
                            [stock_quantity] => 
                            [stock_status] => instock
                            [backorders] => no
                            [low_stock_amount] => 
                            [sold_individually] => 
                            [weight] => 
                            [length] => 
                            [width] => 
                            [height] => 
                            [upsell_ids] => Array
                                (
                                )

                            [cross_sell_ids] => Array
                                (
                                )

                            [parent_id] => 0
                            [reviews_allowed] => 1
                            [purchase_note] => 
                            [attributes] => Array
                                (
                                )

                            [default_attributes] => Array
                                (
                                )

                            [menu_order] => 0
                            [post_password] => 
                            [virtual] => 
                            [downloadable] => 
                            [category_ids] => Array
                                (
                                )

                            [tag_ids] => Array
                                (
                                )

                            [shipping_class_id] => 0
                            [downloads] => Array
                                (
                                )

                            [image_id] => 
                            [gallery_image_ids] => Array
                                (
                                )

                            [download_limit] => -1
                            [download_expiry] => -1
                            [rating_counts] => Array
                                (
                                )

                            [average_rating] => 0
                            [review_count] => 0
                        )

                    [data_store:protected] => WC_Data_Store Object
                        (
                            [instance:WC_Data_Store:private] => WC_Product_Data_Store_CPT Object
                                (
                                    [internal_meta_keys:protected] => Array
                                        (
                                            [0] => _visibility
                                            [1] => _sku
                                            [2] => _price
                                            [3] => _regular_price
                                            [4] => _sale_price
                                            [5] => _sale_price_dates_from
                                            [6] => _sale_price_dates_to
                                            [7] => total_sales
                                            [8] => _tax_status
                                            [9] => _tax_class
                                            [10] => _manage_stock
                                            [11] => _stock
                                            [12] => _stock_status
                                            [13] => _backorders
                                            [14] => _low_stock_amount
                                            [15] => _sold_individually
                                            [16] => _weight
                                            [17] => _length
                                            [18] => _width
                                            [19] => _height
                                            [20] => _upsell_ids
                                            [21] => _crosssell_ids
                                            [22] => _purchase_note
                                            [23] => _default_attributes
                                            [24] => _product_attributes
                                            [25] => _virtual
                                            [26] => _downloadable
                                            [27] => _download_limit
                                            [28] => _download_expiry
                                            [29] => _featured
                                            [30] => _downloadable_files
                                            [31] => _wc_rating_count
                                            [32] => _wc_average_rating
                                            [33] => _wc_review_count
                                            [34] => _variation_description
                                            [35] => _thumbnail_id
                                            [36] => _file_paths
                                            [37] => _product_image_gallery
                                            [38] => _product_version
                                            [39] => _wp_old_slug
                                            [40] => _edit_last
                                            [41] => _edit_lock
                                        )

                                    [must_exist_meta_keys:protected] => Array
                                        (
                                            [0] => _tax_class
                                        )

                                    [extra_data_saved:protected] => 
                                    [updated_props:protected] => Array
                                        (
                                        )

                                    [meta_type:protected] => post
                                    [object_id_field_for_meta:protected] => 
                                )

                            [stores:WC_Data_Store:private] => Array
                                (
                                    [coupon] => WC_Coupon_Data_Store_CPT
                                    [customer] => WC_Customer_Data_Store
                                    [customer-download] => WC_Customer_Download_Data_Store
                                    [customer-download-log] => WC_Customer_Download_Log_Data_Store
                                    [customer-session] => WC_Customer_Data_Store_Session
                                    [order] => WC_Order_Data_Store_CPT
                                    [order-refund] => WC_Order_Refund_Data_Store_CPT
                                    [order-item] => WC_Order_Item_Data_Store
                                    [order-item-coupon] => WC_Order_Item_Coupon_Data_Store
                                    [order-item-fee] => WC_Order_Item_Fee_Data_Store
                                    [order-item-product] => WC_Order_Item_Product_Data_Store
                                    [order-item-shipping] => WC_Order_Item_Shipping_Data_Store
                                    [order-item-tax] => WC_Order_Item_Tax_Data_Store
                                    [payment-token] => WC_Payment_Token_Data_Store
                                    [product] => WC_Product_Data_Store_CPT
                                    [product-grouped] => WC_Product_Grouped_Data_Store_CPT
                                    [product-variable] => WC_Product_Variable_Data_Store_CPT
                                    [product-variation] => WC_Product_Variation_Data_Store_CPT
                                    [shipping-zone] => WC_Shipping_Zone_Data_Store
                                    [webhook] => WC_Webhook_Data_Store
                                )

                            [current_class_name:WC_Data_Store:private] => WC_Product_Data_Store_CPT
                            [object_type:WC_Data_Store:private] => product-simple
                        )

                    [meta_data:protected] => 
                )

        )

)