<?php
/**
 * Werkstatt child theme functions and definitions
 */

/*-----------------------------------------------------------------------------------*/
/* Include the parent theme style.css
/*-----------------------------------------------------------------------------------*/

require_once 'includes/woocommerce/checkout_functions.php';
require_once 'includes/woocommerce/webmapp_hook.php';
require_once 'includes/woocommerce/renewal_token.php';

add_action('wp_enqueue_scripts', 'theme_enqueue_styles');
function theme_enqueue_styles()
{
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_script('wc-add-to-cart-variation');
}

// /** action for title section of single product */
// add_action('poi_single_product_title','my_function_poi_title',15);
// function my_function_poi_title(){

// };

/** add a filter to modify and customize breadcrumb parameters */
add_filter('woocommerce_get_breadcrumb', 'custom_get_breadcrumb', 20, 2);
function custom_get_breadcrumb($crumbs, $breadcrumb)
{

    // The Crump item to target
    $target = __('POI', 'woocommerce');

    foreach ($crumbs as $key => $crumb) {
        if ($target === $crumb[0]) {
            // 1. Change name
            $crumbs[$key][0] = __('Mappa', 'woocommerce');

            // 2. Change URL (you can also use get_permalink( $id ) with the post Id
            $crumbs[$key][1] = home_url('/mappa');
        }
    }
    return $crumbs;
}

/** action for price and add to card section for single product */
function wc_remove_all_quantity_fields($return, $product)
{
    return true;
}
add_filter('woocommerce_is_sold_individually', 'wc_remove_all_quantity_fields', 10, 2);

// add_action('poi_single_product_summary', 'custom_woocommerce_single_product_summary', 15);
// function custom_woocommerce_single_product_summary()
// {
//     $arg = array(
//         'limit' => 1000,
//         'status' => array('completed', 'processing'),
//     );
//     $orders = wc_get_orders($arg);
//     foreach ($orders as $order) {
//         foreach ($order->get_items() as $item_id => $item) {
//             $product_name_variation = $item->get_name();
//             $product_name = preg_replace('/[^0-9]/', '', $product_name_variation); //substr($product_name_variation,0,3);
//             global $product;
//             $current_product_name = $product->name;
//             if ($product_name == $current_product_name) {
//                 $counter = true;
//                 $current_order_id = $item['order_id'];
//                 $order_meta = get_post_meta($current_order_id);
//                 $current_paid_date = $order_meta['_paid_date'][0];
//                 $next_availible_date = date("Y-m-d", strtotime("+1 years +1 days", strtotime($order_meta['_paid_date'][0])));
//                 $current_date = date("Y-m-d");
//             }
//         }
//     }
//     if ($counter == true &&  $current_date < $next_availible_date) {
//         mostraProdottoComprato($current_order_id);
//     } else {
//         mostraPrezzo();
//     }
// }

function mostraPulsanteAdotta($post_id)
{
    $paid_date = get_field('paid_date', $post_id);
    if (isset($paid_date) &&  $paid_date) {
        return;
    } else {
        echo do_shortcode('<div class="button-adottaora large-3 columns">[thb_button link="url:%23adottaora|title:Adotta%20ora!||"]</div>');
    }
}

