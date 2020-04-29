<?php
/**
 * Customer completed order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-completed-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
 ?>

<?php /* translators: %s: Customer first name */ 
$tree_types = montepisanotree_tree_modality_types($order);
$tree_quantity = montepisanotree_tree_quantity_inorder($order);
$renewal_type = montepisanotree_order_is_nenewal($order);
$single_tree_order = __( 'la targhetta è pronta per essere ritirata e collocata sul tuo albero. Puoi ritirarla', 'montepisanotree' );
$multi_tree_order = __( 'le targhette sono pronte per essere ritirate e collocate sui tuoi alberi. Puoi ritirarle', 'montepisanotree' );
if (count($tree_quantity) > 1 ){
	$targhet_number = $multi_tree_order;
} else {
	$targhet_number = $single_tree_order;
}
?>

<?php /* translators: %s: Site title */ ?>
<?php if (count($tree_types) == 1 && $tree_types[0] == "friendship" || $renewal_type[0] == 'renewal_paid_date') :
	do_action( 'woocommerce_email_header', "Certificato di adozione", $email );
?>
<p><?php printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $order->get_billing_first_name() ) ); ?></p>
<p><?php esc_html_e( 'Ti confermiamo che l’adozione è stata confermata. Qua sotto trovi un breve riepilogo. Grazie ancora per aver supportato il progetto MontepisanoTree.', 'montepisanotree' ); ?></p>
<?php else: 
	do_action( 'woocommerce_email_header', "Ritira la tua targhetta", $email );
?>
<p><?php printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $order->get_billing_first_name() ) ); ?></p>
<p><?php printf(esc_html__( 'Ti informiamo che %s tutti i giorni dal lunedì al venerdì dalle ore 9:00 alle 18:00 ed il sabato dalle 9 alle 13 presso il nostro Store in Largo P. B.Shelley, 20, 56017 San Giuliano Terme PI. Grazie ancora', 'montepisanotree' ),$targhet_number); ?></p>
<?php endif; ?>
<p><?php esc_html_e( 'Il TEAM Montepisanotree', 'montepisanotree' ); ?></p>
<?php

/*
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
