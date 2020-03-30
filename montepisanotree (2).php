<?php 
//wp-content/plugins/woocommerce/templates/single-product/add-to-cart/simple.php 
?>
<button type="submit" data-poi="<?= $poi_id?>" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
<?php

add_filter( 'woocommerce_add_to_cart_validation', function( true, $product_id, $quantity ){
    $canUserAddThisToCart = true;
    $poi_id = $_POST['poi'];

    $poi = get_post( $poi_id );
    $poi_status = get_field('poi_status', $poi_id);
    if ( $poi_status != 'available')
    {
        $canUserAddThisToCart = false;
        add_filters( 'woocommerce_cart_redirect_after_error', function( $permalink , $product_id ){
            return add_query_arg('error','Someone has already bought this tree', $permalink );
            //https://test.com/product/tree-1
            //https://test.com/product/tree-1?error=Someone%20has%20already%20bought%20this%20tree
        }, 10,2 )
    }

    return $canUserAddThisToCart;
} , 10 , 3 );


add_action('woocommerce_ajax_added_to_cart', function($product_id){
    $poi_id = $_POST['poi'];
    //update meta of cart item
})