<?php

define('MPT_SESSION_JSON_KEY', 'order_json');
define('MPT_ORDER_JSON_KEY', 'order_json');
define('MPT_ORDER_PAID_DATE', 'order_paid_date');
define('MPT_POI_PAID_DATE', 'paid_date');

function montepisanotree_dedication_product_types()
{
    return array(
        'passion',
        'love'
    );
}

// Set order json in checkout page
add_action('woocommerce_before_checkout_form', 'montepisanotree_get_cart_json');
function montepisanotree_get_cart_json()
{
    $cart = WC()->cart->get_cart();
    $order_json = array();
    
    foreach ($cart as $key => $val) {
        $item = array();
        $poi_id = $val['idpoi'];
        $poi_dedication = $val['dedpoi'];
        $poi_title = get_the_title($poi_id);
        $name = $val['data']->get_name();
        $item['id'] = $poi_id;
        $item['title'] = $poi_title;
        $item['dedication'] = $poi_dedication;
        if ($name == 'Friendship') {
            $order_json['friendship'][] = $item;
        }
        if ($name == 'Love') {
            $order_json['Love'][] = $item;
        }
        if ($name == 'Passion') {
            $order_json['Passion'][] = $item;
        }
    }

    //set key montepisanotree_order_json in user session with json order
    WC()->session->set(MPT_SESSION_JSON_KEY, json_encode($order_json));

    //DEBUG
    // echo '<pre>';
    // echo json_encode($order_json);
    // echo '</pre>';
    echo '<div class="show-woocommerce-cart-checkout">';
    echo do_shortcode('[woocommerce_cart]');
    echo '</div>';
}



add_filter('woocommerce_checkout_fields', function ($fields) {
    
    $dedicationProducts = montepisanotree_dedication_product_types();
    $json = WC()->session->get(MPT_SESSION_JSON_KEY);
    if ($json) {
        $jsonPhp = json_decode($json, true);
        if (is_array($jsonPhp)) {
            foreach ($jsonPhp as $type => $arr) {
                if (is_array($arr)) :
                    foreach ($arr as $data) {
                        if (isset($data['id']) && in_array(strtolower($type), $dedicationProducts)) {
                            $terms = get_the_terms( $data['id'] , 'webmapp_category' );
                            $title = get_the_title($data['id']);
                            $fields['billing'][$data['id']] = array(
                                'type'        => 'textarea',
                                'label'       => __('Nome su targhetta', 'wm-child-mpt') . ' ' . $terms[0]->name . ' ' . $title . ' ' . ucfirst($type),
                                'class'       => array('notes'),
                                'required'    => false,
                                'placeholder' => esc_attr__('Inserisci qui il nome che vuoi mettere sulla targhetta (Es. \'Luca e Martina\',\'Vittorio\' o \'Famiglia Rossi\'...). Lasciando questo campo vuoto, sulla targhetta verrà stampato il nome inserito nei dettagli di fatturazione.	', 'wm-child-mpt'),
                                'default'     => $data['dedication'] ? $data['dedication'] : '',
                                'custom_attributes' => $data['dedication'] ? [ 'readonly' => 'readonly' ] : []
                            );
                        }
                    }
                endif;
            }
        }
    }
    $old_order_id = WC()->session->get('oldOrderId');
    if ( $old_order_id )
    {
        $old_order = wc_get_order($old_order_id);
        $order_meta = get_post_meta($old_order_id);
        $order_data = $old_order->get_data();
        $order_billing_first_name = $order_data['billing']['first_name'];
        $order_billing_last_name = $order_data['billing']['last_name'];
        $order_billing_address_1 = $order_data['billing']['address_1'];
        $order_billing_city = $order_data['billing']['city'];
        $order_billing_state = $order_data['billing']['state'];
        $order_billing_postcode = $order_data['billing']['postcode'];
        $order_billing_country = $order_data['billing']['country'];
        $order_billing_email = $order_data['billing']['email'];
        $order_billing_phone = $order_data['billing']['phone'];
        $order_billing_codice_fiscale = $order_meta['billing_codice_fiscale'][0];

        $fields['billing']['billing_first_name']['default'] =  $order_billing_first_name;
        $fields['billing']['billing_last_name']['default'] =  $order_billing_last_name;
        $fields['billing']['billing_address_1']['default'] =  $order_billing_address_1;
        $fields['billing']['billing_city']['default'] =  $order_billing_city;
        $fields['billing']['billing_country']['default'] =  $order_billing_country;
        $fields['billing']['billing_state']['default'] =  $order_billing_state;
        $fields['billing']['billing_postcode']['default'] =  $order_billing_postcode;
        $fields['billing']['billing_phone']['default'] =  $order_billing_phone;
        $fields['billing']['billing_email']['default'] =  $order_billing_email;
        $fields['billing']['billing_codice_fiscale']['default'] =  $order_billing_codice_fiscale;
        
    }
    return $fields;
});


