<?php

add_action( 'wp_ajax_nopriv_get_content', 'get_content' );
add_action( 'wp_ajax_get_content', 'get_content' );

function get_content() {
	echo $_POST['id'] ? get_content_by_id( $_POST['id'] ) : '';
	die();
}

add_action( 'wp_ajax_nopriv_new_order', 'new_order' );
add_action( 'wp_ajax_new_order', 'new_order' );
/**
 * @throws HttpException
 * @throws \Stripe\Exception\ApiErrorException
 */
function new_order() {
	get_template_part( 'vendor/autoload' );
	$time              = time();
	$var               = variables();
	$set               = $var['setting_home'];
	$assets            = $var['assets'];
	$url               = $var['url'];
	$url_home          = $var['url_home'];
	$first_name        = $_POST['first_name'] ?? '';
	$last_name         = $_POST['last_name'] ?? '';
	$email             = $_POST['email'] ?? '';
	$address           = $_POST['address'] ?? '';
	$country           = $_POST['country'] ?? '';
	$city              = $_POST['city'] ?? '';
	$postcode          = $_POST['postcode'] ?? '';
	$phone_number      = $_POST['phone_number'] ?? '';
	$delivery_price    = $_POST['delivery_price'] ?? 0;
	$delivery_method   = $_POST['delivery_method'] ?? '';
	$payment_method    = $_POST['payment_method'] ?? '';
	$comment           = $_POST['comment'] ?? '';
	$delivery_address  = $_POST['delivery_address'] ?? '';
	$cart              = $_POST['cart'] ?? '';
	$remove_packaging  = $_POST['remove_packaging'] ?? '';
	$company           = $_POST['company'] ?? '';
	$page_id           = $_POST['page_id'] ?? '';
	$delivery_price    = (float) $delivery_price;
	$stripe_secret_key = carbon_get_theme_option( 'stripe_secret_key' );
	$currency          = get_current_currency();
	if ( $email && $phone_number && $cart ) {
		$cart = stripcslashes( $cart );
		$cart = stripcslashes( $cart );
		$cart = json_decode( $cart, true );
		$name = $first_name . ' ' . $last_name;
		if ( $remove_packaging ) {
			$remove_packaging = explode( ",", $remove_packaging );
		}
		$post_data = array(
			'post_type'   => 'orders',
			'post_title'  => 'New order ' . $name . ' ' . $phone_number,
			'post_status' => 'pending'
		);
		$post_id   = wp_insert_post( $post_data );
		$post      = get_post( $post_id );
		if ( $post ) {
			$order_secret = base64_encode( $time . $post_id );
			carbon_set_post_meta( $post_id, 'order_secret', $order_secret );
			$stripe_array = [
				'client_reference_id' => $post_id,
				'customer_email'      => $email,
				'mode'                => 'payment',
				'success_url'         => $url . '?stripe_status=success&order=' . $post_id . '&secret=' . $time,
				'cancel_url'          => $url . '?stripe_status=cancel&order=' . $post_id,
				'line_items'          => [],
				'metadata'            => [
					'order_id' => $post_id,
				],
			];
			$paypal_array = array(
				'intent'        => "sale",
				"payer"         => array(
					"payment_method" => "paypal"
				),
				'transactions'  => array(
					array(
						'description' => 'OrderID: ' . $post_id,
						'amount'      => array(
							'currency' => $currency,
							'details'  => array()
						),
						'item_list'   => array(
							'items'            => array(),
							'shipping_address' => array(
								'recipient_name' => $name,
								'phone'          => $phone_number,
								'postal_code'    => $postcode,
								'city'           => $city,
								'line1'          => $delivery_address ?: $address,
								'country_code'   => get_country_code_by_name( $country ),
							),
						)
					),
				),
				'redirect_urls' => array(
					"return_url" => "$url?paypal_status=success&order=$post_id&secret=$time",
					"cancel_url" => "$url?paypal_status=cancel&order=$post_id",
				),
				'note_to_payer' => 'OrderID: ' . $post_id,
			);
			$_currency    = strtolower( $currency );
			$order_cart   = array();
			$total        = 0;
			foreach ( $cart as $_id => $item ) {
				if ( get_post( $_id ) ) {
					if ( function_exists( 'pll_current_language' ) ) {
						$test = ( $remove_packaging && in_array( $_id, $remove_packaging ) );
						$_id  = function_exists( 'pll_current_language' ) ? pll_get_post( $_id, pll_current_language() ) : $_id;
						if ( $test && get_post( $_id ) ) {
							$remove_packaging[] = $_id;
						}
						if ( $remove_packaging ) {
							$remove_packaging = array_unique( $remove_packaging );
						}
					}
					$qnt       = $item['qnt'] ?? 1;
					$is_active = $item['is_active'] ?? 'true';
					clearing_reserved( $_id, $qnt );
					wp_clear_scheduled_hook( 'clear_reserved', array( $_id, $qnt ) );
					if ( $is_active == 'true' ) {
						$price   = carbon_get_post_meta( $_id, "product_price_$_currency" ) ?: carbon_get_post_meta( $_id, "product_price" );
						$saved   = carbon_get_post_meta( $_id, "product_saved_$_currency" ) ?: carbon_get_post_meta( $_id, "product_saved" );
						$package = carbon_get_post_meta( $_id, "product_package_price_$_currency" ) ?: carbon_get_post_meta( $_id, "product_package_price" );
						$saved   = $saved ?: 0;
						if ( $remove_packaging && in_array( $_id, $remove_packaging ) ) {
							$saved = $saved + $package;
						}
						if ( $saved > 0 ) {
							$saved = $saved * $qnt;
						}
						$sub_price                                               = ( $price * $qnt ) - $saved;
						$total                                                   = $total + $sub_price;
						$order_cart[]                                            = array(
							'image'    => get_post_thumbnail_id( $_id ),
							'title'    => get_the_title( $_id ),
							'id'       => $_id,
							'qnt'      => $qnt,
							'price'    => $price,
							'subtotal' => $sub_price,
							'package'  => $remove_packaging && in_array( $_id, $remove_packaging ) ? 'without_packaging' : 'package',
						);
						$stripe_array['line_items'][]                            = array(
							'price_data' => [
								'currency'     => $_currency,
								'product_data' => [
									'name' => get_the_title( $_id ),
								],
								'unit_amount'  => ( $sub_price * 100 ),
							],
							'quantity'   => $qnt,
						);
						$paypal_array['transactions'][0]['item_list']['items'][] = array(
							'name'        => get_the_title( $_id ),
							"quantity"    => $qnt,
							"price"       => $sub_price,
							'currency'    => $currency,
							'description' => 'Subtotal: ' . $sub_price . ' [' . ( $remove_packaging && in_array( $_id, $remove_packaging ) ) ? 'without packaging' : 'package' . ']',
						);
					}
				}
			}
			$paypal_array['transactions'][0]['amount']['details']['subtotal'] = $total;
			$total                                                            = $total + $delivery_price;
			$paypal_array['transactions'][0]['amount']['total']               = $total;
			if ( $delivery_price > 0 ) {
				$stripe_array['line_items'][]                                     = [
					'price_data' => [
						'currency'     => $_currency,
						'product_data' => [
							'name' => 'Shipping: ' . $delivery_method,
						],
						'unit_amount'  => ( $delivery_price * 100 ),
					],
					'quantity'   => 1,
				];
				$paypal_array['transactions'][0]['amount']['details']['shipping'] = $delivery_price;
			} else {
				$paypal_array['transactions'][0]['amount']['details']['shipping'] = 0;
			}
			carbon_set_post_meta( $post_id, 'order_name', $name );
			carbon_set_post_meta( $post_id, 'order_email', $email );
			carbon_set_post_meta( $post_id, 'order_company', $company );
			carbon_set_post_meta( $post_id, 'order_street_address', $address );
			carbon_set_post_meta( $post_id, 'order_country', $country );
			carbon_set_post_meta( $post_id, 'order_city', $city );
			carbon_set_post_meta( $post_id, 'order_postcode', $postcode );
			carbon_set_post_meta( $post_id, 'order_number', $phone_number );
			carbon_set_post_meta( $post_id, 'order_delivery_address', $delivery_address );
			carbon_set_post_meta( $post_id, 'order_comment', $comment );
			carbon_set_post_meta( $post_id, 'order_delivery_price', $delivery_price );
			carbon_set_post_meta( $post_id, 'order_delivery_method', $delivery_method );
			carbon_set_post_meta( $post_id, 'order_payment_method', $payment_method );
			carbon_set_post_meta( $post_id, 'order_cart', $order_cart );
			carbon_set_post_meta( $post_id, 'order_total', $total );
			carbon_set_post_meta( $post_id, 'order_currency', $currency );
			if ( $payment_method == 'stripe' && $stripe_secret_key ) {
				$stripe           = new \Stripe\StripeClient( $stripe_secret_key );
				$checkout_session = $stripe->checkout->sessions->create( $stripe_array );
				$payment_intent   = $checkout_session->payment_intent;
				$payment_id       = $checkout_session->id;
				carbon_set_post_meta( $post_id, 'payment_intent', $payment_intent );
				carbon_set_post_meta( $post_id, 'payment_stripe_id', $payment_id );
				echo json_encode( array(
					'url' => $checkout_session->url,
				) );
				die();
			}
			if ( $payment_method == 'paypal' ) {
				$obj = sendPayPalRequest( $paypal_array );
				carbon_set_post_meta( $post_id, 'order_test', json_encode( $paypal_array ) );
				$links          = $obj['links'];
				$__id           = $obj['id'] ?? '';
				$invoice_number = $obj['invoice_number'] ?? '';
				carbon_set_post_meta( $post_id, 'payment_paypal_invoice_number', $invoice_number );
				carbon_set_post_meta( $post_id, 'payment_paypal_id', $__id );
				$url = '';
				$a   = array();
				if ( $links ) {
					foreach ( $links as $link ) {
						$method = $link['method'];
						if ( $method == 'REDIRECT' ) {
							$url = $link['href'];
						}
					}
				} else {
					if ( $obj ) {
						$d = $obj['details'] ?? '';
						if ( $d ) {
							foreach ( $d as $item ) {
								$m   = $item['issue'];
								$a[] = $m;
							}
						}
					}
				}
				echo json_encode( array(
					'url'           => $url,
					'$obj'          => $obj,
					'$paypal_array' => $paypal_array,
					'msg'           => $a ? implode( ', ', $a ) : '',
				) );
				die();
			}
			if ( $page_id ) {
				?>
                <div class="light_frame cart_last">
                    <div class="cart_last_container">
                        <div class="main_title">
							<?php echo carbon_get_post_meta( $page_id, 'thanks_title' ); ?>
                        </div>
                        <div class="simple_text">
							<?php _t( carbon_get_post_meta( $page_id, 'thanks_text' ) ); ?>
                        </div>
                    </div>
                </div>
				<?php
			}
			sendMail( $post_id );
		} else {
			echo '<div class="light_frame cart_last"><div class="main_title">Error</div></div>';
		}
	} else {
		echo '<div class="light_frame cart_last"><div class="main_title">Error</div></div>';
	}
	die();
}

