<?php
/* Template Name: Contact page template */
get_header();
$var             = variables();
$set             = $var['setting_home'];
$assets          = $var['assets'];
$url             = $var['url'];
$url_home        = $var['url_home'];
$id              = get_the_ID();
$isLighthouse    = isLighthouse();
$size            = isLighthouse() ? 'thumbnail' : 'full';
$form_title      = carbon_get_post_meta( $id, 'contact_form_title' );
$short_code      = carbon_get_post_meta( $id, 'contact_form_short_code' );
$list            = carbon_get_post_meta( $id, 'contact_list' );
$social_networks = carbon_get_post_meta( $id, 'social_networks' );
$default_image   = get_the_post_thumbnail_url() ?: $assets . 'img/head_img5.jpg';
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
<section class="section-contacts remove_pt">
    <div class="screen_content">
        <div class="cart_sides">
            <div class="cart_side main_side contacts_form_side">
                <div class="main_title"><?php echo $form_title; ?></div>
				<?php echo do_shortcode( $short_code ); ?>
            </div>
            <div class="cart_side aside_side">
                <div class="contact_aside_lines">
					<?php if ( $list ): foreach ( $list as $item ): ?>
                        <div class="contact_aside_line">
							<?php if ( $item['title'] ): ?>
                                <div class="contact_aside_title">
									<?php echo $item['title']; ?>
                                </div>
							<?php endif; ?>
                            <div class="contact_aside_text">
								<?php _t( $item['text'] ); ?>
                            </div>
                        </div>
					<?php endforeach; endif; ?>
					<?php if ( $social_networks ): ?>
                        <div class="contact_aside_line">
                            <div class="contact_aside_text">
                                <p class="ttu">
									<?php _l( 'Social media' ); ?>
                                </p>
                            </div>
                            <ul class="soc_list squered_icos rounded">
								<?php foreach ( $social_networks as $network ): ?>
                                    <li>
                                        <a class="soc_link" target="_blank" rel="nofollow"
                                           href="<?php echo $network['url']; ?>">
                                            <img src="<?php _u( $network['icon'] ); ?>"
                                                 alt="ico"/>
                                        </a>
                                    </li>
								<?php endforeach; ?>
                            </ul>
                        </div>
					<?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>


