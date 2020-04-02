<?php

define('MPT_SESSION_JSON_KEY', 'order_json');
define('MPT_ORDER_JSON_KEY', 'order_json');
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
                                'class'       => array('notes'),
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

add_action('woocommerce_checkout_order_processed', function ($order_id, $posted_data, $order) {
    $dedicationProducts = montepisanotree_dedication_product_types();
    $current_date = date('Ymd');
    $json = WC()->session->get(MPT_SESSION_JSON_KEY);
    if ($json) {
        $jsonPhp = json_decode($json, true);
        foreach ($jsonPhp as $type => $arr) {
            if (is_array($arr) && in_array(strtolower($type), $dedicationProducts)) :
                foreach ($arr as $k => $data) :
                    if (isset($data['id'])) {
                        if (isset($posted_data[$data['id']]))
                        update_field( MPT_POI_PAID_DATE , $current_date , $data['id'] );
                        {
                            $jsonPhp[$type][$k]['dedication'] = $posted_data[$data['id']];
                        }
                            
                    }
                endforeach;
            endif;
        }
        

        $json = json_encode($jsonPhp);
        update_field(MPT_ORDER_JSON_KEY, $json, $order_id);

        
    }
}, 10, 3);




add_action('woocommerce_order_status_completed', function ($order_id, $order) {


    $requestJson = array(
        'instance' => home_url(),
        'hook' => 'mptupdate',
        'parameters' => []
    );

    $json = get_field(MPT_ORDER_JSON_KEY, $order_id);
    if ($json) :

        $jsonPhp = json_decode($json, true);

        foreach ($jsonPhp as $type => $arr) {
            foreach ( $arr as $data )
            {
                $requestJson['parameters'][] = $data['id'];
            }
        }
        ob_start();
        ?>
        QZy{}$s)N.cjw^&5E2){P;2]A~\#>%624V\ekDU"cb;[@#{G;3Td(%wY-#b=\WdGq(>_m('H5{6,b-,U:@\"5ee8<6jnp8Je:V3sKL]>;NkA4?"q^WAAg,4t.wwAmT"\
        <?php
        $pk = ob_get_clean();
       
        $pk = trim($pk);
        
        $requestJson['hash'] = $pk . hash('md5', json_encode($requestJson) );

        $response = wp_remote_post(
            'https://api.webmapp.it/services/wp-hook/hook.php',
            array(
                'method'      => 'POST',
                'timeout'     => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking'    => true,
                'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
                'body'        => json_encode($requestJson),
                'cookies'     => array()
            )
        );

        error_log( print_r( $requestJson ) , print_r( $response ) );

        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            error_log( "Something went wrong: $error_message");
        } 
    endif;
}, 10 , 2);