add_action( 'wp_ajax_nopriv_set_cart_cron', 'set_cart_cron' );
add_action( 'wp_ajax_set_cart_cron', 'set_cart_cron' );
function set_cart_cron() {
	$currency         = get_current_currency();
	$cart             = stripcslashes( $_COOKIE['ts_coin_cart'] ?? '{}' );
	$cart             = json_decode( $cart, true );
	$remove_packaging = $_COOKIE['remove_packaging'] ?? '';
	if ( $remove_packaging ) {
		$remove_packaging = explode( ",", $remove_packaging );
	}
	if ( $cart ):
		foreach ( $cart as $_id => $item ):
			if ( get_post( $_id ) ):
				$_ids = array( $_id );
				if ( function_exists( 'pll_current_language' ) ) {
					$test = ( $remove_packaging && in_array( $_id, $remove_packaging ) );
					$_id  = pll_get_post( $_id, pll_current_language() );
					if ( $test && get_post( $_id ) ) {
						$remove_packaging[] = $_id;
					}
					if ( $remove_packaging ) {
						$remove_packaging = array_unique( $remove_packaging );
					}
					if ( function_exists( 'pll_languages_list' ) ) {
						if ( $languages = pll_languages_list() ) {
							foreach ( $languages as $language ) {
								$_ID = pll_get_post( $_id, $language );
								if ( get_post( $_ID ) && $_ID != 0 ) {
									$_ids[] = $_ID;
								}
							}
						}
						$_ids = array_unique( $_ids );
					}
				}
				$product_qnt  = carbon_get_post_meta( $_id, 'product_qnt' ) ?: 1;
				$reserved_qnt = carbon_get_post_meta( $_id, 'product_reserved_qnt' ) ?: 0;
				$available    = $product_qnt - $reserved_qnt;
				$qnt          = $item['qnt'] ?? 1;
				$is_active    = $item['is_active'] ?? 'true';
				if ( $available > $product_qnt ) {
					$qnt = $available;
				}
				if ( $is_active == 'true' ) {
					carbon_set_post_meta( $_id, 'product_reserved_qnt', $qnt );
					wp_schedule_single_event( ( time() + ( 60 * 20 ) ), 'clear_reserved', array( $_id, $qnt ) );
				}
			endif;
		endforeach;
	endif;
	die();
}