add_action( 'woocommerce_check_cart_items', function () {

    // Only on checkout page 
    if ( !is_checkout() ) 
        return;

    // Check if the items are from a renewal
    $orderPaidDateSession = WC()->session->get('orderPaidDateSession');
    if( $orderPaidDateSession ) 
        return;

    // Check cart items if they are already purchased
    $cart = WC()->cart->get_cart();
    $poi_name_error = array();
    foreach ( $cart as $key => $val){
        $poi_id = $val['idpoi'];
        $product_id = $val['product_id'];
        $name = $val['data']->get_name();
        if (isset($poi_id)) {
            $poi_paid_date = get_field(MPT_POI_PAID_DATE,$poi_id);
            if (isset($poi_paid_date) && !empty($poi_paid_date)) {
                $poi_name_error[] = get_the_title( $poi_id);
                WC()->cart->remove_cart_item( $key );
            }
        }
    }
    if ($poi_name_error && count($poi_name_error) == 1) {
        wc_add_notice( sprintf( 
            __("Albero %s è stato già acquistato, per favore <a href='".home_url('/mappa')."' >scegliere un altro albero</a>", "woocommerce" ),  
            '"' . implode(", ",$poi_name_error) . '"'
        ), 'error' );
    } elseif ($poi_name_error && count($poi_name_error) > 1) {
        wc_add_notice( sprintf( 
            __("Alberi %s sono stati già acquistati, per favore <a href='".home_url('/mappa')."' >scegliere altri alberi</a>", "woocommerce" ),  
            '"' . implode(", ",$poi_name_error) . '"'
        ), 'error' );
    }

}, 10, 0);


add_action('woocommerce_checkout_order_processed', function ($order_id, $posted_data, $order) {
    
    $dedicationProducts = montepisanotree_dedication_product_types();
    $current_date = date('Y-m-d');
    $order_paid_date = '';
    $order = wc_get_order( $order_id );
    $order_items  = $order->get_items();
    $array_id = array();
    $json = WC()->session->get(MPT_SESSION_JSON_KEY);
    if ($json) {
        $jsonPhp = json_decode($json, true);
        $orderPaidDateSession = WC()->session->get('orderPaidDateSession');
        if ($orderPaidDateSession) {
            $old_order_id = WC()->session->get('oldOrderId');
            if ( $old_order_id )
            {
                montepisanotree_delete_token( $old_order_id );
                montepisanotree_add_already_renewed_to_oldorder($old_order_id);
            }
            $jsonPhp['renewal_paid_date'] = date("Y-m-d",strtotime($orderPaidDateSession));
        } else {
            $jsonPhp['first_paid_date'] = date("Y-m-d");
        }
        WC()->session->set( 'orderPaidDateSession', null );
        WC()->session->set( 'oldOrderId', null );
        
        foreach ($jsonPhp as $type => $arr) {
            if ($type == 'renewal_paid_date') {
                $order_paid_date = $arr;
                $next_order_paid_date = date("Y-m-d", strtotime("+1 years", strtotime($arr)));
                update_field(MPT_ORDER_PAID_DATE, $next_order_paid_date, $order_id);
            } elseif ($type == 'first_paid_date') {
                $first_order_paid_date = date("Y-m-d", strtotime($arr));
                update_field(MPT_ORDER_PAID_DATE, $first_order_paid_date, $order_id);
            }
        }
        foreach ($jsonPhp as $type => $arr) {
            if (is_array($arr)) :
                foreach ($arr as $k => $data) :
                    if (isset($data['id'])) {
                        $array_id[] = $data['id'];
                        if ($order_paid_date) {
                            $next_order_paid_date = date("Y-m-d", strtotime($next_order_paid_date));
                            update_field( MPT_POI_PAID_DATE , $next_order_paid_date , $data['id'] );
                            update_field_and_hoqu($data['id']);
                        } else {
                            update_field( MPT_POI_PAID_DATE , $current_date , $data['id'] );
                            update_field_and_hoqu($data['id']);
                        }
                    }
                endforeach;
                if (in_array(strtolower($type), $dedicationProducts)) :
                    foreach ($arr as $k => $data) :
                        if (isset($data['id'])) {
                            if (isset($posted_data[$data['id']]))
                            {
                                $jsonPhp[$type][$k]['dedication'] = sanitize_text_field($posted_data[$data['id']]);
                            }
                            
                        }
                    endforeach;
                endif;
            endif;
        }
        
        
        $json = json_encode($jsonPhp, JSON_UNESCAPED_UNICODE);
        update_field(MPT_ORDER_JSON_KEY, $json, $order_id);

        
    }
    $count = 0;
    foreach ( $order_items as $item_id => $item ) {
        // Added the function to save poi_id to item meta data
        wc_add_order_item_meta($item_id, 'idpoi', $array_id[$count]);
        $count ++;
    }
}, 10, 3);


function update_field_and_hoqu($post_id) {
    update_field( 'color' , '#dd3333' , $post_id );
    $post = get_post( $post_id );
    update_poi_job_hoqu( $post_id, $post, true );
}