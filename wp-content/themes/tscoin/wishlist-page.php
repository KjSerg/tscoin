<?php
/* Template Name: Wishlist page template */
get_header();
$var       = variables();
$set       = $var['setting_home'];
$assets    = $var['assets'];
$url       = $var['url'];
$url_home  = $var['url_home'];
$id        = get_the_ID();
$wish_list = $_COOKIE['wish_list'] ?? '';
if ( $wish_list ) {
	$wish_list = explode( ",", $wish_list );
}
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


<section class="section-product-items remove_pt">
    <div class="screen_content">
        <div class="light_frame items_inside">
            <div class="product_items">
				<?php if ( $wish_list ):
					$arr = array(
						'post_type'      => 'product',
						'posts_per_page' => - 1,
						'posts_status'   => 'publish',
					);
					$arr['post__in'] = $wish_list;
					$query = new WP_Query( $arr );
					if ( $query->have_posts() ):
						while ( $query->have_posts() ):
							$query->the_post();
							the_product();
						endwhile;
					else:
						?>
                        <div class="main_title centered">
							<?php _l( 'Not found' ) ?>
                        </div>
					<?php
					endif;
				else: ?>
                    <div class="main_title centered">
						<?php _l( 'Not found' ) ?>
                    </div>
				<?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php
wp_reset_postdata();
wp_reset_query();
get_footer(); ?>
