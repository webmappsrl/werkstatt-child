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
    $order_paid_date = $_POST['orderPaidDate'];
    
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
                                'default'     => $data['dedication']
                            );
                        }
                    }
                endif;
            }
        }
    }
    return $fields;
});

add_action('woocommerce_checkout_order_processed', function ($order_id, $posted_data, $order) {
    $dedicationProducts = montepisanotree_dedication_product_types();
    $current_date = date('Ymd');
    $order_paid_date = '';
    $order = wc_get_order( $order_id );
    $order_items  = $order->get_items();
    $array_id = array();
    $json = WC()->session->get(MPT_SESSION_JSON_KEY);
    if ($json) {
        $jsonPhp = json_decode($json, true);
        $orderPaidDateSession = WC()->session->get('orderPaidDateSession');
        if ($orderPaidDateSession) {
            $jsonPhp['paid_date'] = date("d/m/Y",strtotime($orderPaidDateSession));
        } else {
            $jsonPhp['first_paid_date'] = date("d/m/Y");
        }
        WC()->session->set( 'orderPaidDateSession', null );
        foreach ($jsonPhp as $type => $arr) {
            if ($type == 'paid_date') {
                $order_paid_date = $arr;
                $next_order_paid_date = date("Ydm", strtotime("+1 years", strtotime($arr)));
                update_field(MPT_ORDER_PAID_DATE, $next_order_paid_date, $order_id);
            } elseif ($type == 'first_paid_date') {
                $first_order_paid_date = date("Ydm", strtotime($arr));
                update_field(MPT_ORDER_PAID_DATE, $first_order_paid_date, $order_id);
            }
        }
        foreach ($jsonPhp as $type => $arr) {
            if (is_array($arr)) :
                foreach ($arr as $k => $data) :
                    if (isset($data['id'])) {
                        $array_id[] = $data['id'];
                        if ($order_paid_date) {
                            $next_order_paid_date = date("Ymd", strtotime($next_order_paid_date));
                            update_field( MPT_POI_PAID_DATE , $next_order_paid_date , $data['id'] );
                        } else {
                            update_field( MPT_POI_PAID_DATE , $current_date , $data['id'] );
                        }
                    }
                endforeach;
                if (in_array(strtolower($type), $dedicationProducts)) :
                    foreach ($arr as $k => $data) :
                        if (isset($data['id'])) {
                            if (isset($posted_data[$data['id']]))
                            {
                                $jsonPhp[$type][$k]['dedication'] = $posted_data[$data['id']];
                            }
                            
                        }
                    endforeach;
                endif;
            endif;
        }
        

        $json = json_encode($jsonPhp);
        update_field(MPT_ORDER_JSON_KEY, $json, $order_id);

        
    }
    $count = 0;
    foreach ( $order_items as $item_id => $item ) {
        // Added the function to save poi_id to item meta data
        wc_add_order_item_meta($item_id, 'idpoi', $array_id[$count]);
        $count ++;
    }
}, 10, 3);