add_action( 'wp_ajax_nopriv_get_cart_total', 'get_cart_total' );
add_action( 'wp_ajax_get_cart_total', 'get_cart_total' );
function get_cart_total() {
	echo get_cart_sum();
	die();
}

function sendMail( $id ) {
	$c            = true;
	$message      = '';
	$project_name = get_bloginfo( 'name' );
	$var          = variables();
	$set          = $var['setting_home'];
	$assets       = $var['assets'];
	$url          = $var['url'];
	$url_home     = $var['url_home'];
	$admin_email  = get_bloginfo( 'admin_email' );
	$form_subject = 'New order №' . $id;
	$user_name    = carbon_get_post_meta( $id, 'order_name' );
	$phone        = carbon_get_post_meta( $id, 'order_number' );
	$sum1         = carbon_get_post_meta( $id, 'order_total' );
	$email        = carbon_get_post_meta( $id, 'order_email' );
	$currency     = carbon_get_post_meta( $id, 'order_currency' );
	$html         = '';
	$html1        = '';
	$items        = carbon_get_post_meta( $id, 'order_cart' );
	if ( $items ) {
		foreach ( $items as $item ) {
			$sum      = (int) $item['qnt'] * (int) $item['price'];
			$subtotal = (int) $item['subtotal'];
			$html     .= '<a target="_blank" href="' . $url . 'wp-admin/post.php?post=' . $item['id'] . '&action=edit">' . $item['title'] . ' (' . $item['qnt'] . ' x ' . $item['price'] . ' = ' . $currency . $subtotal . ')</a> <br>';
			$html1    .= '<div>' . $item['title'] . ' (' . $item['qnt'] . ' x ' . $item['price'] . ' = ' . $currency . $subtotal . ')</div> <br>';
		}
	}
	$message  .= "
			" . ( ( $c = ! $c ) ? '<tr>' : '<tr style="background-color: #f8f8f8;">' ) . "
			<td style='padding: 10px; border: #e9e9e9 1px solid;'><b>Name</b></td>
			<td style='padding: 10px; border: #e9e9e9 1px solid;'>$user_name</td>
		</tr>
		";
	$message  .= "
			" . ( ( $c = ! $c ) ? '<tr>' : '<tr style="background-color: #f8f8f8;">' ) . "
			<td style='padding: 10px; border: #e9e9e9 1px solid;'><b>Phone</b></td>
			<td style='padding: 10px; border: #e9e9e9 1px solid;'>$phone</td>
		</tr>
		";
	$message  .= "
			" . ( ( $c = ! $c ) ? '<tr>' : '<tr style="background-color: #f8f8f8;">' ) . "
			<td style='padding: 10px; border: #e9e9e9 1px solid;'><b>Sum</b></td>
			<td style='padding: 10px; border: #e9e9e9 1px solid;'>$sum1</td>
		</tr>
		";
	$message1 = $message;
	$message  .= "
			" . ( ( $c = ! $c ) ? '<tr>' : '<tr style="background-color: #f8f8f8;">' ) . "
			<td style='padding: 10px; border: #e9e9e9 1px solid;'><b>Order</b></td>
			<td style='padding: 10px; border: #e9e9e9 1px solid;'>$html</td>
		</tr>
		";
	$message1 .= "
			" . ( ( $c = ! $c ) ? '<tr>' : '<tr style="background-color: #f8f8f8;">' ) . "
			<td style='padding: 10px; border: #e9e9e9 1px solid;'><b>Order</b></td>
			<td style='padding: 10px; border: #e9e9e9 1px solid;'>$html1</td>
		</tr>
		";
	$message  = "<table style='width: 100%;'>$message</table>";
	$message1 = "<table style='width: 100%;'>$message1</table>";
	$headers  = "MIME-Version: 1.0" . PHP_EOL .
	            "Content-Type: text/html; charset=utf-8" . PHP_EOL .
	            'From: ' . ___adopt( $project_name ) . ' <order@' . $_SERVER['HTTP_HOST'] . '>' . PHP_EOL .
	            'Reply-To: ' . $admin_email . PHP_EOL;

	wp_mail( $admin_email, ___adopt( $form_subject ), $message, $headers );
	wp_mail( $email, ___adopt( $form_subject ), $message1, $headers );
}

