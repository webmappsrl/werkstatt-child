<?php get_header(); 

$post_id = get_the_ID();
$home_url = home_url();
$home_url = preg_replace('#^https?://#', '', $home_url);

?>
<div class="page-padding extra">

<?php if (have_posts()) :  while (have_posts()) : the_post(); 

do_action( 'woocommerce_before_single_product' );

?>
<div id="product-<?php the_ID(); ?>" class="product-detail row align-center max-width">
	<div class="small-12 large-10 xlarge-8 columns">
		<div class="row">
			<div class="small-12 large-12 columns">
				<?php
				woocommerce_breadcrumb();  // gets the breadcrumb of the product
                echo '<div class="poi-name-tax small-12 large-7 columns">';
                
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
                
                $category_desc = category_description($term->term_id);
                echo '<br>';
                echo '<div class="tax-description small-12 large-9 columns">'. $category_desc .'</div>';
            
				?>
			</div>
			<div class="poi-cell poi-visual poi-gallery small-12 large-6 columns">
				<?php
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
                        if (isset($foto['id'])) {
                            $fotoGalleryId[] = $foto['id'];
                        } else {
                            $fotoGalleryId[] = $foto;
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
                        
				?>
			</div>
			<div class="poi-cell poi-visual poi-map small-12 large-6 columns">
				<?php
					echo do_shortcode ("[webmapp_geojson_map geojson_url='https://a.webmapp.it/$home_url/geojson/$post_id.geojson' height='420']");
				?>
			</div>
			<div class="poi-cell poi-info summary entry-summary small-12 large-6 columns">
				<?php
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
				?>
			</div>

			<div class="poi-cell poi-info summary entry-summary small-12 large-6 columns adotta-ora-cell-main">
				<div class="adotta-ora-cell">

                <?php
                    // do_action( 'poi_single_product_summary' );
                    $paid_date = get_field('paid_date',$post_id);
                    if ( isset($paid_date) &&  $paid_date) {
                        mostraProdottoComprato($paid_date);
                    } else {
                        mostraPrezzo($post_id);
                    }
				?>
				</div>
            </div>
            
		</div>
	</div>
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>




<?php endwhile; else : endif; ?>
</div>
<?php get_footer(); ?>