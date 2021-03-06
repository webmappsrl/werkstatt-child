<?php
/**
 * Customer processing order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-processing-order.php.
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
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>
<?php /* translators: %s: Customer first name */ 
$tree_types = montepisanotree_tree_modality_types($order);
$renewal_type = montepisanotree_order_is_renewal($order);
?>
<p><?php printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $order->get_billing_first_name() ) ); ?></p>
<?php /* translators: %s: Order number */ ?>
<?php if (in_array("love", $tree_types) == false && in_array("passion", $tree_types) == false || $renewal_type[0] == 'renewal_paid_date') :?>
<p><?php esc_html_e( 'La tua adozione è andata a buon fine! Ti ringraziamo per aver partecipato al progetto Montepisanotree. A breve riceverai una mail che conferma l\'adozione del tuo albero. Non esitare a contattarci, siamo a disposizione per chiarimenti e approfondimenti.', 'montepisanotree' ); ?></p>
<?php elseif (in_array("love", $tree_types) == false && in_array("friendship", $tree_types) == false): ?>
<p><?php echo __( 'La tua adozione è andata a buon fine! Ti ringraziamo per aver partecipato al progetto Montepisanotree. A breve il tuo nome comparirà nella <a href="https://montepisanotree.org/grazie/">pagina dei ringraziamenti</a> sul sito www.montepisanotree.org; riceverai una mail che conferma l\'adozione del tuo albero. Ti ricordiamo che installazione della targhetta e la gestione dell\'albero è a cura del progetto. Riceverai una mail non appena avremo messo la targhetta al tuo albero. Non esitare a contattarci, siamo a disposizione per chiarimenti e approfondimenti.', 'montepisanotree' ); ?></p>
<?php else: ?>
<p></p>
<p><?php echo __( 'La tua adozione è andata a buon fine! Ti ringraziamo per aver partecipato al progetto Montepisanotree. A breve il tuo nome comparirà nella <a href="https://montepisanotree.org/grazie/">pagina dei ringraziamenti</a> sul sito www.montepisanotree.org; riceverai una mail che conferma l\'adozione del tuo albero con le indicazioni per ritirare la targhetta. Non esitare a contattarci, siamo a disposizione per chiarimenti e approfondimenti.', 'montepisanotree' ); ?></p>
<?php endif; ?>
<p><?php esc_html_e( 'Il TEAM Montepisanotree', 'montepisanotree' );?></p>
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
