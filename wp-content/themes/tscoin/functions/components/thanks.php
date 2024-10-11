<?php
setcookie( 'ts_coin_cart', '', - 1 );
get_header();
$var           = variables();
$set           = $var['setting_home'];
$assets        = $var['assets'];
$url           = $var['url'];
$url_home      = $var['url_home'];
$id            = get_the_ID();
$isLighthouse  = isLighthouse();
$size          = isLighthouse() ? 'thumbnail' : 'full';
$page_id       = $_COOKIE['page_id'] ?? '';
$default_image = get_the_post_thumbnail_url( $page_id ) ?: $assets . 'img/head_img6.jpg';
$stripe_status = $_GET['stripe_status'] ?? '';
$paypal_status = $_GET['paypal_status'] ?? '';
$status        = $stripe_status ?: $paypal_status;
$order         = $_GET['order'] ?? '';
$secret        = $_GET['secret'] ?? '';
$paymentId     = $_GET['paymentId'] ?? '';
$p             = $_POST;
$order_secret  = '';
$test          = false;
if ( $p ) {
	carbon_set_post_meta( $order, 'order_test', json_encode( $p ) );
}
if ( $stripe_status == 'success' && $order && $secret ) {
	if ( get_post( $order ) ) {
		$s       = carbon_get_post_meta( $order, 'order_secret' );
		$status  = carbon_get_post_meta( $order, 'payment_status' );
		$subtest = base64_encode( $secret . $order ) == $s;
		if ( $subtest ) {
			$test = true;
		}
		if ( $subtest && $status != 'payed' ) {
			carbon_set_post_meta( $order, 'payment_status', 'payed' );
			sendMail( $order );
		}
	}
} else {
	if ( $order ) {
		if ( get_post( $order ) ) {
			$paymentId = $paymentId ?: carbon_get_post_meta( $order, 'payment_paypal_id' );
			var_dump($paymentId);
			if ( $paymentId ) {
				$status = getPayPalStatus( $paymentId );
				$state  = strtolower( $status['state'] ?? '' );
				if ( $state == 'approved' ) {
					$test = true;
					carbon_set_post_meta( $order, 'payment_status', 'payed' );
					sendMail( $order );
				} else {
					carbon_set_post_meta( $order, 'payment_status', 'not_pay' );
				}
			}
		}
	}
}
?>

<div class="bg_frame lozad" data-background-image="<?php echo $default_image; ?>">
    <div class="bg_frame_bottom"></div>
</div>
<section class="section-head first_screen animated_block dark_bg">
    <div class="section_bg">
        <div class="screen_content">
            <div class="head_title"><?php _l( 'attention to details' ); ?></div>
        </div>
    </div>
</section>
<section class="section-cart remove_pt">
    <div class="screen_content cart-container-js">
        <div class="cart_steps dark_bg">
            <div class="cart_step ch_block large active ">
                <div class="ch_block_icon large checked "></div>
                <span class="cart_step_txt"><?php _l( 'Cart' ); ?></span>
            </div>
            <div
                    class="cart_step ch_block large active">
                <div class="ch_block_icon large checked"></div>
                <span class="cart_step_txt"><?php _l( 'Addresses' ); ?></span>
            </div>
            <div
                    class="cart_step ch_block large active">
                <div class="ch_block_icon large checked"></div>
                <span class="cart_step_txt"><?php _l( 'Shipping Method' ); ?></span>
            </div>
            <div
                    class="cart_step ch_block large active">
                <div class="ch_block_icon large checked"></div>
                <span class="cart_step_txt"><?php _l( 'Payment' ); ?></span>
            </div>
            <div
                    class="cart_step ch_block large active">
                <div class="ch_block_icon large checked"></div>
                <span class="cart_step_txt"><?php _l( 'Complete' ); ?></span>
            </div>
        </div>
        <div class="cart_sides">
            <div class="light_frame cart_last">
                <div class="cart_last_container">
                    <div class="main_title">
						<?php
						if ( $page_id ) {
							echo carbon_get_post_meta( $page_id, $test ? 'thanks_title' : 'canceled_title' );
						} ?>
                    </div>
                    <div class="simple_text">
						<?php
						if ( $page_id ) {
							_t( carbon_get_post_meta( $page_id, $test ? 'thanks_text' : 'canceled_text' ) );
						} ?>
                        <br>
                        <br>
                        OrderID: <?php echo $order; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
