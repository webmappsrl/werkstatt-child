<?php
/**
 * Werkstatt child theme functions and definitions
 */

/*-----------------------------------------------------------------------------------*/
/* Include the parent theme style.css
/*-----------------------------------------------------------------------------------*/

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

add_action('poi_single_product_summary', 'custom_woocommerce_single_product_summary', 15);
function custom_woocommerce_single_product_summary()
{
    $arg = array(
        'limit' => 1000,
        'status' => array('completed', 'processing'),
    );
    $orders = wc_get_orders($arg);
    foreach ($orders as $order) {
        foreach ($order->get_items() as $item_id => $item) {
            $product_name_variation = $item->get_name();
            $product_name = preg_replace('/[^0-9]/', '', $product_name_variation); //substr($product_name_variation,0,3);
            global $product;
            $current_product_name = $product->name;
            if ($product_name == $current_product_name) {
                $counter = true;
                $current_order_id = $item['order_id'];
                $order_meta = get_post_meta($current_order_id);
                $current_paid_date = $order_meta['_paid_date'][0];
                $next_availible_date = date("Y-m-d", strtotime("+1 years +1 days", strtotime($order_meta['_paid_date'][0])));
                $current_date = date("Y-m-d");
            }
        }
    }
    if ($counter == true &&  $current_date < $next_availible_date) {
        mostraProdottoComprato($current_order_id);
    } else {
        mostraPrezzo();
    }
}
function OLD_mostraPulsanteAdotta()
{
    $arg = array(
        'limit' => 1000,
        'status' => array('completed', 'processing'),
    );
    $orders = wc_get_orders($arg);
    foreach ($orders as $order) {
        foreach ($order->get_items() as $item_id => $item) {
            $product_name_variation = $item->get_name();
            $product_name = preg_replace('/[^0-9]/', '', $product_name_variation); //substr($product_name_variation,0,3);
            global $product;
            $current_product_name = $product->name;
            if ($product_name == $current_product_name) {
                $counter = true;
                $current_order_id = $item['order_id'];
                $order_meta = get_post_meta($current_order_id);
                $current_paid_date = $order_meta['_paid_date'][0];
                $next_availible_date = date("Y-m-d", strtotime("+1 years +1 days", strtotime($order_meta['_paid_date'][0])));
                $current_date = date("Y-m-d");
            }
        }
    }
    if ($counter == true &&  $current_date < $next_availible_date) { } else {
        echo do_shortcode('<div class="button-adottaora large-3 columns">[thb_button link="url:%23adottaora|title:Adotta%20ora!||"]</div>');
    }
}

function mostraPulsanteAdotta()
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
        echo $cart_has_poi_name;
    }

    $products = wc_get_products(array(
        'category' => array('mpt-category'),
    ));
    // $product_1 = wc_get_product('6887');
    // $product_2 = wc_get_product('6888');
    // $product_3 = wc_get_product('6889');
    $product_1 = wc_get_product('6884');
    $product_2 = wc_get_product('6885');
    $product_3 = wc_get_product('6886');

    
    echo $product_1->get_description();
    echo wc_price($product_1->get_price());
    ?>
    <form class="cart" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product_1->get_permalink())); ?>" method="post" enctype='multipart/form-data'>
        <input name="idpoi" type="hidden" value="<?= $post_id ?>">
        <button type="submit" data-poi="<?= $post_id ?>" name="add-to-cart" value="<?php echo esc_attr($product_1->get_id()); ?>" class="single_add_to_cart_button button alt" <?php if($cart_has_poi_name == 'Friendship'){ echo 'disabled';} ?>><?php echo esc_html($product_1->get_name()); ?></button>

    </form>
    <?php
    
    echo $product_2->get_description();
    echo wc_price($product_2->get_price());
    ?>
    <form class="cart" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product_2->get_permalink())); ?>" method="post" enctype='multipart/form-data'>
        <input name="idpoi" type="hidden" value="<?= $post_id ?>">
        <button type="submit" data-poi="<?= $post_id ?>" name="add-to-cart" value="<?php echo esc_attr($product_2->get_id()); ?>" class="single_add_to_cart_button button alt" <?php if($cart_has_poi_name == 'Love'){ echo 'disabled';} ?>><?php echo esc_html($product_2->get_name()); ?></button>

    </form>
    <?php
    echo $product_3->get_description();
    echo wc_price($product_3->get_price());
    ?>
    <form class="cart" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product_3->get_permalink())); ?>" method="post" enctype='multipart/form-data'>
        <input name="idpoi" type="hidden" value="<?= $post_id ?>">
        <button type="submit" data-poi="<?= $post_id ?>" name="add-to-cart" value="<?php echo esc_attr($product_3->get_id()); ?>" class="single_add_to_cart_button button alt" <?php if($cart_has_poi_name == 'Passion'){ echo 'disabled';} ?>><?php echo esc_html($product_3->get_name()); ?></button>

    </form>
    <?php
        
    echo '<div class="back-to-map"><a href="https://montepisanotree.org/mappa">o torna alla mappa</a></div>';
}

