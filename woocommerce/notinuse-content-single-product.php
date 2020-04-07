<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked wc_print_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
?>
<div id="product-<?php the_ID(); ?>" class="product-detail row align-center max-width"<?php wc_product_class(); ?>>
	<div class="small-12 large-10 xlarge-8 columns">
		<div class="row">
			<div class="small-12 large-12 columns">
				<?php
				/**
				 * custom function for breadcrumb title and category
				 * function.php
				 */
				do_action( 'poi_single_product_title' );
				?>
			</div>
			<div class="poi-cell poi-visual poi-gallery small-12 large-6 columns">
				<?php
					/**
					 * Hook: woocommerce_before_single_product_summary.
					 *
					 * @hooked woocommerce_show_product_sale_flash - 10
					 * @hooked woocommerce_show_product_images - 20
					 */

					do_action( 'woocommerce_before_single_product_summary' );
				?>
			</div>
			<div class="poi-cell poi-visual poi-map small-12 large-6 columns">
				<?php
					/**
					 * custom function to show the single product map
					 *
					 */
					
					do_action( 'poi_single_product_map' );
				?>
			</div>
			<div class="poi-cell poi-info summary entry-summary small-12 large-6 columns">
				<?php
					/**
					 * custom function to get single product features / caratteritiche albero
					 */
					do_action( 'poi_single_product_features' );
				?>
			</div>

			<div class="poi-cell poi-info summary entry-summary small-12 large-6 columns adotta-ora-cell-main">
				<div class="adotta-ora-cell">

				<?php
					/**
					 * Hook: woocommerce_single_product_summary.
					 *
					 * @hooked woocommerce_template_single_title - 5
					 * @hooked woocommerce_template_single_rating - 10
					 * @hooked woocommerce_template_single_price - 10
					 * @hooked woocommerce_template_single_excerpt - 20
					 * @hooked woocommerce_template_single_add_to_cart - 30
					 * @hooked woocommerce_template_single_meta - 40
					 * @hooked woocommerce_template_single_sharing - 50
					 * @hooked WC_Structured_Data::generate_product_data() - 60
					 */
					do_action( 'poi_single_product_summary' );
				?>
				</div>
			</div>

			<?php
				/** 
				 * not using this action
				 * Hook: woocommerce_after_single_product_summary.
				 *
				 * @hooked woocommerce_output_product_data_tabs - 10
				 * @hooked woocommerce_upsell_display - 15
				 * @hooked woocommerce_output_related_products - 20
				 */
				//do_action( 'woocommerce_after_single_product_summary' );


			?>	
		</div>
	</div>
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>



