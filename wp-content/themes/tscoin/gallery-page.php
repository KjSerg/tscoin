<?php
/* Template Name: Gallery page template */
get_header();
$var           = variables();
$set           = $var['setting_home'];
$assets        = $var['assets'];
$url           = $var['url'];
$url_home      = $var['url_home'];
$id            = get_the_ID();
$isLighthouse  = isLighthouse();
$size          = isLighthouse() ? 'thumbnail' : 'full';
$default_image = get_the_post_thumbnail_url() ?: $assets . 'img/bg_head.jpg';
$list          = carbon_get_post_meta( $id, 'gallery_list' );
?>
    <section class="section-head first_screen dark_bg animated_block">
        <div class="section_bg mobile_gradient lozad" data-background-image="<?php echo $default_image; ?>">
            <div class="screen_content">
                <div class="head_title"><?php _l( 'attention to details' ); ?></div>
            </div>
        </div>
        <div class="screen_content">
            <div class="main_title large animation_up delay1">
				<?php echo get_the_title(); ?>
            </div>
            <div class="simple_text larger_text head_mih_block animation_up delay2">
				<?php the_post();
				the_content(); ?>
            </div>
        </div>
    </section>

<?php if ( $list ): ?>
    <section class="section-gallery remove_all_paddings">
		<?php foreach ( $list as $item ):
			$gallery = $item['gallery'];
			$subtitles = $item['subtitles'];
			$title = $item['title'];
			?>
            <div class="gallery_line">
                <div class="gallery_side left_side">
                    <a class="gallery_block fancybox left_half lozad"
                       data-background-image="<?php _u( $gallery[0] ); ?>"
                       href="<?php _u( $gallery[0] ); ?>">
		            <span class="gallery_block_head">
			            <span class="gallery_block_title"><?php echo $title; ?></span>
                        <?php if ( $subtitles ): foreach ( $subtitles as $subtitle ): ?>
                            <span><?php echo $subtitle['subtitle'] ?></span>
                        <?php endforeach; endif; ?>
		            </span>
                    </a>
                </div>
                <div class="gallery_side right_side">
                    <div class="gallery_col">
						<?php foreach ( $gallery as $j => $image_id ):
							$count = count( $gallery );
							$test = $j > 0 && $j < 4;
							if ( $count <= 5 ) {
								$test = $j > 0 && $j < 3;
							}
							if ( $test ):
								$cls = count( $gallery ) <= 5 ? 'vertical_2' : 'vertical_3';
								?>
                                <a class="gallery_block fancybox <?php echo $cls; ?> lozad"
                                   data-background-image="<?php _u( $image_id ); ?>"
                                   href="<?php _u( $image_id ); ?>"></a>
							<?php endif; endforeach; ?>
                    </div>
                    <div class="gallery_col">
						<?php foreach ( $gallery as $j => $image_id ):
							$count = count( $gallery );
							$test = $j > 3 && $j < 7;
							if ( $count <= 5 ) {
								$test = $j > 2 && $j < 5;
							}
							if ( $test ):
								$cls = count( $gallery ) < 7 ? 'vertical_2' : 'vertical_3';
								?>
                                <a class="gallery_block fancybox <?php echo $cls; ?> lozad"
                                   data-background-image="<?php _u( $image_id ); ?>"
                                   href="<?php _u( $image_id ); ?>"></a>
							<?php endif; endforeach; ?>
                    </div>
                </div>
            </div>
			<?php foreach ( $gallery as $j => $image_id ):
			$test = $j > 6;
			if ( $test ):
				?>
                <div class="gallery_line wide_image_inside">
                    <img src="<?php _u( $image_id ); ?>" alt="img"/>
                </div>
			<?php endif; endforeach; ?>
		<?php endforeach; ?>
    </section>
<?php endif; ?>

<?php get_footer(); ?>