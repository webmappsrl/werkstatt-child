<?php /* Template Name: page-renewal */ ?>
<?php 
	$VC = class_exists('WPBakeryVisualComposerAbstract'); 
	$enable_pagepadding = get_post_meta(get_the_ID(), 'enable_pagepadding', true);
	
	$classes[] = $enable_pagepadding === 'on' ? 'page-padding' :  false;
?>
<?php get_header(); ?>
<div style="padding:150px 50px;">
<?php

    $order_id = $_GET['order_id'];
    
    $order = wc_get_order($order_id);
    
    WC()->cart->empty_cart();

    $current_json = get_field('order_json',$order_id);
    $current_json = json_decode($current_json);
    $order_paid_date = get_field('order_paid_date',$order_id);
    if ($order_paid_date) {
        WC()->session->set('orderPaidDateSession', date('Ymd',strtotime($order_paid_date)));
    }
    foreach ($current_json as $modality => $items) {
        // echo $modality;
        if (is_array($items)) :
        foreach ( $items as $item) {
            $product = get_page_by_title( $modality, OBJECT, 'product' );
            // print_r( $product->ID);
            // print_r( $item->id);
            $_POST['idpoi'] = $item->id;
            $_POST['dedpoi'] = $item->dedication;
            WC()->cart->add_to_cart( $product->ID );
        }
        endif;
    }
    // echo '<pre>';
    // print_r($current_json);    
    // echo '</pre>';
        
    wp_safe_redirect( wc_get_checkout_url() );
    exit();
    
			
?>
</div>
<?php get_footer(); ?>