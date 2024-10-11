<?php
get_header();
global $product;
$product_id               = $product->get_id();
$gallery                  = $product->get_gallery_image_ids();
$code                     = $product->get_sku();
$stock_status             = $product->get_stock_status();
$stock_quantity           = $product->get_stock_quantity();
$price                    = $product->get_price_html();
$description              = $product->get_description();
$var                      = variables();
$set                      = $var['setting_home'];
$assets                   = $var['assets'];
$url                      = $var['url'];
$url_home                 = $var['url_home'];
$id                       = get_the_ID();
$image                    = get_the_post_thumbnail_url();
$pictures_banner          = carbon_get_post_meta( $id, 'product_item_pictures' );
$product_item_description = carbon_get_post_meta( $id, 'product_item_description' );
$tag_image                = carbon_get_post_meta( $id, 'product_item_tag_image' );
$similar_title            = carbon_get_post_meta( $id, 'product_similar_list_title' );
$products                 = carbon_get_post_meta( $id, 'similar_products_list' );
$availability_text        = carbon_get_post_meta( $id, 'product_item_availability_text' );
$title                    = get_the_title();
$banner                   = carbon_get_post_meta( $id, 'product_item_banner' );
$wish_list                = $_COOKIE['wish_list'] ?? '';
if ( $wish_list ) {
	$wish_list = explode( ",", $wish_list );
}

if ( $pictures_banner ): ?>
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
							<?php _l( $product_item_description ); ?>
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
                                                <a class="zoom_image_open fancybox_gal"
                                                   href="<?php _u( $item ); ?>"></a>
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
							<?php _t( $description ); ?>
                        </div>
                        <div class="product__controls wow fadeInUp" data-wow-duration="2s">
                            <div class="product__price_top">
                                <div class="product__price">
                                    <strong><?php echo $price; ?></strong>
									<?php if ( $code ): ?>
                                        <span><?php _l( 'code' ); ?>: <?php echo $code; ?></span>
									<?php endif; ?>
                                </div>
                            </div>
                            <div class="product__controls_sides">
                                <div class="product__controls_side product_state">
									<?php if ( $stock_status == 'instock' ): ?>
                                        <div class="product_state_title green">
											<?php _l( 'Available on stock' ); ?>
                                        </div>
									<?php elseif ( $stock_status == 'outofstock' ): ?>
                                        <div class="product_state_title " style="color: #E32D2B;">
											<?php _l( 'Sold Out' ); ?>
                                        </div>
									<?php elseif ( $stock_status == 'onbackorder' ): ?>
                                        <div class="product_state_title " style="color: #1A73D7;">
											<?php _l( 'In Pre-Order' ); ?>
                                        </div>
									<?php endif; ?>
									<?php if ( $availability_text ): ?>
                                        <p><?php echo $availability_text; ?></p>
									<?php endif; ?>
                                </div>
                                <div class="product__controls_side product_order_controls">
									<?php if ( $product->is_purchasable() ): ?>
                                        <div class="quantity_wrapper">
                                            <input class="quantity_input"
                                                   type="text"
                                                   name="quantity"
                                                   value="1"
                                                   data-max="<?php echo $stock_quantity ?: 100; ?>"
                                                   data-reserved="<?php echo 0; ?>"
                                                   data-available="<?php echo $stock_quantity ?: 100; ?>"
                                                   readonly=""/>
                                            <a class="quant_btn plus_btn transition" href="javascript:void(0)"></a>
                                            <a class="quant_btn minus_btn transition" href="javascript:void(0)"></a>
                                        </div>
                                        <a class="product_btn add-to-cart-button"
                                           data-product_id="<?php echo $id; ?>"
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
						<?php the_wc_attributes( $product ); ?>
                    </div>
                </div>
				<?php if ( $products ): ?>
                    <div class="main_title centered ttu"><?php echo $similar_title; ?></div>
                    <div class="product_items">
						<?php foreach ( $products as $product ) {
							$_id = $product['id'];
							if ( get_post( $_id ) ) {
								the_wc_product( $_id );
							}
						} ?>
                    </div>
				<?php endif; ?>
            </div>
        </div>
    </section>

<?php get_footer(); ?>