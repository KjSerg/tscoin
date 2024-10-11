<?php

get_header();
$var           = variables();
$set           = $var['setting_home'];
$assets        = $var['assets'];
$url           = $var['url'];
$url_home      = $var['url_home'];
$default_image = get_the_post_thumbnail_url() ?: $assets . 'img/head_img6.jpg';
$is_account    = function_exists( 'is_account_page' ) ? is_account_page() : false;
$is_checkout   = function_exists( 'is_checkout' ) ? is_checkout() : false;
$current_lang  = function_exists( 'pll_current_language' ) ? pll_current_language() : false;
if ( $is_account ) {
	$default_image = get_the_post_thumbnail_url() ?: $assets . 'img/login-page.jpg';
}
$is_cart = function_exists( 'is_cart' ) ? is_cart() : false;
$cls     = '';
if ( $is_checkout ) {
	$cls = 'wc-checkout-page';
} elseif ( $is_cart ) {
	$cls = 'wc-cart-page';
}
?>


<div class="bg_frame lozad <?php echo $cls; ?>" data-background-image="<?php echo $default_image; ?>">
    <div class="bg_frame_bottom"></div>
</div>

<?php if ( ! $is_account ): ?>
    <section class="section-head first_screen animated_block dark_bg <?php echo $cls; ?>">
        <div class="section_bg">
            <div class="screen_content">
                <div class="head_title"><?php _l( 'attention to details' ); ?></div>
            </div>
        </div>
        <div class="screen_content">
            <div class="main_title large ttu animation_up delay1"><?php echo get_the_title(); ?></div>
            <div class="simple_text larger_text animation_up delay2"></div>
        </div>
    </section>
    <div class="section  <?php if ( is_woocommerce() ) {
		echo 'wc-custom';
	}else{
	    echo 'dark_bg';
    } ?> ">
        <div class="screen_content">
			<?php
			if ( ! is_woocommerce() ) {
				echo '<div class="simple_text">';
			}
			the_post();
			the_content();
			if ( ! is_woocommerce() ) {
				echo '</div>';
			}
			?>
        </div>
    </div>
<?php else: ?>
	<?php the_post();
	the_content(); ?>
<?php endif; ?>




<?php
get_footer() ?>
