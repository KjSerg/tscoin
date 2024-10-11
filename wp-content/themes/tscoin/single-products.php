<?php
get_header();
$var               = variables();
$set               = $var['setting_home'];
$assets            = $var['assets'];
$url               = $var['url'];
$url_home          = $var['url_home'];
$id                = get_the_ID();
$image             = carbon_get_post_meta( $id, 'product_image' );
$description       = carbon_get_post_meta( $id, 'product_description' );
$gallery           = carbon_get_post_meta( $id, 'product_gallery' );
$tag_image         = carbon_get_post_meta( $id, 'product_tag_image' );
$code              = carbon_get_post_meta( $id, 'product_code' );
$availability      = carbon_get_post_meta( $id, 'product_availability' );
$availability_text = carbon_get_post_meta( $id, 'product_availability_text' );
$list              = carbon_get_post_meta( $id, 'product_list' );
$products          = carbon_get_post_meta( $id, 'similar_products' );
$similar_title     = carbon_get_post_meta( $id, 'product_similar_title' );
$banner            = carbon_get_post_meta( $id, 'product_banner' );
$pictures_banner   = carbon_get_post_meta( $id, 'product_pictures_banner' );
$qnt               = carbon_get_post_meta( $id, 'product_qnt' ) ?: 1;
$reserved_qnt      = carbon_get_post_meta( $id, 'product_reserved_qnt' ) ?: 0;
$_available        = $qnt - $reserved_qnt;
$title             = get_the_title();
$wish_list         = $_COOKIE['wish_list'] ?? '';
if ( $wish_list ) {
	$wish_list = explode( ",", $wish_list );
}
?>

<?php if ( $pictures_banner ): ?>
    <div class="bg_frame_slider with_autoplay_trigger">
		<?php foreach ( $pictures_banner as $key => $banner ): ?>
            <div>
                <div class="bg_frame <?php echo $key == 0 ? 'first_animated_slide' : ''; ?> ">
					<?php if ( $picture = $banner['picture'] ): ?>
                        <div class="bg_frame_in main-picture-wrapper">
                            <picture class="">
								<?php foreach ( $picture as $item ): ?>
                                    <source srcset="<?php _u( $item['image'] ) ?>"
                                            media="(min-width: <?php echo $item['min_width']; ?>px)"/>
								<?php endforeach; ?>
                                <img src="<?php _u( $picture[0]['image'] ) ?>" alt=""/>
                            </picture>
                        </div>
					<?php endif; ?>
                    <div class="bg_frame_bottom"></div>
                </div>
            </div>
		<?php endforeach; ?>
    </div>
<?php else: ?>
	<?php if ( $banner ): ?>
        <div class="bg_frame_slider with_autoplay">
			<?php foreach ( $banner as $item ): ?>
                <div>
                    <div class="bg_frame first_animated_slide">
                        <div class="bg_frame_in lozad" data-background-image="<?php _u( $item ); ?>"></div>
                        <div class="bg_frame_bottom"></div>
                    </div>
                </div>
			<?php endforeach; ?>
        </div>
	<?php else: ?>
        <div class="bg_frame lozad" data-background-image="<?php _u( $image ); ?>">
            <div class="bg_frame_bottom"></div>
        </div>
	<?php endif; ?>
<?php endif; ?>

<section class="section-head first_screen dark_bg animated_block">
    <div class="section_bg">
        <div class="screen_content">
            <div class="head_title"><?php _l( 'attention to details' ); ?></div>
        </div>
    </div>
    <div class="screen_content">
        <div class="coin_sides">
            <div class="coin_side content_side">
                <div class="head_coins_slide" data-name="<?php echo $title; ?>">
                    <div class="main_title large ttu animation_up delay1">
						<?php echo $title; ?>
                    </div>
                    <div class="coin_description simple_text animation_up delay2">
						<?php _l( $description ); ?>
                    </div>
                </div>
            </div>
            <div class="coin_side img_placeholder"></div>
        </div>
    </div>