function send_mail( $email, $text, $subject = '', $prefix = 'notification' ) {
	$project_name = get_bloginfo( 'name' );
	$admin_email  = get_bloginfo( 'admin_email' );
	$form_subject = $subject ?: $project_name;
	$headers      = "MIME-Version: 1.0" . PHP_EOL .
	                "Content-Type: text/html; charset=utf-8" . PHP_EOL .
	                'From: ' . ___adopt( $project_name ) . ' <' . $prefix . '@' . $_SERVER['HTTP_HOST'] . '>' . PHP_EOL .
	                'Reply-To: ' . $admin_email . PHP_EOL;

	wp_mail( $email, ___adopt( $form_subject ), $text, $headers );
}

function custom_woocommerce_ajax_add_to_cart() {
	$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
	$quantity          = empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( $_POST['quantity'] );
	$variation_id      = absint( $_POST['variation_id'] );
	$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
	$product_status    = get_post_status( $product_id );

	if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity, $variation_id ) && 'publish' === $product_status ) {
		do_action( 'woocommerce_ajax_added_to_cart', $product_id );

		if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
			wc_add_to_cart_message( array( $product_id => $quantity ), true );
		}

		WC_AJAX::get_refreshed_fragments();
	} else {
		$data = array(
			'error'       => true,
			'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id )
		);

		echo wp_send_json( $data );
	}

	wp_die();
}

