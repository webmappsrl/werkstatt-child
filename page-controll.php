<?php /* Template Name: page-controll */ ?>
<?php 
	$VC = class_exists('WPBakeryVisualComposerAbstract'); 
	$enable_pagepadding = get_post_meta(get_the_ID(), 'enable_pagepadding', true);
	
	$classes[] = $enable_pagepadding === 'on' ? 'page-padding' :  false;
?>
<?php get_header(); ?>
<?php

$arg = array(
        'limit' => 1000,
        'status' => array('completed','processing'),
    );
    
    $orders = wc_get_orders($arg);
        foreach ($orders as $order ){
         foreach( $order->get_items() as $item_id => $item ){
           $product_name_variation = $item->get_name();
           $product_name = preg_replace('/[^0-9]/', '', $product_name_variation);//substr($product_name_variation,0,3);
           global $woocommerce;
            $cart_items = $woocommerce->cart->get_cart();
            foreach($cart_items as $cart_item => $values) { 
                $_product =  wc_get_product( $values['data']->get_id()); 
                $cart_item_title = $_product->get_title(); 
                //echo $cart_item_title;
                if ($product_name == $cart_item_title ) {
                    $counter = true;
                    $current_order_id = $item['order_id'];
                    $order_meta = get_post_meta($current_order_id);
                    $current_paid_date = $order_meta[_paid_date][0];
                    $next_availible_date = date("Y-m-d", strtotime("+1 years +1 days", strtotime($order_meta[_paid_date][0])));
                    $current_date = date ("Y-m-d");
                   } 
            } 
          }
        }
        if ( $counter == true &&  $current_date<$next_availible_date){
            
             global $woocommerce;
             $woocommerce->cart->empty_cart();
              
        } else {
			wc_get_checkout_url();
       $url_proceed = esc_url( wc_get_checkout_url() );
       echo '<script type="text/javascript">
           window.location = "'.$url_proceed.'"
      </script>';
			// echo '
			// <div class="row">
			// 	<div class="small-12 columns">
			// 		<div class="post-content no-vc">
			// 		<a href='.$url_proceed.' class="checkout-button button alt wc-forward">procedi al checkout</a>
			// 		</div>
			// 	</div>
			// </div>';
            
        }
			
			?>
<?php get_footer(); ?>