// Adds custom input data to WC_CART
function wm_add_poi_id_to_cart_item( $cart_item_data, $product_id, $variation_id ) {
    // $post_id = filter_input( INPUT_POST, 'idpoi' );
    
    $post_id = intval($_POST['idpoi']);
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
 
    return $cart_item_data;
}
 
add_filter( 'woocommerce_add_cart_item_data', 'wm_add_poi_id_to_cart_item', 10, 3 );

// display Poi title in the cart
function wm_poi_title_text_cart( $item_data, $cart_item ) {
    if ( empty( $cart_item['idpoi'] ) ) {
        return $item_data;
    }
    $poi_title = get_the_title( $cart_item['idpoi'] );
    $item_data[] = array(
        'key'     => __( 'Albero', 'iconic' ),
        'value'   => wc_clean( $poi_title ),
        'display' => '',
    );
 
    return $item_data;
}
 
add_filter( 'woocommerce_get_item_data', 'wm_poi_title_text_cart', 10, 2 );

// Removesitem from cart
// add_action('woocommerce_ajax_added_to_cart', function($product_id){
//     $cart = WC()->cart->get_cart();
//     $cart_has_poi_id = '';
//     $cart_product_id = '';
//     foreach ( $cart as $key => $val){
//         $poi_id = $val['idpoi'];
//         $product_id = $val['product_id'];
//         // if ($post_id == $poi_id){
//             $cart_has_poi_id = $poi_id;
//             $product_cart_id = WC()->cart->generate_cart_id( $product_id );
//             $cart_item_key = WC()->cart->find_product_in_cart( $product_cart_id );
//             if ( $cart_item_key ) WC()->cart->remove_cart_item( $cart_item_key );
//         // }
//     }
// });


// add_filter( 'woocommerce_add_to_cart_validation', 'remove_cart_item_before_add_to_cart', 20, 3 );
// function remove_cart_item_before_add_to_cart( $passed, $product_id, $quantity ) {
//     $cart = WC()->cart->get_cart();
//         $cart_has_poi_id = '';
//         $cart_product_id = '';
//         // foreach ( $cart as $key => $val){
//             $poi_id = $val['idpoi'];
//             // $product_id = $val['product_id'];
//             $product_id = '6887';
//             // if ($post_id == $poi_id){
//                 $cart_has_poi_id = $poi_id;
//                 $product_cart_id = WC()->cart->generate_cart_id( '6887' );
//                 $cart_item_key = WC()->cart->find_product_in_cart( $product_cart_id );
//                 if ( $cart_item_key ) WC()->cart->remove_cart_item( $cart_item_key );
//             // }
//         // }
//     return $passed;
// }

// action to add custom cart data to order
// add_action( 'woocommerce_add_order_item_meta', function ( $itemId, $values, $key ) {
//     if ( isset( $values['myCustomData'] ) ) {
//         wc_add_order_item_meta( $itemId, 'myCustomData', $values['myCustomData'] );
//     }
// }, 10, 3 );

// 
function wm_get_poi_id() {
    $cart = WC()->cart->get_cart();
    $order_json = array();
    foreach ( $cart as $key => $val){
        $item = array();
        $poi_id = $val['idpoi'];
        $poi_title = get_the_title( $poi_id );
        $name = $val['data']->get_name();
        $item['id'] = $poi_id;
        $item['title'] = $poi_title;
        $item['dedication'] = '';
        if($name == 'Friendship') {
            $order_json['friendship'][] = $item ;
        }
        if($name == 'Love') {
            $order_json['Love'][] = $item ;
        }
        if($name == 'Passion') {
            $order_json['Passion'][] = $item ;
        }
    }

    echo '<pre>';
    print_r ($order_json);
    echo '</pre>';

    echo '<pre>';
    print_r ($cart);
    echo '</pre>';
}
add_action( 'woocommerce_before_cart_table', 'wm_get_poi_id' );

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


// For cart page: replacing proceed to checkout button
add_action('woocommerce_proceed_to_checkout', 'change_proceed_to_checkout', 1);
function change_proceed_to_checkout()
{
    remove_action('woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20);
    add_action('woocommerce_proceed_to_checkout', 'custom_button_proceed_to_custom_page', 20);
}
// Cart page: Displays the replacement custom button linked to your custom page
function custom_button_proceed_to_custom_page()
{
    $button_name = esc_html__('Proceed to checkout', 'woocommerce'); // <== button Name
    $button_link = get_permalink(4852); // <== Set here the page ID or use home_url() function
    ?>
    <a href="<?php echo $button_link; ?>" class="checkout-button button alt wc-forward">
        <?php echo $button_name; ?>
    </a>
<?php
}