add_action( 'wp_ajax_woocommerce_ajax_add_to_cart', 'custom_woocommerce_ajax_add_to_cart' );
add_action( 'wp_ajax_nopriv_woocommerce_ajax_add_to_cart', 'custom_woocommerce_ajax_add_to_cart' );

add_action( 'wp_ajax_nopriv_login_user', 'login_user' );
add_action( 'wp_ajax_login_user', 'login_user' );
function login_user() {
	$res     = array();
	$user_id = get_current_user_id();
	if ( isset( $_POST['login_user_nonce'] ) && wp_verify_nonce( $_POST['login_user_nonce'], 'login_user' ) && ! $user_id ) {
		$password = $_POST['password'] ?? '';
		$email    = $_POST['email'] ?? '';
		if ( $email && $password ) {
			$user = wp_signon( array(
				'user_login'    => $email,
				'user_password' => $password,
				'remember'      => true,
			) );
			if ( is_wp_error( $user ) ) {
				$res['type'] = 'error';
				if ( $user_id = email_exists( $email ) ) {
					$res['msg'] = _l( 'Invalid password', 1 );
				} else {
					$res['msg'] = _l( 'Invalid email', 1 );
				}
			} else {
				$user_id        = email_exists( $email );
				$res['type']    = 'success';
				$res['user_id'] = $user_id;
				$res['url']     = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
			}
		} else {
			$res['type'] = 'error';
			$res['msg']  = _l( 'Fill in the fields to login', 1 );
		}

	} else {
		$res['type'] = 'error';
		$res['msg']  = _l( 'Error', 1 );
	}
	echo json_encode( $res, JSON_UNESCAPED_UNICODE );
	die();
}

add_action( 'wp_ajax_nopriv_reset__password', 'reset__password' );
add_action( 'wp_ajax_reset__password', 'reset__password' );
function reset__password() {
	$res = array();
	if ( isset( $_POST['reset__password_nonce'] ) && wp_verify_nonce( $_POST['reset__password_nonce'], 'reset__password' ) ) {
		$email = $_POST['email'] ?? '';
		if ( $email ) {
			if ( $user_id = email_exists( $email ) ) {
				$res['user_id']  = $user_id;
				$res['email']    = $email;
				$res['url']      = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
				$res['msg']      = _l( 'Check the mailbox', 1 );
				$reset_link_text = _l( 'Reset your password', 1 );
				$l               = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
				$reset_link      = $l . '?action=reset&email=' . $email;
				carbon_set_user_meta( $user_id, 'user_reset_key', md5( $user_id . $email ) );
				$reset_link = "<a href='$reset_link' target='_blank'>$l</a>";
				$message    = "";
				$c          = true;
				$message    .= "
			" . ( ( $c = ! $c ) ? '<tr>' : '<tr style="background-color: #f8f8f8;">' ) . "
			<td style='padding: 10px; border: #e9e9e9 1px solid;'><b>$reset_link_text</b></td>
			<td style='padding: 10px; border: #e9e9e9 1px solid;'>$reset_link</td>
		</tr>
		";
				$message    = "<table style='width: 100%;'>$message</table>";
				send_mail( $email, $message, _l( 'Set a new password', 1 ) );
			} else {
				$res['msg'] = _l( 'Invalid email', 1 );
			}
		} else {
			$res['type'] = 'error';
			$res['msg']  = _l( 'Fill in the fields', 1 );
		}
	} else {
		$res['type'] = 'error';
		$res['msg']  = _l( 'Error', 1 );
	}
	echo json_encode( $res, JSON_UNESCAPED_UNICODE );

	if ( is_user_logged_in() ) {
		wp_logout();
	}
	die();
}