</section>
<section class="section-product-items remove_pt">
    <div class="screen_content">
        <div class="light_frame">
            <div class="product_description_sides half_sides bottom_bordered dashed">
				<?php if ( $gallery ): ?>
                    <div class="product_description_side sliders_side">
                        <div class="product_description_slider">
							<?php foreach ( $gallery as $item ): ?>

                                <div>
                                    <div class="product_description_slide">
                                        <div class="zoom_img">
                                            <img class="prod_img" src="<?php _u( $item ); ?>" alt="img"/>
                                            <a class="zoom_image_open fancybox_gal" href="<?php _u( $item ); ?>"></a>
                                        </div>
										<?php if ( $tag_image ): ?>
                                            <img class="product_lbl"
                                                 src="<?php _u( $tag_image ); ?>"
                                                 alt="img"/>
										<?php endif; ?>
                                        <div class="zoom_btn">
                                            <img src="<?php echo $assets; ?>img/zoom.svg" alt="ico"/>
                                        </div>
                                    </div>
                                </div>

							<?php endforeach; ?>
                        </div>
                        <div class="product_thumb_slider">
							<?php foreach ( $gallery as $item ): ?>
                                <div>
                                    <div class="product_thumb_slide">
                                        <img src="<?php echo wp_get_attachment_image_src( $item, 'medium' )[0]; ?>"
                                             alt=""/>
                                    </div>
                                </div>
							<?php endforeach; ?>
                        </div>
                    </div>
				<?php endif; ?>
                <div class="product_description_side content_side">
                    <div class="main_title ttu wow fadeInUp" data-wow-duration="2s"><?php echo $title; ?></div>
                    <div class="simple_text product_description_text wow fadeInUp" data-wow-duration="2s">
						<?php the_post();
						the_content(); ?>
                    </div>
                    <div class="product__controls wow fadeInUp" data-wow-duration="2s">
                        <div class="product__price_top">
                            <div class="product__price">
                                <strong><?php the_product_price( $id ); ?></strong>
								<?php if ( $code ): ?>
                                    <span><?php _l( 'code' ); ?>: <?php echo $code; ?></span>
								<?php endif; ?>
                            </div>
							<?php the_package_price( $id ); ?>
                        </div>
                        <div class="product__controls_sides">
                            <div class="product__controls_side product_state">
								<?php if ( $availability == 'on_stock' ): ?>
                                    <div class="product_state_title green">
										<?php _l( 'Available on stock' ); ?>
                                    </div>
								<?php elseif ( $availability == 'sold_out' ): ?>
                                    <div class="product_state_title " style="color: #E32D2B;">
										<?php _l( 'Sold Out' ); ?>
                                    </div>
								<?php elseif ( $availability == 'pre_order' ): ?>
                                    <div class="product_state_title " style="color: #1A73D7;">
										<?php _l( 'In Pre-Order' ); ?>
                                    </div>
								<?php endif; ?>
								<?php if ( $availability_text ): ?>
                                    <p><?php echo $availability_text; ?></p>
								<?php endif; ?>
                            </div>
                            <div class="product__controls_side product_order_controls">
								<?php if ( ( $availability == 'on_stock' || $availability == 'pre_order' ) && $_available > 0 ): ?>
                                    <div class="quantity_wrapper">
                                        <input class="quantity_input" type="text"
                                               name="quantity"
                                               value="1"
                                               data-max="<?php echo $qnt; ?>"
                                               data-reserved="<?php echo $reserved_qnt; ?>"
                                               data-available="<?php echo $_available; ?>"
                                               readonly=""/>
                                        <a class="quant_btn plus_btn transition" href="javascript:void(0)"></a>
                                        <a class="quant_btn minus_btn transition" href="javascript:void(0)"></a>
                                    </div>
                                    <a class="product_btn add-to-cart"
                                       data-id="<?php echo $id; ?>"
                                       href="#">
										<?php _s( _i( 'cart_ico' ) ); ?>
                                    </a>
								<?php endif; ?>

                                <a class="product_btn add-to-wish-list <?php echo $wish_list && in_array( $id, $wish_list ) ? 'active' : ''; ?>"
                                   data-id="<?php echo $id; ?>" href="#">
									<?php _s( _i( 'favorite' ) ); ?>
                                </a>
                            </div>
                        </div>
                    </div>
					<?php if ( $list ): ?>
                        <div class="product_features_block wow fadeInUp" data-wow-duration="2s">
                            <div class="main_title"><?php _l( 'Product information' ); ?></div>
                            <ul class="product_features">
								<?php foreach ( $list as $item ): ?>
                                    <li class="product_feature">
                                        <p><?php echo $item['characteristic']; ?></p>
                                        <p><?php echo $item['val']; ?></p>
                                    </li>
								<?php endforeach; ?>
                            </ul>
                        </div>
					<?php endif; ?>
                </div>
            </div>
			<?php if ( $products ): ?>
                <div class="main_title centered ttu"><?php echo $similar_title; ?></div>
                <div class="product_items">
					<?php foreach ( $products as $product ) {
						$_id = $product['id'];
						if ( get_post( $_id ) ) {
							the_product( $_id );
						}
					} ?>
                </div>
			<?php endif; ?>
        </div>
    </div>
</section>

<?php if ( $f = carbon_get_post_meta( $set, 'short_code_order_form' ) ): ?>
    <div class="is_hidden thanks_message" id="order">
        <div class="thanks_title"><?php echo carbon_get_post_meta( $set, 'order_form_title' ); ?></div>
        <div class="thanks_title"></div>
		<?php echo do_shortcode( $f ); ?>
    </div>
<?php endif; ?>

<?php get_footer(); ?>
