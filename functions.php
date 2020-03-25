<?php
/**
 * Werkstatt child theme functions and definitions
 */

/*-----------------------------------------------------------------------------------*/
/* Include the parent theme style.css
/*-----------------------------------------------------------------------------------*/

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );

}

/** action for title section of single product */
add_action('poi_single_product_title','my_function_poi_title',15);
function my_function_poi_title(){
    woocommerce_breadcrumb();  // gets the breadcrumb of the product
    echo '<div class="poi-name-tax small-12 large-7 columns">';
    
    $Poi_title =  woocommerce_template_single_title();   // gets the title of the product
    $taxonomies = get_post_taxonomies();  // gets the category of the product and the icon
    foreach ( $taxonomies as $taxonomy )
        {
            if ( $taxonomy != $main_tax )
            {
                $terms = get_the_terms( $post_id , $taxonomy );
                if ( $terms && is_array( $terms ) )
                    foreach ( $terms as $term ) :
                        $term_icon = get_field( 'wm_taxonomy_icon',$term );
                        $term_link = get_term_link( $term );
                        echo "<h1 class='webmapp-term-short webmapp-term-short-$term->slug'><i class='$term_icon'></i><span><a href='$term_link'>$term->name</a></span></h1>";
                        
                    endforeach;
            }
        }
    echo '</div>';

    mostraPulsanteAdotta();
    
    // shows the coordination of the tree latitude and longitude
    $coords = get_field ('n7webmap_coord');
    $lat = number_format($coords['lat'],7);
    $lng = number_format($coords['lng'],7);

    echo '<div class="tax-description small-12 large-9 columns" style="font-size: 15px;color: #676767;padding-bottom:10px;">Lat: '.$lat.' lng: '.$lng.' (WGS84)</div>';
    //print_r ($coords);
    
    
    $category_desc = category_description($term->term_id);
    echo '<br>';
    echo '<div class="tax-description small-12 large-9 columns">'. $category_desc .'</div>';

        
    
            
        
};

/** add a filter to modify and customize breadcrumb parameters */
add_filter( 'woocommerce_get_breadcrumb', 'custom_get_breadcrumb', 20, 2 );
function custom_get_breadcrumb( $crumbs, $breadcrumb ){

    // The Crump item to target
    $target = __( 'POI', 'woocommerce' );

    foreach($crumbs as $key => $crumb){
        if( $target === $crumb[0] ){
            // 1. Change name
            $crumbs[$key][0] = __( 'Mappa', 'woocommerce' );

            // 2. Change URL (you can also use get_permalink( $id ) with the post Id
            $crumbs[$key][1] = home_url( 'https://montepisanotree.org/mappa' );
        }
    }
    return $crumbs;
}
    