add_action( 'wp_ajax_nopriv_set_new_password', 'set_new_password' );
add_action( 'wp_ajax_set_new_password', 'set_new_password' );
function set_new_password() {
	$res = array();
	if ( isset( $_POST['set_new_password_nonce'] ) && wp_verify_nonce( $_POST['set_new_password_nonce'], 'set_new_password' ) ) {
		$email           = $_POST['email'] ?? '';
		$password        = $_POST['password'] ?? '';
		$repeat_password = $_POST['repeat_password'] ?? '';
		if ( $email ) {
			if ( $user_id = email_exists( $email ) ) {
				if ( $password == $repeat_password ) {
					$result = validate_password( $password );
					if ( $result === true ) {
						wp_set_password( $password, $user_id );
						$res['type'] = 'success';
						$res['url']  = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
						$res['msg']  = _l( 'The password has been changed', 1 );
						carbon_set_user_meta( $user_id, 'user_reset_key', '' );
					} else {
						$res['msg'] = $result;
					}
				} else {
					$res['msg'] = _l( 'Passwords do not match', 1 );
				}
				if ( is_user_logged_in() ) {
					wp_logout();
				}
			} else {
				$res['msg'] = _l( 'Invalid email', 1 );
			}
		} else {
			$res['type'] = 'error';
			$res['msg']  = _l( 'Fill in the fields', 1 );
		}
	} else {
		$res['type'] = 'error';
		$res['msg']  = _l( 'Error', 1 );
	}
	echo json_encode( $res, JSON_UNESCAPED_UNICODE );

	die();
}

add_action( 'wp_ajax_nopriv_create_new_user', 'create_new_user' );
add_action( 'wp_ajax_create_new_user', 'create_new_user' );
function create_new_user() {
	$res = array();
	if ( isset( $_POST['create_new_user_nonce'] ) && wp_verify_nonce( $_POST['create_new_user_nonce'], 'create_new_user' ) ) {
		$email           = $_POST['email'] ?? '';
		$password        = $_POST['password'] ?? '';
		$repeat_password = $_POST['repeat_password'] ?? '';
		$name            = $_POST['user_name'] ?? '';
		if ( $email && $name && $password ) {
			if ( $user_id = email_exists( $email ) ) {
				$res['msg'] = _l( 'Invalid email', 1 );
			} else {
				if ( $password == $repeat_password ) {
					$result = validate_password( $password );
					if ( $result === true ) {
						$_user_id = wp_create_user( $email, $password, $email );
						wp_update_user(
							array(
								'ID'         => $_user_id,
								'first_name' => $name
							)
						);
						$res['type'] = 'success';
						$res['url']  = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
						$res['msg']  = _l( 'You have successfully created an account', 1 );
					} else {
						$res['msg'] = $result;
					}
				} else {
					$res['msg'] = _l( 'Passwords do not match', 1 );
				}
			}
		} else {
			$res['type'] = 'error';
			$res['msg']  = _l( 'Fill in the fields ', 1 );
		}
	} else {
		$res['type'] = 'error';
		$res['msg']  = _l( 'Error', 1 );
	}
	echo json_encode( $res, JSON_UNESCAPED_UNICODE );

	die();
}

