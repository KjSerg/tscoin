<?php
/* Template Name: Home page template */
$stripe_status = $_GET['stripe_status'] ?? '';
$paypal_status = $_GET['paypal_status'] ?? '';
$action        = $_GET['action_name'] ?? '';

if ( $stripe_status != '' || $paypal_status != '' ) {
	get_template_part( 'functions/components/thanks' );
	die();
}
get_header();
$var           = variables();
$set           = $var['setting_home'];
$assets        = $var['assets'];
$url           = $var['url'];
$url_home      = $var['url_home'];
$id            = get_the_ID();
$isLighthouse  = isLighthouse();
$size          = isLighthouse() ? 'thumbnail' : 'full';
$screens       = carbon_get_post_meta( $id, 'screens' );
$default_image = carbon_get_post_meta( $id, 'default_image' );
if ( ! empty( $screens ) ) :
	foreach ( $screens as $index => $screen ) :
		if ( $screen['_type'] == 'screen_1' ) :
			if ( ! $screen['screen_off'] ) :
				?>

				<?php if ( $banners = $screen['banners'] ): ?>

                <div class="bg_frame_slider">
					<?php foreach ( $banners as $banner ): ?>
                        <div>
                            <div class="bg_frame first_animated_slide">
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
								<?php else: ?>
                                    <div class="bg_frame_in lozad"
                                         data-background-image="<?php _u( $banner['image'] ); ?>"></div>
								<?php endif; ?>
                                <div class="bg_frame_bottom"></div>
                            </div>
                        </div>
					<?php endforeach; ?>
                </div>
                <section id="<?php echo $screen['id']; ?>" class="section-head first_screen dark_bg">
                    <div class="section_bg">
                        <div class="screen_content">
                            <div class="head_title"><?php _l( 'attention to details' ); ?></div>
                        </div>
                    </div>
                    <div class="screen_content head_main_container">
                        <div class="coin_sides">
                            <div class="coin_side content_side">
                                <div class="coin_pretitle"><?php echo $screen['subtitle']; ?></div>
                                <div class="head_coins_slider head_coins_slider_<?php echo $index; ?>">
									<?php foreach ( $banners as $banner ): ?>
                                        <div>
                                            <div class="head_coins_slide"
                                                 data-name="<?php echo strip_tags( $banner['title'] ); ?>">
                                                <div class="main_title large ttu animation_up delay1">
													<?php echo $banner['title']; ?>
                                                </div>
                                                <div class="coin_description simple_text animation_up delay2">
													<?php _t( $banner['text'] ); ?>
                                                </div>
                                                <div class="main_btn_wrapper animation_up delay3">
													<?php the_buttons( $banner['links'] ); ?>
                                                </div>
                                            </div>
                                        </div>
									<?php endforeach; ?>
                                </div>
                            </div>
                            <div class="coin_side img_placeholder">
                                <div class="slider_controls slider_controls__js">
                                    <button class="slider_control prev"
                                            data-target=".head_coins_slider_<?php echo $index; ?>">
                                        <img src="<?php echo $assets; ?>img/chevron_left.svg" alt="ico"/>
                                    </button>
                                    <button class="slider_control next"
                                            data-target=".head_coins_slider_<?php echo $index; ?>">
                                        <img src="<?php echo $assets; ?>img/chevron_right.svg" alt="ico"/>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="head_coins_slider_dots" id="head_coins_slider_dots"></div>
                    </div>
                </section>

			<?php else: ?>

                <div class="bg_frame_slider">
                    <div>
                        <div class="bg_frame first_animated_slide">
                            <div class="bg_frame_in lozad" data-background-image="<?php _u( $default_image ); ?>"></div>
                            <div class="bg_frame_bottom"></div>
                        </div>
                    </div>
                </div>

			<?php endif; ?>

			<?php
			endif;
        elseif ( $screen['_type'] == 'screen_2' ):
			if ( ! $screen['screen_off'] ):
				?>

				<?php
				$years      = get_terms( array(
					'hide_empty' => false,
					'taxonomy'   => 'years',
				) );
				$themes     = get_terms( array(
					'hide_empty' => false,
					'taxonomy'   => 'coin_theme',
				) );
				$countries  = get_terms( array(
					'hide_empty' => false,
					'taxonomy'   => 'country',
				) );
				$features   = get_terms( array(
					'hide_empty' => false,
					'taxonomy'   => 'features',
				) );
				$currencies = carbon_get_theme_option( 'currencies' );
				?>

                <section id="<?php echo $screen['id']; ?>" class="section-product-items remove_pt">
                    <div class="screen_content">
                        <div class="light_frame">
                            <div class="product_filters_frame dark_bg">
                                <a class="filters_trigger filters_trigger__js" href="#filters">
                                    <img src="<?php echo $assets; ?>img/filters.svg" alt="ico"/>
                                </a>
                                <div class="product_filters_block" id="filters">
                                    <div class="product_filters_heading">
                                        <div class="product_filters_ttl"><?php _l( 'Filter' ); ?></div>
                                        <a class="mobile_filters_close filters_trigger__js" href="#filters">
                                            <img src="<?php echo $assets; ?>img/close.svg" alt="ico"/>
                                        </a>
                                    </div>
									<?php the_product_filter(); ?>
                                </div>
                            </div>
							<?php the_products( $screen['products'] ); ?>
                        </div>
                    </div>
                </section>

			<?php
			endif;
		endif;
	endforeach;
endif; ?>
<?php get_footer(); ?>