/** Image gallery section of single product - plugin Product Gallery Slider for WooCommerce  */
    remove_action('woocommerce_before_single_product_summary', 'wpgs_product_image', 20);
    remove_action('woocommerce_product_thumbnails', 'wpgs_product_thumbnails', 20);

    /** first part - product image */
    add_action('woocommerce_before_single_product_summary', 'poi_wpgs_product_image', 20);
    function poi_wpgs_product_image(){
        global $product;
        
        $post_thumbnail_id = $product->get_image_id();
        $image         = wp_get_attachment_image($post_thumbnail_id, 'shop_single', true,array( "class" => "attachment-shop_single size-shop_single wp-post-image" ));


        $wrapper_classes = apply_filters('woocommerce_single_product_image_gallery_classes', array(
            'wpgs',
            'wpgs--' . (has_post_thumbnail() ? 'with-images' : 'without-images'),
            'images',

        ));

        ?>

        <div class="<?php echo esc_attr(implode(' ', array_map('sanitize_html_class', $wrapper_classes))); ?>">

        <?php
            
        if (has_post_thumbnail()) {
        echo '<div class="wpgs-for">';
        $poi_gallery = get_field('n7webmap_media_gallery');
        $track_gallery = get_field('n7webmap_track_media_gallery');
        if ( !empty($poi_gallery) ){
            $myArray = $poi_gallery;
        } else {
            $myArray = $track_gallery;
        }
        // $myArray = get_field( "n7webmap_track_media_gallery" );
        foreach($myArray as $foto) {
            if (isset($foto['ID'])) {
                $fotoGalleryId[] = $foto['ID'];
            }
            // $fotoGalleryurl[] = $foto['url'];
        }
            $attachment_ids = $fotoGalleryId;//$product->get_gallery_image_ids();

            $lightbox_src;// = wc_get_product_attachment_props($post_thumbnail_id);
        
            if ($attachment_ids) {
                foreach ($attachment_ids as $attachment_id) {
                    $thumbnail_image     = wp_get_attachment_image($attachment_id, 'shop_single');
                    $lightbox_src = wc_get_product_attachment_props($attachment_id);
                    // fw_print($thumbnail_src);
                    echo '<a class="venobox" data-gall="wpgs-lightbox" title="'.$lightbox_src['title'].'" href="'.$lightbox_src['url'].'" >' . $thumbnail_image . '</a>';

                }
            }
            echo "</div>";
        } else {
            $html = '<div class="woocommerce-product-gallery__image--placeholder">';
            $html .= sprintf('<img src="%s" alt="%s" class="wp-post-image" />', esc_url(wc_placeholder_img_src()), esc_html__('Awaiting product image', 'woocommerce'));
            $html .= '</div>';
        }

        do_action( 'woocommerce_product_thumbnails' );
    echo "</div>";

    }

    /** second part - product thumbnails gallery */
    add_action('woocommerce_product_thumbnails', 'poi_wpgs_product_thumbnails', 20);
    function poi_wpgs_product_thumbnails(){
        global $product;
        $poi_gallery = get_field('n7webmap_media_gallery');
        $track_gallery = get_field('n7webmap_track_media_gallery');
        if ( !empty($poi_gallery) ){
            $myArray = $poi_gallery;
        } else {
            $myArray = $track_gallery;
        }
        // $myArray = get_field( "n7webmap_track_media_gallery" );
        foreach($myArray as $foto) {
            if (isset($foto['ID'])) {
                $fotoGalleryId[] = $foto['ID'];
            }
            // $fotoGalleryurl[] = $foto['url'];
        }
        
        // $post_thumbnail_id = $product->get_image_id();

        if( wpgs_woocommerce_version_check() ) {
            // Use new, updated functions
            // $attachment_ids = $product->get_gallery_image_ids() ;
            $attachment_ids = $fotoGalleryId;
        } else {
            // Use older, deprecated functions
            $attachment_ids = $fotoGalleryId;//$product->get_gallery_attachment_ids() ;
        }

        $gallery_thumbnail = wc_get_image_size('gallery_thumbnail');

        $thumbnail_size    = apply_filters('woocommerce_gallery_thumbnail_size', array($gallery_thumbnail['width'], $gallery_thumbnail['height']));

        if ( $attachment_ids ) {
            echo '<div class="wpgs-nav">';

            foreach ( $attachment_ids as $attachment_id ) {
                $thumbnail_image     = wp_get_attachment_image($attachment_id, $thumbnail_size);
                    
                    echo '<div>' . $thumbnail_image . '</div>';
            }
            echo "</div>";
        }
    }



/** action for map section of single product */
    add_action('poi_single_product_map','my_function_poi_map',15);
    function my_function_poi_map(){
        
        $id = get_the_ID();
        echo do_shortcode ("[webmapp_geojson_map geojson_url='https://a.webmapp.it/montepisanotree.org/geojson/$id.geojson' height='420']");
        
    }

/** action for features section of single product */
add_action('poi_single_product_features','my_function_poi_features',10);
function my_function_poi_features(){
    echo '<h2>Caratteristiche Albero</h2>';

    /** First Feature Condizioni vegetative */
    $value = get_field( "condizione_vegetativa_pianta_e_principali_caratteristiche" );
    if( $value ) {
        echo '<p class="features-title">Condizione vegetative:</p>';
        echo $value;
    } 

    /** Second Feature Spalcatura */
    $value = get_field( "spalcatura" );
    if( $value ) {
        $value1 = get_field( "descrizione_spalcatura" );
        echo '<p class="features-title">Spalcatura:</p>';
        echo $value1;
    }

    /** Third Feature Diradamenti */
    $value = get_field( "diradamenti" );
    if( $value ) {
        $value1 = get_field( "descrizione_diradamenti" );
        echo '<p class="features-title">Diradamenti:</p>';
        echo $value1;
    }

    /** Forth Feature Altri interventi */
    $value = get_field( "altri_interventi" );
    if( $value ) {
        $value1 = get_field( "descrizione_altri_interventi" );
        echo '<p class="features-title">Altri interventi:</p>';
        echo $value1;
    }
}


