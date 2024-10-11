<?php
$var                = variables();
$set                = $var['setting_home'];
$assets             = $var['assets'];
$url                = $var['url'];
$logo               = carbon_get_theme_option( 'logo' );
$social_networks    = carbon_get_theme_option( 'social_networks' );
$shopping_cart_page = wc_get_page_id( 'cart' );
$shopping_cart_page = $shopping_cart_page ?: 0;
if ( function_exists( 'pll_current_language' ) ) {
	if ( $shopping_cart_page ) {
		if ( get_post( $shopping_cart_page ) ) {
			$shopping_cart_page = pll_get_post( $shopping_cart_page, pll_current_language() );
		}
	}
}
?>

<!DOCTYPE html>
<html class="no-js page <?php echo carbon_get_theme_option( 'biggest_content' ) ? 'zoom100' : '' ?>" <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries-->
    <!-- WARNING: Respond.js doesn't work if you view the page via file://-->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
    <title><?php wp_title(); ?></title>
	<?php wp_head(); ?>
</head>

<body>


<header class="header">
    <div class="screen_content">
        <div class="header_mobile_line">
            <a class="main_logo" href="<?php echo $url; ?>">
                <img src="<?php _u( $logo ); ?>" alt="img"/>
            </a>
            <div class="header_mobile_right">
                <a class="cart_btn" href="<?php echo wc_get_cart_url(); ?>">
                    <img src="<?php echo $assets; ?>img/cart_ico.svg" alt="ico"/>
                </a>
                <a class="header_links_trigger header_links_trigger__js" href="#nav_list">
                    <span></span><span class="transparent"></span><span></span>
                </a>
            </div>
        </div>
        <div class="header_line" id="nav_list">
            <div class="header_line_left">
                <div class="header_logo">
                    <a class="main_logo" href="<?php echo $url; ?>">
                        <img src="<?php _u( $logo ); ?>" alt="<?php bloginfo( 'name' ); ?>"/>
                    </a>
                </div>
				<?php wp_nav_menu( [ 'theme_location' => 'header_menu','container' => '', 'menu_class' => 'nav_links', 'add_a_class' => 'nav_link' ] ); ?>
            </div>
            <div class="header_line_right">
                <div class="header_line_slogan"><?php _l( 'attention to details' ); ?></div>
				<?php if ( $social_networks ) : ?>
                    <ul class="soc_list header_soc_list">
						<?php foreach ( $social_networks as $network ) : ?>
                            <li>
                                <a class="soc_link" target="_blank" rel="nofollow"
                                   href="<?php echo $network['url']; ?>">
									<?php the_image( $network['icon'] ); ?>
                                </a>
                            </li>
						<?php endforeach; ?>
                    </ul>
				<?php endif; ?>
				<?php $language_switcher = wp_get_nav_menu_items( 'lang' ); ?>
				<?php if ( $language_switcher ) : ?>
                    <div class="lang_chooser select_wrapper">
                        <select class="select select_lang">
							<?php foreach ( $language_switcher as $item ) : if ( is_current_lang( $item ) ) : ?>
                                <option selected="selected"><?php echo $item->title; ?></option>
							<?php endif;
							endforeach; ?>
							<?php foreach ( $language_switcher as $item ) : if ( ! is_current_lang( $item ) ) : ?>

                                <option value="<?php echo $item->url; ?>"><?php echo $item->title; ?></option>

							<?php endif;
							endforeach; ?>
                        </select>

                    </div>
				<?php endif; ?>
                <div class="product_filter currency_filter">
					<?php
					if ( function_exists( 'wc_get_currency_switcher_markup' ) ) {
						$instance = [
							'symbol' => true,
							'flag'   => false,
						];
						$args     = [];
						echo wc_get_currency_switcher_markup( $instance, $args );
					}
					?>
                </div>
                <a class="cart_btn" href="<?php echo get_the_permalink( $shopping_cart_page ); ?>">
					<?php _s( _i( 'cart_ico' ) ); ?>
                </a>
                <a href="<?php echo get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ); ?>"
                   class="login-button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="52" height="52" viewBox="0 0 53 53" fill="none">
                        <path d="M35.3388 17.6612C32.9779 15.3002 29.8388 14 26.5 14C23.1612 14 20.0221 15.3002 17.6612 17.6612C15.3002 20.0221 14 23.1612 14 26.5C14 29.8388 15.3002 32.9779 17.6612 35.3388C20.0221 37.6998 23.1612 39 26.5 39C29.8388 39 32.9779 37.6998 35.3388 35.3388C37.6998 32.9779 39 29.8388 39 26.5C39 23.1612 37.6998 20.0221 35.3388 17.6612ZM19.4251 34.9618C19.8378 31.4103 22.8913 28.6683 26.5 28.6683C28.4024 28.6683 30.1913 29.4095 31.5369 30.7549C32.6737 31.8919 33.3907 33.3764 33.5751 34.9616C31.6582 36.567 29.1901 37.5352 26.5 37.5352C23.8099 37.5352 21.342 36.5672 19.4251 34.9618ZM26.5 27.1596C24.4067 27.1596 22.7034 25.4563 22.7034 23.363C22.7034 21.2695 24.4067 19.5664 26.5 19.5664C28.5933 19.5664 30.2966 21.2695 30.2966 23.363C30.2966 25.4563 28.5933 27.1596 26.5 27.1596ZM34.8359 33.7233C34.4626 32.2184 33.6842 30.8308 32.5726 29.7192C31.6723 28.819 30.6073 28.1436 29.4457 27.7203C30.8421 26.7733 31.7614 25.1734 31.7614 23.363C31.7614 20.4619 29.4011 18.1016 26.5 18.1016C23.5989 18.1016 21.2386 20.4619 21.2386 23.363C21.2386 25.1744 22.1587 26.7748 23.5562 27.7217C22.4875 28.1111 21.4986 28.7133 20.6477 29.506C19.4155 30.6534 18.5599 32.1166 18.1632 33.7222C16.483 31.7853 15.4648 29.2594 15.4648 26.5C15.4648 20.4152 20.4152 15.4648 26.5 15.4648C32.5848 15.4648 37.5352 20.4152 37.5352 26.5C37.5352 29.2599 36.5166 31.7864 34.8359 33.7233Z"
                              fill="white"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</header>
<main class="content">