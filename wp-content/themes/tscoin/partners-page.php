<?php
/* Template Name: Partners page template */
get_header();
$var           = variables();
$set           = $var['setting_home'];
$assets        = $var['assets'];
$url           = $var['url'];
$url_home      = $var['url_home'];
$id            = get_the_ID();
$isLighthouse  = isLighthouse();
$size          = isLighthouse() ? 'thumbnail' : 'full';
$default_image = get_the_post_thumbnail_url() ?: $assets . 'img/head_img8.jpg';
$list          = carbon_get_post_meta( $id, 'partners_list' );
$string        = _l( 'Read more', 1 );
?>


    <div class="bg_frame lozad" data-background-image="<?php echo $default_image; ?>">
        <div class="bg_frame_bottom"></div>
    </div>

    <section class="section-head first_screen dark_bg animated_block">
        <div class="section_bg">
            <div class="screen_content">
                <div class="head_title"><?php _l( 'attention to details' ); ?></div>
            </div>
        </div>
        <div class="screen_content">
            <div class="main_title large animation_up delay1">
				<?php echo get_the_title(); ?>
            </div>
            <div class="simple_text larger_text animation_up delay2">
				<?php the_post();
				the_content(); ?>
            </div>
        </div>
    </section>

<?php if ( $list ): ?>
    <section class="section-partners remove_pt">
        <div class="screen_content">
            <div class="partners_frame">
				<?php foreach ( $list as $i => $item ): ?>
                    <div class="partners_line">
                        <div class="partner_line_headng">
                            <div class="partner_line_title">
                                <a class="partner_line_trigger__js partner_title_in"
                                   href="#partners_line<?php echo $i; ?>">
									<?php echo $item['title']; ?>
                                </a>
                            </div>
                            <div class="partner_line_descr">
								<?php _t( $item['text'] ); ?>
                            </div>
                        </div>
						<?php if ( $coins = $item['coins'] ): ?>
                            <div class="partners_line_frame" id="partners_line<?php echo $i; ?>">
                                <div class="light_frame">
                                    <div class="partner_blocks">
										<?php foreach ( $coins as $coin ):
											$_id = $coin['id'];
											if ( get_post( $_id ) ):
												$_img = get_the_post_thumbnail_url( $_id ) ?: $assets . 'img/product12.jpg';
												$_text = get_content_by_id( $_id );
												$_title = get_the_title( $_id );
												$arr = explode( "<!--more-->", $_text );
												?>
                                                <div class="partner_block product_item">
                                                    <a class="product_item_img fancybox_gal"
                                                       href="<?php echo $_img; ?>">
                                                        <img src="<?php echo $_img; ?>"
                                                             alt="<?php echo get_the_title( $_id ); ?>"/>
                                                    </a>
                                                    <div class="product_item_body">
                                                        <div class="product_item_title">
															<?php echo get_the_title( $_id ); ?>
                                                        </div>
														<?php _t( $arr[0] ); ?>
														<?php if ( count( $arr ) > 1 ) {
															echo "<a class='show-modal-info' data-title='$_title' data-img='$_img' data-id='$_id' href='#partner_modal'><strong>$string</strong></a>";
														} ?>
                                                    </div>
                                                </div>
											<?php endif; endforeach; ?>
                                    </div>
                                </div>
                            </div>
						<?php endif; ?>
                    </div>
				<?php endforeach; ?>

                <div class="partners_line">
                    <div class="partner_line_headng">
                        <span class="partner_line_title"><span class="partner_title_in no_arrow">...</span></span><span
                                class="partner_line_descr"> </span>
                    </div>
                </div>
            </div>
            <div class="partner_modal is_hidden" id="partner_modal">
                <div class="product_item_img"></div>
                <div class="product_item_body"></div>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php get_footer(); ?>