function mostraProdottoComprato($paid_date)
{
    // $order_meta = get_post_meta($current_order_id);

    $mesi = array(
        1 => 'gennaio', 'febbraio', 'marzo', 'aprile',
        'maggio', 'giugno', 'luglio', 'agosto',
        'settembre', 'ottobre', 'novembre', 'dicembre'
    );

    list($giorno, $mese, $anno) = explode('-', date("d-n-Y", strtotime("+1 years +1 days", strtotime(str_replace('/', '-', $paid_date)))));

    //' .$order_meta[_billing_first_name][0].' '. $order_meta[_billing_last_name][0].' nome e cognome del acquirente
    echo '<h2 class="gia-adottato">L\'albero è stato già adottato fino al ' . $giorno, ' ', $mesi[$mese], ' ', $anno . '!</h2>';
}
function mostraPrezzo($post_id)
{
    echo '<h2 id="adottaora">Adotta per un anno!</h2>';
    //woocommerce_template_single_price();
    echo '<p>Scegli fra le nostre tre opzioni di adozione, a partire da €9 all\'anno. Questo versamento permetterà di fornire le cure necessarie alla pianta e di mantenere in vita il progetto.</p>';
    
    $cart = WC()->cart->get_cart();
    $cart_has_poi_id = '';
    $cart_has_poi_name = '';
    $cart_product_id = '';
    foreach ( $cart as $key => $val){
        $poi_id = $val['idpoi'];
        $product_id = $val['product_id'];
        $name = $val['data']->get_name();
        if ($post_id == $poi_id){
            $cart_has_poi_id = $poi_id;
            $cart_product_id = $product_id;
            $cart_has_poi_name = $name;
        }
    }
    if (!empty($cart_has_poi_id)) {
        echo '<p><strong>Modifica il tuo acquisto cambiando opzione di adozione</strong></p>';
        // echo $cart_has_poi_name;
    }

    
    $product_friendship = get_page_by_title( 'Friendship', OBJECT, 'product' );
    $product_love = get_page_by_title( 'Love', OBJECT, 'product' );
    $product_passion = get_page_by_title( 'Passion', OBJECT, 'product' );
    $friendship = wc_get_product($product_friendship->ID);
    $love = wc_get_product($product_love->ID);
    $passion = wc_get_product($product_passion->ID);
    ?>

    <form class="cart" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $friendship->get_permalink())); ?>" method="post" enctype='multipart/form-data'>
        <input name="idpoi" type="hidden" value="<?= $post_id ?>">
        <button type="submit" data-poi="<?= $post_id ?>" name="add-to-cart" value="<?php echo esc_attr($friendship->get_id()); ?>" class="single_add_to_cart_button button alt" <?php if($cart_has_poi_name == 'Friendship'){ echo 'disabled';} ?>><?php echo esc_html($friendship->get_name()); ?></button>
        <div class="mpt-product-price"><?php echo wc_price($friendship->get_price());?></div>
    </form>
    <div class="mpt-product-desc"><?php echo $friendship->get_description();?></div>

    <form class="cart" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $love->get_permalink())); ?>" method="post" enctype='multipart/form-data'>
        <input name="idpoi" type="hidden" value="<?= $post_id ?>">
        <button type="submit" data-poi="<?= $post_id ?>" name="add-to-cart" value="<?php echo esc_attr($love->get_id()); ?>" class="single_add_to_cart_button button alt" <?php if($cart_has_poi_name == 'Love'){ echo 'disabled';} ?>><?php echo esc_html($love->get_name()); ?></button>
        <div class="mpt-product-price"><?php echo wc_price($love->get_price());?></div>
    </form>
    <div class="mpt-product-desc"><?php echo $love->get_description();?></div>

    <form class="cart" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $passion->get_permalink())); ?>" method="post" enctype='multipart/form-data'>
        <input name="idpoi" type="hidden" value="<?= $post_id ?>">
        <button type="submit" data-poi="<?= $post_id ?>" name="add-to-cart" value="<?php echo esc_attr($passion->get_id()); ?>" class="single_add_to_cart_button button alt" <?php if($cart_has_poi_name == 'Passion'){ echo 'disabled';} ?>><?php echo esc_html($passion->get_name()); ?></button>
        <div class="mpt-product-price"><?php echo wc_price($passion->get_price());?></div>
    </form>
    <div class="mpt-product-desc"><?php echo $passion->get_description();?></div>

    <?php 
    echo '<div class="back-to-map"><a href="'.home_url().'/mappa">o torna alla mappa</a></div>';
}

/**
 * DEBUG TOOLS
 * Debug email content
 * TODO: remove this hook usage
 add_filter( 'wp_mail', function($wp_mail){
     $wp_mail['to'] = 'alessiopiccioli@webmapp.it';
     return $wp_mail;
    } , 10 , 1 );
*/


