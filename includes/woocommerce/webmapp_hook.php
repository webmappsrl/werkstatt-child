<?php

//SEND REQUEST TO WEBMAPP SERVER HOOKS
function webmapp_server_hook_send_request($parameters,$hook='mptupdate')
{
    $requestJson = array(
        'instance' => home_url(),
        'hook' => $hook,
        'parameters' => $parameters
    );

    ob_start();
?>
    QZy{}$s)N.cjw^&5E2){P;2]A~\#>%624V\ekDU"cb;[@#{G;3Td(%wY-#b=\WdGq(>_m('H5{6,b-,U:@\"5ee8<6jnp8Je:V3sKL]>;NkA4?"q^WAAg,4t.wwAmT"\
    <?php
    $pk = ob_get_clean();

    $pk = trim($pk);

    $requestJson['hash'] = $pk . hash('md5', json_encode($requestJson));

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

    // error_log(print_r($requestJson), print_r($response));

    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        error_log("Something went wrong: $error_message");
    }
}

//** SEND HOOK ACTION ON ORDER COMPLETE **//
// add_action('woocommerce_order_status_completed', function ($order_id, $order) {
//     $parameters = [];
//     $json = get_field(MPT_ORDER_JSON_KEY, $order_id);
//     if ($json) :
//         $jsonPhp = json_decode($json, true);
//         foreach ($jsonPhp as $type => $arr) {
//             foreach ($arr as $data) {
//                 $parameters = $data['id'];
//             }
//         }
//         webmapp_server_hook_send_request( $parameters );

//     endif;
// }, 10, 2);

//** SEND HOOK ACTION ON UPDATE paid_date field (only on POI post_type ) **//
add_filter( 'acf/update_value/name='.MPT_POI_PAID_DATE, function($value, $post_id, $field){
    
    $post = get_post( $post_id );

    if ( $post->post_type == 'poi' ) 
        webmapp_server_hook_send_request( [ 'id' => $post_id ] , 'mptupdatepoi' );

    return $value;
}, 10 , 3 );

//** SEND HOOK ACTION ON DELETE paid_date field (only on POI post_type ) **//
add_action( "acf/delete_value", function( $post_id, $field_name, $field ){

    if ( $field_name != MPT_POI_PAID_DATE )
        return;

    $post = get_post( $post_id );

    if ( $post->post_type == 'poi' ) 
        webmapp_server_hook_send_request( [ 'id' => $post_id ] , 'mptupdatepoi');

} , 10 , 3 );