<?php
$route                  = $_GET['route'] ?? '';
$var                    = variables();
$set                    = $var['setting_home'];
$assets                 = $var['assets'];
$url                    = $var['url'];
$url_home               = $var['url_home'];
$user_id                = get_current_user_id();
$user                   = get_user_by( 'id', $user_id );
$countries              = get_woocommerce_countries();
$my_orders_url          = wc_get_account_endpoint_url( 'orders' );
$default_posts_per_page = get_option( 'posts_per_page' );
$customer_orders        = wc_get_orders( array(
	'posts_per_page' => - 1,
	'customer_id'    => $user_id,
	'status'         => 'any',
) );
?>

<section class="account-section section first_screen">
    <div class="screen_content">
        <div class="account-section__head">
			<?php _l( 'Personal Account' ) ?>
        </div>
        <div class="account-section-content">
            <div class="account-section-sidebar">
                <ul>
                    <li>
                        <a href="<?php echo get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ); ?>"
                        >
							<?php _l( 'My Information' ) ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $my_orders_url; ?>" class="active">
							<?php _l( 'My Orders' ) ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ); ?>?route=company"
                        >
							<?php _l( 'Company information' ) ?>
                        </a>
                    </li>
                </ul>
                <a href="<?php echo esc_url( wc_logout_url() ); ?>" class="logaut-button">
                    <span class="icon"><img src="<?php echo $assets; ?>img/logout.svg" alt=""></span>
					<?php _l( 'Log out' ) ?>
                </a>
            </div>
            <div class="account-section-box">
                <div class="account-section-box__title">
					<?php _l( 'My Orders' ) ?>
                </div>
                <div class="account-orders">
					<?php if ( $customer_orders ): foreach ( $customer_orders as $customer_order ) :
						the_user_order( $customer_order );
					endforeach;
					else: ?>
                        <div class="main_title centered">
							<?php _l( 'Not found' ) ?>
                        </div>
					<?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>