add_action( 'wp_ajax_nopriv_change_user_data', 'change_user_data' );
add_action( 'wp_ajax_change_user_data', 'change_user_data' );
function change_user_data() {
	$res         = array();
	$result      = array();
	$change_data = array();
	$user_id     = get_current_user_id();
	if ( isset( $_POST['change_user_data_nonce'] ) && wp_verify_nonce( $_POST['change_user_data_nonce'], 'change_user_data' ) && $user_id ) {
		$first_name   = $_POST['first_name'] ?? '';
		$last_name    = $_POST['last_name'] ?? '';
		$email        = $_POST['email'] ?? '';
		$phone_number = $_POST['phone_number'] ?? '';
		$country      = $_POST['country'] ?? '';
		$city         = $_POST['city'] ?? '';
		$postcode     = $_POST['postcode'] ?? '';
		$address      = $_POST['address'] ?? '';
		if ( $email && $first_name && $last_name && $address && $phone_number && $country && $city && $postcode ) {
			$user               = get_user_by( 'ID', $user_id );
			$_first_name        = $user->first_name;
			$_last_name         = $user->last_name ?? '';
			$_user_email        = $user->user_email ?? '';
			$_billing_phone     = get_user_meta( $user_id, 'billing_phone', true );
			$_billing_country   = get_user_meta( $user_id, 'billing_country', true );
			$_billing_postcode  = get_user_meta( $user_id, 'billing_postcode', true );
			$_billing_city      = get_user_meta( $user_id, 'billing_city', true );
			$_billing_address_1 = get_user_meta( $user_id, 'billing_address_1', true );
			$args               = array(
				'ID' => $user_id,
			);
			if ( $_first_name != $first_name ) {
				$args['first_name'] = $first_name;
				wp_update_user( $args );
				$result[]                  = _l( 'The name has been changed', 1 );
				$change_data['first_name'] = $first_name;
				update_user_meta( $user_id, 'billing_first_name', sanitize_text_field( $first_name ) );
				update_user_meta( $user_id, 'shipping_first_name', sanitize_text_field( $first_name ) );
			}
			if ( $last_name != $_last_name ) {
				$change_data['last_name'] = $last_name;
				$args['last_name']        = $last_name;
				wp_update_user( $args );
				$result[] = _l( 'The last name has been changed', 1 );
				update_user_meta( $user_id, 'billing_last_name', sanitize_text_field( $last_name ) );
				update_user_meta( $user_id, 'shipping_last_name', sanitize_text_field( $last_name ) );
			}
			if ( $email != $_user_email ) {
				if ( email_exists( $email ) ) {
					$result[] = _l( 'Email is already busy', 1 );
				} else {
					$result[]           = _l( 'Email has been changed', 1 );
					$args['user_email'] = $email;
					wp_update_user( $args );
					$change_data['email'] = $email;
				}
			}
			if ( $phone_number != $_billing_phone ) {
				update_user_meta( $user_id, 'billing_phone', sanitize_text_field( $phone_number ) );
				update_user_meta( $user_id, 'shipping_phone', sanitize_text_field( $phone_number ) );
				$result[]                    = _l( 'Changed phone number for billing and shipping', 1 );
				$change_data['phone_number'] = $phone_number;
			}
			if ( $country != $_billing_country ) {
				update_user_meta( $user_id, 'billing_country', sanitize_text_field( $country ) );
				update_user_meta( $user_id, 'shipping_country', sanitize_text_field( $country ) );
				$result[]               = _l( 'Changed country for billing and shipping', 1 );
				$change_data['country'] = $country;
			}
			if ( $postcode != $_billing_postcode ) {
				update_user_meta( $user_id, 'billing_postcode', sanitize_text_field( $postcode ) );
				update_user_meta( $user_id, 'shipping_postcode', sanitize_text_field( $postcode ) );
				$result[]                = _l( 'Changed postcode for billing and shipping', 1 );
				$change_data['postcode'] = $postcode;
			}
			if ( $city != $_billing_city ) {
				update_user_meta( $user_id, 'billing_city', sanitize_text_field( $city ) );
				update_user_meta( $user_id, 'shipping_city', sanitize_text_field( $city ) );
				$result[]                    = _l( 'Changed city for billing and shipping', 1 );
				$change_data['billing_city'] = $city;
			}
			if ( $address != $_billing_address_1 ) {
				update_user_meta( $user_id, 'billing_address_1', sanitize_text_field( $address ) );
				update_user_meta( $user_id, 'shipping_address_1', sanitize_text_field( $address ) );
				$result[]               = _l( 'Changed address for billing and shipping', 1 );
				$change_data['address'] = $city;
			}
			$res['msg']         = implode( ', ', $result );
			$res['change_data'] = $change_data;
			$res['type']        = 'success';
		} else {
			$res['type'] = 'error';
			$res['msg']  = _l( 'Fill in the fields ', 1 );
		}
	} else {
		$res['type'] = 'error';
		$res['msg']  = _l( 'Error', 1 );
	}
	echo json_encode( $res, JSON_UNESCAPED_UNICODE );
	die();
}