// Adds custom input data to WC_CART
function wm_add_poi_id_to_cart_item( $cart_item_data, $product_id, $variation_id ) {
    // $post_id = filter_input( INPUT_POST, 'idpoi' );
    
    $post_id = intval($_POST['idpoi']);
    $post_ded = $_POST['dedpoi'];

    $post = get_post($post_id);

    if (!$post instanceof WP_Post) {
        throw new Exception ('Invalid Post ID provided!');
        return false;
    }

    if ( empty( $post_id ) ) {
        
        return $cart_item_data;
    }

    $items = WC()->cart->get_cart();
    foreach ( $items as $key => $data )
    {
        if ( isset( $data['idpoi'] ) && $data['idpoi']==$post_id)
        {
            WC()->cart->remove_cart_item( $key );
        }
    }

    $cart_item_data['idpoi'] = $post_id;
    if ($post_ded) {
        $cart_item_data['dedpoi'] = $post_ded;
    }
 
    return $cart_item_data;
}
 
add_filter( 'woocommerce_add_cart_item_data', 'wm_add_poi_id_to_cart_item', 10, 3 );


// display Poi thumbnail in cart
function wm_poi_thumb_title_cat_cart( $product_get_image, $cart_item, $cart_item_key ) {
   
    if ( empty( $cart_item['idpoi'] ) ) {
        return $product_get_image;
    }
    $product_get_image = '';
    $product_get_image = get_the_post_thumbnail($cart_item['idpoi']);
    $poi_title = get_the_title( $cart_item['idpoi'] );
    $terms = get_the_terms( $cart_item['idpoi'] , 'webmapp_category' );
    $product_get_image .= '<div class="cart-item-cat-title">'.$terms[0]->name . ' - ' . $poi_title.'</div>';

 
    return $product_get_image;
}
add_filter( 'woocommerce_cart_item_thumbnail', 'wm_poi_thumb_title_cat_cart', 10, 3 );

// display Poi thumbnail in cart
function wm_poi_thumb_title_cat_cart_permalink( $product_get_permalink, $cart_item, $cart_item_key ) {
   
    if ( empty( $cart_item['idpoi'] ) ) {
        return $product_get_permalink;
    }
    $product_get_permalink = '';
    $product_get_permalink = get_permalink($cart_item['idpoi']);
 
    return $product_get_permalink;
}
add_filter( 'woocommerce_cart_item_permalink', 'wm_poi_thumb_title_cat_cart_permalink', 10, 3 );


// bonifico only for logged in users
add_filter( "woocommerce_available_payment_gateways", "wm_mpt_filter_gateways", 100 );

function wm_mpt_filter_gateways($args) {
 if(!is_user_logged_in() && isset($args['bacs'])) {
  unset($args['bacs']);
 }
 return $args;
}

/** change Aggiungi al carrello text */

function woo_custom_cart_button_text()
{
    return __('Adotta ora!', 'woocommerce');
}
add_filter('woocommerce_product_single_add_to_cart_text', 'woo_custom_cart_button_text');


/** Add the product attribute ( modalita ) to the product name in cart page ( carrello ) */
add_filter('woocommerce_cart_item_name', 'add_variations_in_cart', 10, 3);
function add_variations_in_cart($name, $cart_item, $item_key)
{
    $product_variation = '';
    if (!empty($cart_item['variation_id']) && $cart_item['variation_id'] != 0) {
        if (is_array($cart_item['variation']) && !empty($cart_item['variation'])) {
            foreach ($cart_item['variation'] as $key => $value) {
                $product_variation .= '<span class="product-attribute-carrello"> - ' . ucfirst($value) . '</span>';
            }
        }
    }

    echo $name . $product_variation;
}



add_filter('gettext', 'bbloomer_translate_woocommerce_strings', 999, 3);

function bbloomer_translate_woocommerce_strings($translated, $text, $domain)
{

    // STRING 1
    $translated = str_ireplace('Your personal data will be used to process your order, support your experience throughout this website, and for other purposes described in our', 'I tuoi dati personali saranno utilizzati per elaborare il tuo ordine, supportare la tua esperienza su questo sito web e per altri scopi descritti nella nostra', $translated);

    // ETC.

    return $translated;
}

add_action('wc_get_privacy_policy_text', 'custom_get_privacy_policy_text', 20, 2);
function custom_get_privacy_policy_text($type = '')
{
    $text = '';

    switch ($type) {
        case 'checkout':
            /* translators: %s privacy policy page name and link */
            $text = get_option('woocommerce_checkout_privacy_policy_text', sprintf(__('I tuoi dati personali saranno utilizzati per elaborare il tuo ordine, supportare la tua esperienza su questo sito web e per altri scopi descritti nella nostra %s.', 'woocommerce'), '[privacy_policy]'));
            break;
        case 'registration':
            /* translators: %s privacy policy page name and link */
            $text = get_option('woocommerce_registration_privacy_policy_text', sprintf(__('Your personal data will be used to support your experience throughout this website, to manage access to your account, and for other purposes described in our %s.', 'woocommerce'), '[privacy_policy]'));
            break;
    }

    return trim(apply_filters('woocommerce_get_privacy_policy_text', $text, $type));
}


