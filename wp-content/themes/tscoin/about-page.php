<?php
/* Template Name: About page template */
get_header();
$var           = variables();
$set           = $var['setting_home'];
$assets        = $var['assets'];
$url           = $var['url'];
$url_home      = $var['url_home'];
$id            = get_the_ID();
$isLighthouse  = isLighthouse();
$size          = isLighthouse() ? 'thumbnail' : 'full';
$default_image = get_the_post_thumbnail_url() ?: $assets . 'img/head_img6.jpg';
$sections      = carbon_get_post_meta( $id, 'about_sections' );
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

<?php if ( $sections ): ?>
    <section class="section-about remove_pt">
        <div class="screen_content">
			<?php foreach ( $sections as $section ):
				if ( $section['_type'] == 'text_section' ):
					?>
                    <div class="about_frame dark_bg ">
                        <div class="section_bg">
                            <img class="shadow_bg_img" src="<?php echo $assets; ?>img/shadow_bg_img.png" alt="img"/>
                        </div>
                        <div class="about_frame_content">
                            <div class="main_title ttu text_center">
								<?php echo $section['title']; ?>
                            </div>
                            <div class="simple_text">
								<?php _t( $section['text'] ); ?>
                            </div>
                        </div>
                    </div>
				<?php elseif ( $section['_type'] == 'dark_block' ): ?>
                    <div class="about_frame dark_bg">
                        <div class="about_frame_image section_bg">
                            <picture class="">
		                        <?php if ( $picture = $section['picture'] ): ?>
			                        <?php foreach ( $picture as $item ): ?>
                                        <source srcset="<?php _u( $item['image'] ) ?>"
                                                media="(min-width: <?php echo $item['min_width']; ?>px)"/>
			                        <?php endforeach; ?>
                                    <img src="<?php _u( $picture[0]['image'] ) ?>" alt=""/>
		                        <?php endif; ?>
                            </picture>
                        </div>
                        <div class="about_frame_content persone_description">
                            <div class="about_frame_head">
                                <div class="main_title">
									<?php echo $section['title']; ?>
                                </div>
                                <div class="persone_subtitle with_offset">
									<?php _t( $section['subtitle'] ); ?>
                                </div>
                            </div>
                            <div class="simple_text bottom_offset">
								<?php _t( $section['text'] ); ?>
                            </div>
                            <div class="about_frame_button">
								<?php the_buttons( $section['links'], 'dark_btn' ); ?>
                            </div>
                        </div>
                    </div>
				<?php elseif ( $section['_type'] == 'white_block' ): $background_color = $section['background_color']; ?>
                    <div class="about_frame light_frame">
                        <div class="about_frame_image section_bg">
                            <picture class="">
			                    <?php if ( $picture = $section['picture'] ): ?>
				                    <?php foreach ( $picture as $item ): ?>
                                        <source srcset="<?php _u( $item['image'] ) ?>"
                                                media="(min-width: <?php echo $item['min_width']; ?>px)"/>
				                    <?php endforeach; ?>
                                    <img src="<?php _u( $picture[0]['image'] ) ?>" alt=""/>
			                    <?php endif; ?>
                            </picture>
                        </div>
                        <div class="about_frame_content persone_description to_right">
                            <div class="about_frame_head">
                                <div class="main_title">
									<?php echo $section['title']; ?>
                                </div>
                                <div class="persone_subtitle with_offset">
									<?php _t( $section['subtitle'] ); ?>
                                </div>
                            </div>
                            <div class="simple_text bottom_offset">
								<?php _t( $section['text'] ); ?>
                            </div>
                            <div class="about_frame_button">
								<?php the_buttons( $section['links'], 'dark_btn' ); ?>
                            </div>
                        </div>
                    </div>
				<?php
				endif;
			endforeach;
			?>
        </div>
    </section>
<?php endif; ?>

<?php get_footer(); ?>