/** action for price and add to card section for single product */
function wc_remove_all_quantity_fields( $return, $product ) {
    return true;
}
add_filter( 'woocommerce_is_sold_individually', 'wc_remove_all_quantity_fields', 10, 2 );

add_action('poi_single_product_summary','custom_woocommerce_single_product_summary',15);
function custom_woocommerce_single_product_summary(){
    $arg = array(
        'limit' => 1000,
        'status' => array('completed','processing'),
    );
    $orders = wc_get_orders($arg);
        foreach ($orders as $order ){
         foreach( $order->get_items() as $item_id => $item ){
           $product_name_variation = $item->get_name();
           $product_name = preg_replace('/[^0-9]/', '', $product_name_variation);//substr($product_name_variation,0,3);
           global $product;
           $current_product_name = $product->name;
           if ($product_name == $current_product_name ) {
            $counter = true;
            $current_order_id = $item['order_id'];
            $order_meta = get_post_meta($current_order_id);
            $current_paid_date = $order_meta['_paid_date'][0];
            $next_availible_date = date("Y-m-d", strtotime("+1 years +1 days", strtotime($order_meta['_paid_date'][0])));
            $current_date = date ("Y-m-d");
           } 
          }
        }
        if ( $counter == true &&  $current_date<$next_availible_date){
            mostraProdottoComprato($current_order_id);
        }else{
            mostraPrezzo();
            
        }
}
function mostraPulsanteAdotta(){
    $arg = array(
        'limit' => 1000,
        'status' => array('completed','processing'),
    );
    $orders = wc_get_orders($arg);
        foreach ($orders as $order ){
         foreach( $order->get_items() as $item_id => $item ){
           $product_name_variation = $item->get_name();
           $product_name = preg_replace('/[^0-9]/', '', $product_name_variation);//substr($product_name_variation,0,3);
           global $product;
           $current_product_name = $product->name;
           if ($product_name == $current_product_name ) {
            $counter = true;
            $current_order_id = $item['order_id'];
            $order_meta = get_post_meta($current_order_id);
            $current_paid_date = $order_meta['_paid_date'][0];
            $next_availible_date = date("Y-m-d", strtotime("+1 years +1 days", strtotime($order_meta['_paid_date'][0])));
            $current_date = date ("Y-m-d");
           } 
          }
        }
        if ( $counter == true &&  $current_date<$next_availible_date){
            
        }else{
            echo do_shortcode( '<div class="button-adottaora large-3 columns">[thb_button link="url:%23adottaora|title:Adotta%20ora!||"]</div>');
        }
}
function mostraProdottoComprato($current_order_id){
    $order_meta = get_post_meta($current_order_id);
    
    $mesi = array(1=>'gennaio', 'febbraio', 'marzo', 'aprile',
                    'maggio', 'giugno', 'luglio', 'agosto',
                    'settembre', 'ottobre', 'novembre','dicembre');
    
    list($giorno,$mese,$anno) = explode('-',date("d-n-Y", strtotime("+1 years +1 days", strtotime($order_meta['_paid_date'][0]))));
    
    //' .$order_meta[_billing_first_name][0].' '. $order_meta[_billing_last_name][0].' nome e cognome del acquirente
    echo '<h2 class="gia-adottato">L\'albero è stato già adottato fino al ' . $giorno,' ',$mesi[$mese],' ',$anno . '!</h2>';
    

}
function mostraPrezzo(){
    echo '<h2 id="adottaora">Adotta per un anno!</h2>';
    //woocommerce_template_single_price();
    echo '<p>Scegli fra le nostre tre opzioni di adozione, a partire da €9 all\'anno. Questo versamento permetterà di fornire le cure necessarie alla pianta e di mantenere in vita il progetto.</p>';
    woocommerce_template_single_add_to_cart();
    echo wc_attribute_label( $attribute_name );
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
function add_variations_in_cart($name, $cart_item, $item_key){
    $product_variation = '';
    if(!empty($cart_item['variation_id']) && $cart_item['variation_id'] != 0 ){
       if(is_array($cart_item['variation']) && !empty($cart_item['variation'])){
          foreach ($cart_item['variation'] as $key => $value) {
             $product_variation .= '<span class="product-attribute-carrello"> - '.ucfirst($value).'</span>';
        }
    }
}

echo $name.$product_variation; }



add_filter( 'gettext', 'bbloomer_translate_woocommerce_strings', 999, 3 );
 
function bbloomer_translate_woocommerce_strings( $translated, $text, $domain ) {
 
// STRING 1
$translated = str_ireplace( 'Your personal data will be used to process your order, support your experience throughout this website, and for other purposes described in our', 'I tuoi dati personali saranno utilizzati per elaborare il tuo ordine, supportare la tua esperienza su questo sito web e per altri scopi descritti nella nostra', $translated );
 
// ETC.
 
return $translated;
}

add_action('wc_get_privacy_policy_text','custom_get_privacy_policy_text',20,2);
function custom_get_privacy_policy_text( $type = '' ) {
	$text = '';

	switch ( $type ) {
		case 'checkout':
			/* translators: %s privacy policy page name and link */
			$text = get_option( 'woocommerce_checkout_privacy_policy_text', sprintf( __( 'I tuoi dati personali saranno utilizzati per elaborare il tuo ordine, supportare la tua esperienza su questo sito web e per altri scopi descritti nella nostra %s.', 'woocommerce' ), '[privacy_policy]' ) );
			break;
		case 'registration':
			/* translators: %s privacy policy page name and link */
			$text = get_option( 'woocommerce_registration_privacy_policy_text', sprintf( __( 'Your personal data will be used to support your experience throughout this website, to manage access to your account, and for other purposes described in our %s.', 'woocommerce' ), '[privacy_policy]' ) );
			break;
	}

	return trim( apply_filters( 'woocommerce_get_privacy_policy_text', $text, $type ) );
}


apply_filters( 'woocommerce_get_privacy_policy_text', $text, $type );
$text = get_option( 'woocommerce_checkout_privacy_policy_text', sprintf( __( 'I tuoi dati personali saranno utilizzati per elaborare il tuo ordine, supportare la tua esperienza su questo sito web e per altri scopi descritti nella nostra %s.', 'woocommerce' ), '[privacy_policy]' ) );


add_action( 'woocommerce_review_order_before_submit', 'add_privacy_checkbox', 9 );
function add_privacy_checkbox() {
woocommerce_form_field( 'privacy_policy', array(
'type' => 'checkbox',
'class' => array('form-row privacy'),
'label_class' => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'),
'input_class' => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'),
'required' => true,
'label' => 'Dichiaro di aver letto e accettato la <a href="https://montepisanotree.org/privacy-policy">Privacy Policy</a>',
));
}
add_action( 'woocommerce_checkout_process', 'privacy_checkbox_error_message' );
function privacy_checkbox_error_message() {
if ( ! (int) isset( $_POST['privacy_policy'] ) ) {
wc_add_notice( __( 'Devi accettare la nostra politica sulla privacy per procedere' ), 'error' );
}
}


// torna al negozio link
function wc_empty_cart_redirect_url() {
    return 'https://montepisanotree.org/mappa/';
}
add_filter( 'woocommerce_return_to_shop_redirect', 'wc_empty_cart_redirect_url' );


// For cart page: replacing proceed to checkout button
add_action( 'woocommerce_proceed_to_checkout', 'change_proceed_to_checkout', 1 );
function change_proceed_to_checkout() {
    remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );
    add_action( 'woocommerce_proceed_to_checkout', 'custom_button_proceed_to_custom_page', 20 );
}
// Cart page: Displays the replacement custom button linked to your custom page
function custom_button_proceed_to_custom_page() {
    $button_name = esc_html__( 'Proceed to checkout', 'woocommerce' ); // <== button Name
    $button_link = get_permalink( 4852 ); // <== Set here the page ID or use home_url() function
    ?>
    <a href="<?php echo $button_link;?>" class="checkout-button button alt wc-forward">
        <?php echo $button_name; ?>
    </a>
    <?php
}