add_action( 'wp_ajax_nopriv_change_company_data', 'change_company_data' );
add_action( 'wp_ajax_change_company_data', 'change_company_data' );
function change_company_data() {
	$res         = array();
	$result      = array();
	$change_data = array();
	$user_id     = get_current_user_id();
	if ( isset( $_POST['change_company_data_nonce'] ) && wp_verify_nonce( $_POST['change_company_data_nonce'], 'change_company_data' ) && $user_id ) {
		$_billing_company      = get_user_meta( $user_id, 'billing_company', true );
		$_company_address      = carbon_get_user_meta( $user_id, 'company_address' );
		$_company_contact_name = carbon_get_user_meta( $user_id, 'company_contact_name' );
		$_company_city         = carbon_get_user_meta( $user_id, 'company_city' );
		$_company_number       = carbon_get_user_meta( $user_id, 'company_number' );
		$_company_postcode     = carbon_get_user_meta( $user_id, 'company_postcode' );
		$_company_email        = carbon_get_user_meta( $user_id, 'company_email' );
		$_company_country      = carbon_get_user_meta( $user_id, 'company_country' );
		$_company_phone_number = carbon_get_user_meta( $user_id, 'company_phone_number' );
		$company_name          = $_POST['company_name'] ?? '';
		$company_address       = $_POST['company_address'] ?? '';
		$company_contact_name  = $_POST['company_contact_name'] ?? '';
		$company_city          = $_POST['company_city'] ?? '';
		$company_number        = $_POST['company_number'] ?? '';
		$company_postcode      = $_POST['company_postcode'] ?? '';
		$company_email         = $_POST['company_email'] ?? '';
		$company_country       = $_POST['company_country'] ?? '';
		$company_phone_number  = $_POST['company_phone_number'] ?? '';
		if ( $company_name && $company_name != $_billing_company ) {
			update_user_meta( $user_id, 'billing_company', sanitize_text_field( $company_name ) );
			update_user_meta( $user_id, 'shipping_company', sanitize_text_field( $company_name ) );
			$result[] = _l( 'Changed company name', 1 );
		}
		if ( $company_address && $company_address != $_company_address ) {
			carbon_set_user_meta( $user_id, 'company_address', sanitize_text_field( $company_address ) );
			$result[] = _l( 'Changed company address', 1 );
		}
		if ( $company_contact_name && $company_contact_name != $_company_contact_name ) {
			carbon_set_user_meta( $user_id, 'company_contact_name', sanitize_text_field( $company_contact_name ) );
			$result[] = _l( 'Changed company contact name', 1 );
		}
		if ( $company_city && $company_city != $_company_city ) {
			carbon_set_user_meta( $user_id, 'company_city', sanitize_text_field( $company_city ) );
			$result[] = _l( 'Changed company city', 1 );
		}
		if ( $company_number && $company_number != $_company_number ) {
			carbon_set_user_meta( $user_id, 'company_number', sanitize_text_field( $company_number ) );
			$result[] = _l( 'Changed company number', 1 );
		}
		if ( $company_postcode && $company_postcode != $_company_postcode ) {
			carbon_set_user_meta( $user_id, 'company_postcode', sanitize_text_field( $company_postcode ) );
			$result[] = _l( 'Changed company postcode', 1 );
		}
		if ( $company_email && $company_email != $_company_email ) {
			carbon_set_user_meta( $user_id, 'company_email', sanitize_text_field( $company_email ) );
			$result[] = _l( 'Changed company email', 1 );
		}
		if ( $company_country && $company_country != $_company_country ) {
			carbon_set_user_meta( $user_id, 'company_country', sanitize_text_field( $company_country ) );
			$result[] = _l( 'Changed company country', 1 );
		}
		if ( $company_phone_number && $company_phone_number != $_company_phone_number ) {
			carbon_set_user_meta( $user_id, 'company_phone_number', sanitize_text_field( $company_phone_number ) );
			$result[] = _l( 'Changed company phone number', 1 );
		}
		$res['msg']         = implode( ', ', $result );
		$res['change_data'] = $change_data;
		$res['type']        = 'success';
	} else {
		$res['type'] = 'error';
		$res['msg']  = _l( 'Error', 1 );
	}
	echo json_encode( $res, JSON_UNESCAPED_UNICODE );
	die();
}