//apply_filters('woocommerce_get_privacy_policy_text', $text, $type);
$text = get_option('woocommerce_checkout_privacy_policy_text', sprintf(__('I tuoi dati personali saranno utilizzati per elaborare il tuo ordine, supportare la tua esperienza su questo sito web e per altri scopi descritti nella nostra %s.', 'woocommerce'), '[privacy_policy]'));


add_action('woocommerce_review_order_before_submit', 'add_privacy_checkbox', 9);
function add_privacy_checkbox()
{
    woocommerce_form_field('privacy_policy', array(
        'type' => 'checkbox',
        'class' => array('form-row privacy'),
        'label_class' => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'),
        'input_class' => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'),
        'required' => true,
        'label' => 'Dichiaro di aver letto e accettato la <a href="https://montepisanotree.org/privacy-policy">Privacy Policy</a>',
    ));
}

add_action('woocommerce_checkout_process', 'privacy_checkbox_error_message');
function privacy_checkbox_error_message()
{
    if (!(int)isset($_POST['privacy_policy'])) {
        wc_add_notice(__('Devi accettare la nostra politica sulla privacy per procedere'), 'error');
    }
}


// torna al negozio link
function wc_empty_cart_redirect_url()
{
    return 'https://montepisanotree.org/mappa/';
}
add_filter('woocommerce_return_to_shop_redirect', 'wc_empty_cart_redirect_url');


/**
 * Handle a custom 'customvar' query var to get orders with the 'customvar' meta.
 * @param array $query - Args for WP_Query.
 * @param array $query_vars - Query vars from WC_Order_Query.
 * @return array modified $query
 */
// function handle_custom_query_var( $query, $query_vars ) {
// 	if ( ! empty( $query_vars['_paid_date'] ) ) {
//         $current_paid_date = date("d/m/Y", $query_vars['_paid_date']);
// 		$query['meta_query'][] = array(
// 			'key' => '_paid_date',
// 			'value' => esc_attr( $query_vars['_paid_date'] ),
// 		);
// 	}

// 	return $query;
// }
// add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', 'handle_custom_query_var', 10, 2 );

// Filter to remove the remove item x from cart and checkout page when a product is being renewed
add_filter('woocommerce_cart_item_remove_link', 'wm_customized_cart_item_remove_link', 20, 2 );
function wm_customized_cart_item_remove_link( $button_link, $cart_item_key ){

    $old_order_id = WC()->session->get('oldOrderId');
    if ( $old_order_id )
    {
        $button_link = '';
    }
    return $button_link;
}

// gives an array of order's type of modality. takes the order object.
function montepisanotree_tree_modality_types($order)
{
    $json = get_field("order_json", $order->ID);
    $treesmodalityallowed = array(
        'friendship',
        'passion',
        'love'
    );
    $tree_types = array();
    if ($json) {
        $jsonPhp = json_decode($json, true);
        if (is_array($jsonPhp)) {
            foreach ($jsonPhp as $type => $arr) {
                if (in_array(strtolower($type), $treesmodalityallowed)) {
                    array_push($tree_types, strtolower($type));
                }
            }
        }
    }
    return $tree_types;
}

// gives an array of ids of trees in the order only if their modality is love or passion. takes the order object.
function montepisanotree_tree_quantity_inorder($order)
{
    $json = get_field("order_json", $order->ID);
    $treesmodalityallowed = array(
        'passion',
        'love'
    );
    $tree_quantity = array();
    if ($json) {
        $jsonPhp = json_decode($json, true);
        if (is_array($jsonPhp)) {
            foreach ($jsonPhp as $type => $arr) {
                if (in_array(strtolower($type), $treesmodalityallowed)) {
                    foreach ($arr as $tree) {
                        array_push($tree_quantity, $tree['id']);
                    }
                }
            }
        }
    }
    return $tree_quantity;
}

