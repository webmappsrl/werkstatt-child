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
function mostraPrezzo($post_id){
    echo '<h2 id="adottaora">Adotta per un anno!</h2>';
    //woocommerce_template_single_price();
    echo '<p>Scegli fra le nostre tre opzioni di adozione, a partire da €9 all\'anno. Questo versamento permetterà di fornire le cure necessarie alla pianta e di mantenere in vita il progetto.</p>';
    // woocommerce_template_single_add_to_cart();
    // echo do_shortcode('[products id="6889"]');
    // echo do_shortcode('[add_to_cart id="6896"]');
    // echo wc_attribute_label( $attribute_name );
    $product = wc_get_product( '6888');
    $attributes_main = $product->get_attributes();
    $available_variations = $product->get_available_variations();
    echo '<br>';
    // $attributes = array();
    // print_r($available_variations);
    foreach ( $attributes_main as $attribute ):
        $attributes['Modalità'] = $attribute->get_options();
        // testing output
        // print_r($attributes);
    endforeach;
    $attribute_keys  = array_keys( $attributes );
    $variations_json = wp_json_encode( $available_variations );
    $variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );
    ?>
        <form class="variations_form cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo $variations_attr; // WPCS: XSS ok. ?>">
        <?php do_action( 'woocommerce_before_variations_form' ); ?>

        <?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
            <p class="stock out-of-stock"><?php echo esc_html( apply_filters( 'woocommerce_out_of_stock_message', __( 'This product is currently out of stock and unavailable.', 'woocommerce' ) ) ); ?></p>
        <?php else : ?>
            <table class="variations" cellspacing="0">
                <tbody>
                    <?php foreach ( $attributes as $attribute_name => $options ) : ?>
                        <tr>
                            <td class="label"><label for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>"><?php echo wc_attribute_label( $attribute_name ); // WPCS: XSS ok. ?></label></td>
                            <td class="value">
                                <?php
                                    wc_dropdown_variation_attribute_options( array(
                                        'options'   => $options,
                                        'attribute' => $attribute_name,
                                        'product'   => $product,
                                    ) );
                                    echo end( $attribute_keys ) === $attribute_name ? wp_kses_post( apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . esc_html__( 'Clear', 'woocommerce' ) . '</a>' ) ) : '';
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="single_variation_wrap">
                <div class="woocommerce-variation single_variation">
                    <script type="text/template" id="tmpl-variation-template">
                    <div class="woocommerce-variation-description">{{{ data.variation.variation_description }}}</div>
                    <div class="woocommerce-variation-price">{{{ data.variation.price_html }}}</div>
                    <div class="woocommerce-variation-availability">{{{ data.variation.availability_html }}}</div>
                    </script>
                    <script type="text/template" id="tmpl-unavailable-variation-template">
                    <p><?php esc_html_e( 'Sorry, this product is unavailable. Please choose a different combination.', 'woocommerce' ); ?></p>
                    </script>
                </div>
                <div class="woocommerce-variation-add-to-cart variations_button">
                    <?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

                    <?php
                    do_action( 'woocommerce_before_add_to_cart_quantity' );

                    woocommerce_quantity_input( array(
                        'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
                        'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
                        'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
                    ) );

                    do_action( 'woocommerce_after_add_to_cart_quantity' );
                    ?>

                    <button type="submit" data-poi="<?= $post_id?>" class="single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>

                    <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

                    <input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>" />
                    <input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>" />
                    <input type="hidden" name="variation_id" class="variation_id" value="0" />
                </div>
            </div>
        <?php endif; ?>

        <?php do_action( 'woocommerce_after_variations_form' ); ?>
    </form>
    <?php
    echo '<div class="back-to-map"><a href="https://montepisanotree.org/mappa">o torna alla mappa</a></div>';
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


apply_filters('woocommerce_get_privacy_policy_text', $text, $type);
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
