<?php

define('MPT_SESSION_JSON_KEY', 'order_json');
define('MPT_ORDER_JSON_KEY', 'order_json');

function montepisanotree_dedication_product_types(){
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
        $poi_title = get_the_title($poi_id);
        $name = $val['data']->get_name();
        $item['id'] = $poi_id;
        $item['title'] = $poi_title;
        $item['dedication'] = '';
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
    echo '<pre>';
    echo json_encode($order_json);
    echo '</pre>';
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
                            $fields['order'][$data['id']] = array(
                                'type'        => 'textarea',
                                'label'       => __('Dedication for tree', 'woocommerce') . ' ' . $data['id'] . ' ' . ucfirst($type),
                                'class'       => array( 'notes' ),
                                'required'    => false,
                                'placeholder' => esc_attr__('Insert here your tree dedication', 'woocommerce'),
                            );
                        }
                    }
                endif;
            }
        }
    }

    return $fields;
});

add_action( 'woocommerce_checkout_order_processed', function ($order_id, $posted_data, $order){
    $dedicationProducts = montepisanotree_dedication_product_types();
    $json = WC()->session->get(MPT_SESSION_JSON_KEY);
    if ($json) {
        $jsonPhp = json_decode($json, true);
        foreach ($jsonPhp as $type => $arr) 
        {
            if ( is_array($arr) && in_array(strtolower($type), $dedicationProducts) ) :
                foreach ($arr as $k => $data) :
                    if ( isset($data['id']) ) {
                        if ( isset( $posted_data[$data['id']] ) )
                            $jsonPhp[$type][$k]['dedication'] = $posted_data[$data['id']];
                       
                    }
                endforeach;
            endif;
        }

        $json = json_encode($jsonPhp);
        update_field( MPT_ORDER_JSON_KEY , $json , $order_id );
    }

}, 10 ,3 );