// checks the order_json to see if its already renewed 
function montepisanotree_order_is_already_renewed($order)
{
    $json = get_field("order_json", $order->ID);
    $typeallowed = array(
        'already_renewed',
        'already_expired'
    );
    $renewal_type = array();
    if ($json) {
        $jsonPhp = json_decode($json, true);
        if (is_array($jsonPhp)) {
            foreach ($jsonPhp as $type => $arr) {
                if (in_array(strtolower($type), $typeallowed)) {
                        array_push($renewal_type, $type);
                }
            }
        }
    }
    return $renewal_type;
}


// checks the order_json to see if its has renewal paid date to personilize emails 
function montepisanotree_order_is_renewal($order)
{
    $json = get_field("order_json", $order->ID);
    $typeallowed = array(
        'renewal_paid_date',
    );
    $renewal_type = array();
    if ($json) {
        $jsonPhp = json_decode($json, true);
        if (is_array($jsonPhp)) {
            foreach ($jsonPhp as $type => $arr) {
                if (in_array(strtolower($type), $typeallowed)) {
                        array_push($renewal_type, $type);
                }
            }
        }
    }
    return $renewal_type;
}

// adds the already renewed parameter to an order_json og a give order_id
function montepisanotree_add_already_renewed_to_oldorder($order_id)
{
    $json = get_field("order_json", $order_id);
    if ($json) {
        $jsonPhp = json_decode($json, true);
        if (is_array($jsonPhp)) {
            $jsonPhp['already_renewed'] = date("Y-m-d");
            $json = json_encode($jsonPhp);
            update_field('order_json', $json, $order_id);
        }
    }
}


// adds the already expired parameter to an order_json og a give order_id
function montepisanotree_add_already_expired_to_oldorder($order_id)
{
    $json = get_field("order_json", $order_id);
    if ($json) {
        $jsonPhp = json_decode($json, true);
        if (is_array($jsonPhp)) {
            $jsonPhp['already_expired'] = date("Y-m-d");
            $json = json_encode($jsonPhp);
            update_field('order_json', $json, $order_id);
        }
    }
}


// changes the subject of WC mail completed if the order has only friendship modality tree
add_filter('woocommerce_email_subject_customer_completed_order', 'change_client_email_subject_order_complete', 1, 2);
function change_client_email_subject_order_complete( $subject, $order ) {
    $tree_types = montepisanotree_tree_modality_types($order);
    $renewal_type = montepisanotree_order_is_renewal($order);
    if (in_array("love", $tree_types) == false || $renewal_type[0] == 'renewal_paid_date') {
        $subject = "Adozione confermata";
    }else {
        $subject = "La tua targhetta è pronta!";
    }
	return $subject;
}

// Add new order status for email notification from processing to on-hold il lavorazione a sospeso
add_filter( 'woocommerce_email_actions', 'add_another_email_action' );
    function add_another_email_action( $array ) {
    $array[]='woocommerce_order_status_processing_to_on-hold';
    return $array;
}
add_action( 'woocommerce_email', 'hook_another_email_on_hold' );
    function hook_another_email_on_hold( $email_class ) {
    add_action( 'woocommerce_order_status_processing_to_on-hold_notification', array( $email_class->emails['WC_Email_Customer_On_Hold_Order'], 'trigger' ) );
}

// wm_write_log_file($err_contact_search,'a+','contactHS_error_search');
// writes logs into a file in upload directory
function wm_write_log_file($entry, $mode = 'a', $file = 'mpt-orders') {
    // Get WordPress uploads directory.
    $upload_dir = wp_upload_dir();
    $upload_dir = $upload_dir['basedir'];

    // If the entry is array, json_encode.
    $entry = json_encode( $entry ); 
    if (!file_exists($upload_dir.'/wc-orders')) {
        mkdir($upload_dir.'/wc-orders', 0777, true);
    }
    // Write the log file.
    $file  = $upload_dir . '/wc-orders/' . $file . '.log';
    $file  = fopen( $file, $mode );
    $bytes = fwrite( $file, current_time( 'mysql' ) . "\n" ); 
    $bytes = fwrite( $file, $entry . "\n\n" ); 
    fclose( $file ); 

    return $bytes;
}