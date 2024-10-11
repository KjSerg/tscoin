<?php
$var                    = variables();
$set                    = $var['setting_home'];
$assets                 = $var['assets'];
$url                    = $var['url'];
$user_cookies           = $_COOKIE['user_cookies'] ?? '';
$id                     = get_the_ID();
$policy_page_id         = (int) get_option( 'wp_page_for_privacy_policy' );
$logo                   = carbon_get_theme_option( 'logo' );
$social_networks        = carbon_get_theme_option( 'social_networks' );
$form                   = carbon_get_post_meta( $set, 'short_code_form' );
$text1                  = carbon_get_post_meta( $set, 'footer_text_column_1' );
$text2                  = carbon_get_post_meta( $set, 'footer_text_column_2' );
$text3                  = carbon_get_post_meta( $set, 'footer_text_column_3' );
$is_order_received_page = function_exists( 'is_order_received_page' ) ? is_order_received_page() : false;
$is_checkout            = function_exists( 'is_checkout' ) ? is_checkout() : false;
$current_lang           = function_exists( 'pll_current_language' ) ? pll_current_language() : false;
if ( ! $is_checkout && function_exists( 'pll_current_language' ) && function_exists( 'wc_get_page_id' ) ) {
	$checkout_page_id = wc_get_page_id( 'checkout' );
	$checkout_ID      = pll_get_post( $checkout_page_id, $current_lang );
	$is_checkout      = $checkout_ID === $id;
}
if ( $is_order_received_page ) {
	$is_checkout = false;
}
?>

<script>
    var lang = '<?php echo $current_lang ?: ""; ?>';
    var checkout_lang = '<?php echo $is_checkout && $current_lang ? $current_lang : ""; ?>';
    var admin_ajax = '<?php echo $var['admin_ajax']; ?>';
    var addedString = '<?php _l( 'Added' ); ?>';
</script>
</main>
<?php if ( $user_cookies != 'accept' ): ?>
    <div class="cookies-settings">
        <div class="cookies-settings__title">
			<?php _l( 'Cookies settings' ) ?>
        </div>
        <div class="cookies-settings__text">
			<?php _l( 'Cookies text' ) ?>
        </div>
        <div class="cookies-settings-controls">
            <div class="cookies-settings__btn">
				<?php _l( 'decline' ) ?>
            </div>
            <div class="cookies-settings__btn accept">
				<?php _l( 'Accept' ) ?>
            </div>
        </div>
    </div>
<?php endif; ?>
<div class="preloader">
    <img src="<?php echo $assets; ?>img/loading.gif" alt="loading.gif">
</div>
<footer class="footer dark_bg">
    <div class="screen_content">
        <div class="footer_cols half_sides">
            <div class="footer_col left_col">
                <div class="footer_left_sides">
                    <div class="footer_left_side left">
                        <a class="footer_logo" href="<?php echo $url; ?>">
                            <img src="<?php _u( $logo ); ?>" alt="<?php bloginfo( 'name' ); ?>"/>
                        </a>
                        <div class="footer_left_cols">
							<?php if ( $text1 ): ?>
                                <div class="footer_left_col">
									<?php _t( $text1 ); ?>
                                </div>
							<?php endif; ?>
							<?php if ( $text2 ): ?>
                                <div class="footer_left_col">
									<?php _t( $text2 ); ?>
                                </div>
							<?php endif; ?>
                        </div>
                    </div>
					<?php if ( $text3 ): ?>
                        <div class="footer_left_side right">
							<?php _t( $text3 ); ?>
                        </div>
					<?php endif; ?>
                </div>
            </div>
            <div class="footer_col right_col">
                <div class="footer-row">
                    <div class="footer-row-item">
	                    <?php wp_nav_menu( [
		                    'theme_location' => 'footer_menu',
		                    'container'      => '',
		                    'menu_class'     => 'footer-menu',
		                    'add_a_class'    => ''
	                    ] ); ?>
                    </div>
                    <div class="footer-row-item">
                        <div class="footer_col_title"><?php _l( 'CONTACTS' ); ?></div>
						<?php if ( $form ) {
							echo do_shortcode( $form );
						} ?>
                    </div>
                    <div class="footer-row-item">
						<?php if ( $social_networks ): ?>
                            <ul class="soc_list ">
								<?php foreach ( $social_networks as $network ): ?>
                                    <li>
                                        <a class="soc_link" target="_blank" rel="nofollow"
                                           href="<?php echo $network['url']; ?>">
											<?php the_image( $network['icon'] ); ?>
                                        </a>
                                    </li>
								<?php endforeach; ?>
                            </ul>
						<?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="copyright">
				<?php echo carbon_get_post_meta( $set, 'copyright' ); ?>
            </div>
			<?php wp_nav_menu( [
				'theme_location' => 'policy_menu',
				'container'      => '',
				'menu_class'     => 'footer-links',
				'add_a_class'    => ''
			] ); ?>
        </div>
    </div>
</footer>
<div class="is_hidden thanks_message" id="myThanks">
    <div class="thanks_title"></div>
</div>
<?php wp_footer(); ?>
</body>
</html>