<?php
define('MPT_RENEW_ORDERS_TOKENS', 'mpt_renew_order_tokens');

function montepisanotree_generate_token(){
    //Generate a random string.
    $bin = openssl_random_pseudo_bytes(32, $is_secure);
    //Convert the binary data into hexadecimal representation.
    $token = bin2hex($bin);
    //Print it out for example purposes.
    return $token;
}

function montepisanotree_add_token( $order_id ){
    $option = get_option( MPT_RENEW_ORDERS_TOKENS );
    if ( $option === FALSE )//option doesnt exists
    {
        $option = [];
    }

    $token = montepisanotree_generate_token();
    $option[$order_id] = $token;
    update_option( MPT_RENEW_ORDERS_TOKENS , $option );
    return $token;
}

function montepisanotree_check_token( $order_id , $token ){}