<?php
/**
 * tscoin functions and definitions
 *
 * @package tscoin
 */

function tscoin_scripts() {
	wp_enqueue_style( 'tscoin-style', get_stylesheet_uri() );

	wp_enqueue_style( 'tscoin-viewer', get_template_directory_uri() . '/assets/css/viewer.min.css', array(), '1.0' );

	wp_enqueue_style( 'tscoin-main', get_template_directory_uri() . '/assets/css/main.css', array(), '1.0' );

	wp_enqueue_style( 'tscoin-fix', get_template_directory_uri() . '/assets/css/fix.css', array(), '1.0' );

	wp_enqueue_style( 'tscoin-styles', get_template_directory_uri() . '/assets/css/style.css', array(), '1.0' );

	wp_enqueue_script( 'tscoin-jq', get_template_directory_uri() . '/assets/js/jquery.js', array(), '1.0', true );

	wp_enqueue_script( 'tscoin-libs', get_template_directory_uri() . '/assets/js/libs.min.js', array(), '1.0', true );

	wp_enqueue_script( 'tscoin-zoom', get_template_directory_uri() . '/assets/js/jquery.zoom.min.js', array(), '1.0', true );

	wp_enqueue_script( 'tscoin-viewer', get_template_directory_uri() . '/assets/js/viewer.min.js', array(), '1.0', true );

	wp_enqueue_script( 'tscoin-scripts', get_template_directory_uri() . '/assets/js/main.js', array(), '1.0', true );

	wp_enqueue_script( 'tscoin-fix-scripts', get_template_directory_uri() . '/assets/js/fix.js', array(), '1.0', true );

	wp_enqueue_script( 'tscoin-js-scripts', get_template_directory_uri() . '/assets/js/js.js', array(), '1.0', true );

	wp_localize_script( 'ajax-script', 'AJAX', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}

add_action( 'wp_enqueue_scripts', 'tscoin_scripts' );

get_template_part( 'functions/ajax-functions' );
get_template_part( 'functions/helpers' );
get_template_part( 'functions/settings' );
get_template_part( 'functions/carbon-settings' );
get_template_part( 'functions/components' );
get_template_part( 'functions/woo' );
if ( function_exists( 'pll_register_string' ) ) {
	get_template_part( 'functions/translations' );
}
function add_additional_class_on_a( $classes, $item, $args ) {
	if ( isset( $args->add_a_class ) ) {
		$classes['class'] = $args->add_a_class;
	}

	return $classes;
}

add_filter( 'nav_menu_link_attributes', 'add_additional_class_on_a', 1, 3 );

function theme_setup() {
	add_theme_support( 'woocommerce' );
}

add_action( 'after_setup_theme', 'theme_setup' );

function parse_products() {
	$langs        = pll_languages_list();
	$default_lang = pll_default_language();
	$translations = array();
	echo '<pre>';
	if ( $langs ) {
		foreach ( $langs as $__lang ) {
			$args  = array(
				'post_type'      => 'products',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'lang'           => $__lang
			);
			$query = new WP_Query( $args );
			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					$id           = get_the_ID();
					$lang         = function_exists( 'pll_get_post_language' ) ? pll_get_post_language( $id ) : '';
					$title         = get_the_title();
					$content       = get_content_by_id( $id );
					$post          = get_post( $id );
					$slug          = $post->post_name;
					$wc_product_ID = get_wc_product_by_slug( $slug );
					if ( ! $wc_product_ID ) {
						$price                     = carbon_get_post_meta( $id, 'product_price_eur' );
						$product_banner            = carbon_get_post_meta( $id, 'product_banner' );
						$product_image             = carbon_get_post_meta( $id, 'product_image' );
						$product_pictures_banner   = carbon_get_post_meta( $id, 'product_pictures_banner' );
						$product_qnt               = carbon_get_post_meta( $id, 'product_qnt' );
						$product_code              = carbon_get_post_meta( $id, 'product_code' );
						$product_availability      = carbon_get_post_meta( $id, 'product_availability' );
						$product_availability_text = carbon_get_post_meta( $id, 'product_availability_text' );
						$product_description       = carbon_get_post_meta( $id, 'product_description' );
						$product_short_description = carbon_get_post_meta( $id, 'product_short_description' );
						$product_gallery           = carbon_get_post_meta( $id, 'product_gallery' );
						$product_list              = carbon_get_post_meta( $id, 'product_list' );
						$product_tag_image         = carbon_get_post_meta( $id, 'product_tag_image' );
						$img                       = get_the_post_thumbnail_url( $id );
						$coin_theme                = get_the_terms( $id, 'coin_theme' );
						$country                   = get_the_terms( $id, 'country' );
						$years                     = get_the_terms( $id, 'years' );
						$features                  = get_the_terms( $id, 'features' );
						if ( ! $wc_product_ID ) {
							$wc_post_data  = array(
								'post_type'   => 'product',
								'post_title'  => $title,
								'post_status' => 'publish',
								'name'        => $slug,
							);
							$wc_product_ID = wp_insert_post( $wc_post_data );
							$wc_product    = get_post( $wc_product_ID );
							echo "Створено $title [$wc_product_ID - $id] [$slug] '$__lang' <hr>";
							if ( $wc_product ) {
								if ( $lang ) {
									pll_set_post_language( $wc_product_ID, $lang );
								}
								carbon_set_post_meta( $wc_product_ID, 'product_item_banner', $product_banner );
								carbon_set_post_meta( $wc_product_ID, 'product_item_image', $product_image );
								carbon_set_post_meta( $wc_product_ID, 'product_item_pictures', $product_pictures_banner );
								carbon_set_post_meta( $wc_product_ID, 'product_item_description', $product_description );
								carbon_set_post_meta( $wc_product_ID, 'product_item_availability_text', $product_availability_text );
								carbon_set_post_meta( $wc_product_ID, 'product_item_tag_image', $product_tag_image );
								$product = wc_get_product( $wc_product_ID );
								$product->set_name( $title );
								$product->set_description( $content );
								$product->set_short_description( $product_short_description );
								$product->set_sku( $product_code . '_' . $wc_product_ID );
								$main_image_id = attachment_url_to_postid( $img );
								$product->set_image_id( $main_image_id );
								$gallery_image_ids = [];
								if ( $product_gallery ) {
									foreach ( $product_gallery as $image ) {
										$gallery_image_ids[] = $image;
									}
								}
								$product->set_gallery_image_ids( $gallery_image_ids );
								if ( $product_qnt ) {
									$product->set_manage_stock( true );
									$product->set_stock_quantity( (int) $product_qnt );
								}
								if ( $product_availability == 'on_stock' ) {
									$product->set_stock_status( 'instock' );
								} elseif ( $product_availability == 'pre_order' ) {
									$product->set_stock_status( 'onbackorder' );
								} else {
									$product->set_stock_status( 'outofstock' );
								}
								$product->set_regular_price( $price );
								$pa_themes          = array();
								$pa_countries       = array();
								$pa_features        = array();
								$pa_years           = array();
								$product_attributes = array();
								$attributes         = array();
								if ( $product_list ) {
									foreach ( $product_list as $item ) {
										$attributes[ $item['characteristic'] ] = $item['val'];
									}
								}
								foreach ( $attributes as $taxonomy => $value ) {
									$attribute = new WC_Product_Attribute();
									$attribute->set_name( $taxonomy );
									$attribute->set_options( explode( ', ', $value ) );
									$attribute->set_visible( true );
									$attribute->set_variation( false );
									$product_attributes[] = $attribute;
								}
								$product->set_attributes( $product_attributes );
								$product->save();
							}
						}
					}
				}
			}
			echo '<hr>';
			echo '<hr>';
			echo $query->found_posts;
			echo '<hr>';
			echo '<hr>';
			wp_reset_query();
			wp_reset_postdata();
		}
	}
}


function setWCProductAttrs( $product_attributes, $taxonomy, $value ) {
	$value     = ! is_array( $value ) ? explode( ', ', $value ) : $value;
	$attribute = new WC_Product_Attribute();
	$attribute->set_id( wc_attribute_taxonomy_id_by_name( $taxonomy ) );
	$attribute->set_name( $taxonomy );
	$attribute->set_options( $value );
	$attribute->set_visible( true );
	$attribute->set_variation( false );
	$product_attributes[] = $attribute;

	return $product_attributes;
}


function get_wc_product_by_slug( $slug ) {
	$args     = array(
		'name'           => $slug,
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => 1
	);
	$my_posts = get_posts( $args );
	if ( $my_posts ) :
		return $my_posts[0]->ID;
	endif;

	return false;
}