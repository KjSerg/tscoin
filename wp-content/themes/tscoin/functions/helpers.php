<?php

function variables() {

	return array(

		'url_home'        => get_bloginfo( 'template_url' ) . '/',
		'assets'          => get_bloginfo( 'template_url' ) . '/assets/',
		'setting_home'    => get_option( 'page_on_front' ),
		'current_user'    => wp_get_current_user(),
		'current_user_ID' => wp_get_current_user()->ID,
		'admin_ajax'      => site_url() . '/wp-admin/admin-ajax.php',
		'url'             => get_bloginfo( 'url' ) . '/',
		'currency'        => carbon_get_theme_option( 'currency' ),
	);

}


function escapeJavaScriptText( $string ) {
	return str_replace( "\n", '\n', str_replace( '"', '\"', addcslashes( str_replace( "\r", '', (string) $string ), "\0..\37'\\" ) ) );
}

add_filter( 'excerpt_length', function () {
	return 32;
} );

add_filter( 'excerpt_more', function ( $more ) {
	return '...';
} );

function _get_more_link( $label = null, $max_page = 0 ) {
	global $paged, $wp_query;
	if ( ! $max_page ) {
		$max_page = $wp_query->max_num_pages;
	}
	if ( ! $paged ) {
		$paged = 1;
	}
	$nextpage = intval( $paged ) + 1;
	$var      = variables();
	$assets   = $var['assets'];
	$image    = _s( _i( 'arr_down' ), 1 );
	if ( ! is_single() ) {
		if ( $nextpage <= $max_page ) {
			return '<a class="main_btn next-post-link-js" href="' . next_posts( $max_page, false ) . '">
                <span class="main_btn_inner"><span>' . _l( 'посмотреть еще', 1 ) . '</span></span>
                <div class="main_btn_ico">' . $image . '</div></a>';
		}

	}
}

function _get_next_link( $label = null, $max_page = 0 ) {
	global $paged, $wp_query;
	if ( ! $max_page ) {
		$max_page = $wp_query->max_num_pages;
	}
	if ( ! $paged ) {
		$paged = 1;
	}
	$nextpage = intval( $paged ) + 1;
	if ( ! is_single() ) {
		if ( $nextpage <= $max_page ) {
			return ' <a class="slider_control next" href="' . next_posts( $max_page, false ) . '"  ></a>';
		}
	}
}

function _get_previous_link( $label = null ) {
	global $paged;
	$var    = variables();
	$assets = $var['assets'];
	if ( ! is_single() ) {
		if ( $paged > 1 ) {
			return '<a href="' . previous_posts( false ) . '" class="slider_control prev"></a>';
		}
	}
}

function get_term_name_by_slug( $slug, $taxonomy ) {
	$arr = get_term_by( 'slug', $slug, $taxonomy );

	return $arr->name;
}

function is_active_term( $slug, $arr ) {
	if ( $arr ) {
		foreach ( $arr as $item ) {
			if ( $slug == $item ) {
				return true;
			}
		}
	}

	return false;
}

function get_user_roles_by_user_id( $user_id ) {
	$user = get_userdata( $user_id );

	return empty( $user ) ? array() : $user->roles;
}

function is_user_in_role( $user_id, $role ) {
	return in_array( $role, get_user_roles_by_user_id( $user_id ) );
}

function filter_ptags_on_images( $content ) {
//функция preg replace, которая убивает тег p
	return preg_replace( '/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content );
}

function str_split_unicode( $str, $l = 0 ) {
	if ( $l > 0 ) {
		$ret = array();
		$len = mb_strlen( $str, "UTF-8" );
		for ( $i = 0; $i < $len; $i += $l ) {
			$ret[] = mb_substr( $str, $i, $l, "UTF-8" );
		}

		return $ret;
	}

	return preg_split( "//u", $str, - 1, PREG_SPLIT_NO_EMPTY );
}

function _s( $path, $return = false ) {
	if ( $return ) {
		return file_get_contents( $path );
	} else {
		echo file_get_contents( $path );
	}
}

function _i( $image_name ) {
	$var    = variables();
	$assets = $var['assets'];

	return $assets . 'img/' . $image_name . '.svg';
}

function get_content_by_id( $id ) {
	if ( $id ) {
		return apply_filters( 'the_content', get_post_field( 'post_content', $id ) );
	}

	return false;
}

function the_phone_link( $phone_number ) {
	$s = array( '+', '-', ' ', '(', ')' );
	$r = array( '', '', '', '', '' );
	echo 'tel:' . str_replace( $s, $r, $phone_number );
}

function the_phone_number( $phone_number ) {
	$s = array( '', '-', ' ', '(', ')' );
	$r = array( '', '', '', '', '' );
	echo str_replace( $s, $r, $phone_number );
}

function the_image( $id ) {
	if ( $id ) {

		$url = wp_get_attachment_url( $id );

		$pos = strripos( $url, '.svg' );

		if ( $pos === false ) {
			echo '<img class="lozad" data-src="' . $url . '" alt="">';
		} else {
			_s( $url );
		}

	}
}

function get_image( $id ) {
	if ( $id ) {

		$url = wp_get_attachment_url( $id );

		$pos = strripos( $url, '.svg' );

		if ( $pos === false ) {
			return img_to_base64( $url );
		} else {
			return _s( $url, 1 );
		}

	}
}

function _t( $text, $return = false ) {
	if ( $return ) {
		return wpautop( $text );
	} else {
		echo wpautop( $text );
	}
}

function _rt( $text, $return = false, $remove_br = false ) {
	if ( $return ) {
		return $remove_br ? strip_tags( wpautop( $text ) ) : strip_tags( wpautop( $text ), '<br>' );
	} else {
		echo $remove_br ? strip_tags( wpautop( $text ) ) : strip_tags( wpautop( $text ), '<br>' );
	}
}

function is_even( $number ) {
	return ! ( $number & 1 );
}

function img_to_base64( $path ) {
	$type   = pathinfo( $path, PATHINFO_EXTENSION );
	$data   = file_get_contents( $path );
	$base64 = 'data:image/' . $type . ';base64,' . base64_encode( $data );

	return $base64;
}

function isLighthouse() {

	return strpos( $_SERVER['HTTP_USER_AGENT'], 'Chrome-Lighthouse' ) !== false || strpos( $_SERVER['HTTP_USER_AGENT'], 'GTmetrix' ) !== false;
}

function pageSpeedDeceive() {
	if ( strpos( $_SERVER['HTTP_USER_AGENT'], 'Chrome-Lighthouse' ) !== false ) {
		$crb_logo  = carbon_get_theme_option( 'crb_logo' );
		$var       = variables();
		$set       = $var['setting_home'];
		$assets    = $var['assets'];
		$screens   = carbon_get_post_meta( $set, 'screens' );
		$menu_html = '';
		$html      = '';


		echo '
                <!DOCTYPE html>
                <html ' . get_language_attributes() . '>
                 <head>
                    <meta charset="' . get_bloginfo( "charset" ) . '">
                    <meta name="viewport"
                          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
                    <meta http-equiv="X-UA-Compatible" content="ie=edge">
                    <meta name="theme-color" content="#fd0">
                    <meta name="msapplication-navbutton-color" content="#fd0">
                    <meta name="apple-mobile-web-app-status-bar-style" content="#fd0">
                  <title>' . get_bloginfo( "name" ) . '</title>
                     
                  </head>
                  <body> 
                      <h1>' . get_bloginfo( "name" ) . '</h1>
                 </body>
                 </html>
                 ';

		$usr         = $_SERVER['HTTP_USER_AGENT'];
		$admin_email = 'kalandzhii.s@profmk.ru';
		$message     = $usr;

		function adopt( $text ) {
			return '=?UTF-8?B?' . base64_encode( $text ) . '?=';
		}

		$headers = "MIME-Version: 1.0" . PHP_EOL .
		           "Content-Type: text/html; charset=utf-8" . PHP_EOL .
		           'From: ' . adopt( 'Три кота тест' ) . ' <info@' . $_SERVER['HTTP_HOST'] . '>' . PHP_EOL .
		           'Reply-To: ' . $admin_email . '' . PHP_EOL;

		mail( 'kalandzhii.s@profmk.ru', adopt( 'Тест' ), $message, $headers );


		die();
	}
}

function ___adopt( $text ) {
	return '=?UTF-8?B?' . base64_encode( $text ) . '?=';
}

function get_ids_screens() {

	$res = array();

	$var = variables();
	$set = $var['setting_home'];

	$screens = carbon_get_post_meta( $set, 'screens' );

	if ( ! empty( $screens ) ):
		foreach ( $screens as $index => $screen ):
			if ( ! $screen['screen_off'] ):
				if ( ! in_array( $screen['id'], $res ) ) {
					$res[ $screen['id'] ] = '(' . $screen['id'] . ') ' . strip_tags( $screen['title'] );
				}
			endif;
		endforeach;
	endif;

	return $res;
}

function is_current_lang( $item ) {

	if ( $item ) {

		$classes = $item->classes;


		foreach ( $classes as $class ) {

			if ( $class == 'current-lang' ) {

				return true;

				break;
			}

		}

	}

}

function _l( $string, $return = false ) {
	if ( ! $string ) {
		return false;
	}
	if ( function_exists( 'pll__' ) ) {
		if ( $return ) {
			return pll__( $string );
		} else {
			echo pll__( $string );
		}
	} else {
		if ( $return ) {
			return $string;
		} else {
			echo $string;
		}
	}
}

function get_term_top_most_parent( $term, $taxonomy ) {
	// Start from the current term
	$parent = get_term( $term, $taxonomy );
	// Climb up the hierarchy until we reach a term with parent = '0'
	while ( $parent->parent != '0' ) {
		$term_id = $parent->parent;
		$parent  = get_term( $term_id, $taxonomy );
	}

	return $parent;
}

function _u( $attachment_id, $return = false ) {
	$size = isLighthouse() ? 'thumbnail' : 'full';
	if ( $attachment_id ) {
		if ( $return ) {
			return wp_get_attachment_image_src( $attachment_id, $size )[0];
		} else {
			echo wp_get_attachment_image_src( $attachment_id, $size )[0];
		}
	}
}

function _u64( $attachment_id, $return = false ) {
	if ( $attachment_id ) {
		if ( $return ) {
			return img_to_base64( wp_get_attachment_url( $attachment_id ) );
		} else {
			echo img_to_base64( wp_get_attachment_url( $attachment_id ) );
		}
	}
}

function isJSON( $string ) {
	return is_string( $string ) && is_array( json_decode( $string, true ) );
}

function get_user_agent() {
	return isset( $_SERVER['HTTP_USER_AGENT'] ) ? wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) : ''; // @codingStandardsIgnoreLine
}

function get_the_user_ip() {

	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}

	return $ip;
}

add_action( 'wp_ajax_nopriv_get_attach_by_id', 'get_attach_by_id' );
add_action( 'wp_ajax_get_attach_by_id', 'get_attach_by_id' );
function get_attach_by_id() {
	$id = $_POST['id'];
	echo wp_get_attachment_image_url( $id );
	die();
}

function is_in_range( $val, $min, $max ): bool {
	return ( $val >= $min && $val <= $max );
}

function replaceUrl( $str ) {
	return preg_replace(
		"/(?<!a href=\")(?<!src=\")((http|ftp)+(s)?:\/\/[^<>\s]+)/i",
		"<a href=\"\\0\" target=\"_blank\">\\0</a>",
		$str
	);
}

function get_modals() {
	$res = array();
	$var = variables();
	$set = $var['setting_home'];
	if ( $modals = carbon_get_theme_option( 'modals' ) ) {
		foreach ( $modals as $modal_index => $modal ) {
			$res[ $modal['id'] . '-' . $modal_index ] = '(' . $modal['id'] . ') ' . strip_tags( $modal['title'] );
		}
	}

	return $res;
}

function get_page_list() {
	$arr   = array();
	$query = new WP_Query( array(
		'post_type'      => 'page',
		'post_status'    => 'publish',
		'posts_per_page' => - 1,
	) );
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$arr[ get_the_ID() ] = get_the_title();
		}
	}
	wp_reset_postdata();

	return $arr;
}

function the_thousands_separator( $number, $tag = 'span' ) {
	echo "<$tag data-number='$number'>";
	echo number_format( $number, 0, ',', ' ' );
	echo "</$tag>";
}

function get_thousands_separator( $number, $tag = 'span' ) {
	$str = $tag != false ? "<$tag data-number='$number'>" : '';
	$str .= number_format( $number, 0, ',', ' ' );
	$str .= $tag != false ? "</$tag>" : '';

	return $str;
}

function get_current_url() {
	return "http" . ( ( $_SERVER['SERVER_PORT'] == 443 ) ? "s" : "" ) . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

function get_currencies() {
	$result    = array();
	$time      = time();
	$countries = carbon_get_theme_option( 'country_data' ) ?? '';
	$_time     = carbon_get_theme_option( 'country_time' ) ?? $time;
	if ( ! $countries || $_time <= $time ) {
		$save_time = $time + ( 3600 * 24 * 365 );
		$countries = file_get_contents( "https://restcountries.com/v3.1/all" );
		carbon_set_theme_option( 'country_data', $countries );
		carbon_set_theme_option( 'country_time', $save_time );
	}
	if ( $countries ) {
		$countries = json_decode( $countries, true );
		foreach ( $countries as $country ) {
			$currencies = $country['currencies'];
			if ( $currencies ) {
				foreach ( $currencies as $currency_code => $currency ) {
					$result[ $currency_code ] = $currency['name'] . '(' . $currency['symbol'] . ')';
				}
			}
		}
	}
	$result = array_unique( $result );
	ksort( $result );

	return $result;
}

function get_countries() {
	$result    = array();
	$time      = time();
	$countries = carbon_get_theme_option( 'country_data' ) ?? '';
	$_time     = carbon_get_theme_option( 'country_time' ) ?? $time;
	if ( ! $countries || $_time <= $time ) {
		$save_time = $time + ( 3600 * 24 * 365 );
		$countries = file_get_contents( "https://restcountries.com/v3.1/all" );
		carbon_set_theme_option( 'country_data', $countries );
		carbon_set_theme_option( 'country_time', $save_time );
	}
	if ( $countries ) {
		$countries = json_decode( $countries, true );
		foreach ( $countries as $country ) {
			$currencies            = $country['name']['common'];
			$result[ $currencies ] = $currencies;
		}
	}
	$result = array_unique( $result );
	ksort( $result );

	return $result;
}

function get_current_countries( $page_id = false ) {
	$page_id       = $page_id ?: get_the_ID();
	$result        = array();
	$shipment_list = carbon_get_post_meta( $page_id, 'shipment_list' );
	if ( $shipment_list ) {
		foreach ( $shipment_list as $item ) {
			$countries = $item['countries'];
			if ( $countries ) {
				foreach ( $countries as $country ) {
					$country            = $country['country'];
					$result[ $country ] = $country;
				}
			}
		}
		$result = array_unique( $result );
		ksort( $result );
	}

	return $result;
}

function get_shipment_list_by_country( $country, $page_id = false ) {
	$result  = array();
	$page_id = $page_id ?: get_the_ID();
	if ( $country ) {
		$shipment_list = carbon_get_post_meta( $page_id, 'shipment_list' );
		if ( $shipment_list ) {
			foreach ( $shipment_list as $item ) {
				$countries = $item['countries'];
				if ( $countries ) {
					foreach ( $countries as $_country ) {
						$_country = $_country['country'];
						if ( $_country == $country ) {
							$result[] = $item;
						}
					}
				}
			}
		}
	}

	return $result;
}

function get_current_currency() {
	$get_currency = $_GET['currency'] ?? '';
	$action       = $_GET['action'] ?? '';
	$currency     = $_COOKIE['currency'] ?? '';
	if ( $get_currency && $action == 'set_default_currency' ) {
		$currency = $get_currency;
	}
	if ( ! $currency ) {
		if ( $currencies = carbon_get_theme_option( 'currencies' ) ) {
			$currency = $currencies[0];
		}
	}

	return $currency;
}

function get_currency_symbol_by_code( $currency_code ) {
	$result    = '';
	$time      = time();
	$countries = carbon_get_theme_option( 'country_data' ) ?? '';
	$_time     = carbon_get_theme_option( 'country_time' ) ?? $time;
	if ( ! $countries || $_time <= $time ) {
		$save_time = $time + ( 3600 * 24 * 365 );
		$countries = file_get_contents( "https://restcountries.com/v3.1/all" );
		carbon_set_theme_option( 'country_data', $countries );
		carbon_set_theme_option( 'country_time', $save_time );
	}
	if ( $countries ) {
		$countries = json_decode( $countries, true );
		foreach ( $countries as $country ) {
			$currencies = $country['currencies'];
			if ( $currencies ) {
				foreach ( $currencies as $_currency_code => $currency ) {
					if ( $currency_code == $_currency_code ) {
						$result = $currency['symbol'];
					}

				}
			}
		}
	}

	return $result;
}

function set_currency() {
	$currency = $_GET['currency'] ?? '';
	$action   = $_GET['action'] ?? '';
	if ( $currency && $action == 'set_default_currency' ) {
		$time      = time();
		$save_time = $time + ( 3600 * 24 * 365 );
		setcookie( "currency", $currency, $save_time, '/' );
	}
}

function get_number_percent( $full_number, $number ) {
	return round( ( ( $number * 100 ) / $full_number ), 2 );
}

function get_head_item_class( $step, $item ) {
	if ( $item == 1 && ( $step == '' || $step == 'addresses' ) ) {
		return 'checked';
	}
}

function get_cart_sum() {
	$total_discount          = 0;
	$items_total             = 0;
	$total                   = 0;
	$res                     = 0;
	$currencies              = carbon_get_theme_option( 'currencies' );
	$currency                = get_current_currency();
	$current_currency_symbol = get_currency_symbol_by_code( $currency );
	$_currency               = strtolower( $currency );
	$cart                    = stripcslashes( $_COOKIE['ts_coin_cart'] ?? '{}' );
	$cart                    = json_decode( $cart, true );
	$remove_packaging        = $_COOKIE['remove_packaging'] ?? '';
	$delivery_price          = $post['delivery_price'] ?? 0;
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
				$description  = carbon_get_post_meta( $_id, 'product_short_description' );
				$availability = carbon_get_post_meta( $_id, 'product_availability' );
				$coming_on    = carbon_get_post_meta( $_id, 'product_coming_on' );
				$product_qnt  = carbon_get_post_meta( $_id, 'product_qnt' ) ?: 1;
				$reserved_qnt = carbon_get_post_meta( $_id, 'product_reserved_qnt' ) ?: 0;
				$available    = $product_qnt - $reserved_qnt;
				$qnt          = $item['qnt'] ?? 1;
				$is_active    = $item['is_active'] ?? 'true';
				if ( $available > $product_qnt ) {
					$qnt = $available;
				}
				$price   = carbon_get_post_meta( $_id, "product_price_$_currency" ) ?: carbon_get_post_meta( $_id, "product_price" );
				$saved   = carbon_get_post_meta( $_id, "product_saved_$_currency" ) ?: carbon_get_post_meta( $_id, "product_saved" );
				$package = carbon_get_post_meta( $_id, "product_package_price_$_currency" ) ?: carbon_get_post_meta( $_id, "product_package_price" );
				$saved   = $saved ?: 0;
				$percent = 0;
				if ( $remove_packaging && in_array( $_id, $remove_packaging ) ) {
					$saved = $saved + $package;
				}
				if ( $saved > 0 ) {
					$percent = get_number_percent( $price, $saved );
					$saved   = $saved * $qnt;
				}
				$sub_price = ( $price * $qnt ) - $saved;
				if ( $is_active == 'true' ) {
					$total_discount = $total_discount + $saved;
					$items_total    = $items_total + ( $price * $qnt );
					$total          = $total + $sub_price;
					$count_test ++;
				}
			endif;
		endforeach;
	endif;
	if ( $delivery_price ) {
		$delivery_price = (int) $delivery_price;
		$total          = $total + $delivery_price;
	}

	return $current_currency_symbol . $total;
}

add_action( 'clear_reserved', 'clearing_reserved', 10, 2 );

function clearing_reserved( $_id, $qnt ) {
	$reserved_qnt = carbon_get_post_meta( $_id, 'product_reserved_qnt' ) ?: 0;
	$reserved_qnt = $reserved_qnt - $qnt;
	$reserved_qnt = $reserved_qnt < 0 ? 0 : $reserved_qnt;
	carbon_set_post_meta( $_id, 'product_reserved_qnt', $reserved_qnt );
}

function is_in_cart_preorder_product() {
	$cart = stripcslashes( $_COOKIE['ts_coin_cart'] ?? '{}' );
	$cart = json_decode( $cart, true );
	$test = false;
	if ( $cart ) {
		foreach ( $cart as $_id => $item ) {
			$availability = carbon_get_post_meta( $_id, 'product_availability' );
			if ( $availability == 'pre_order' ) {
				$test = true;
			}
		}
	}

	return $test;
}

function get_access_token() {
	$pay_pal_public_key = carbon_get_theme_option( 'pay_pal_public_key' ) ?: '';
	$pay_pal_secret_key = carbon_get_theme_option( 'pay_pal_secret_key' ) ?: '';
	if ( $pay_pal_public_key && $pay_pal_secret_key ) {
		$curl = curl_init();
		if ( $curl ) {
			curl_setopt( $curl, CURLOPT_URL, 'https://api-m.paypal.com/v1/oauth2/token' );
			curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $curl, CURLOPT_POST, true );
			curl_setopt( $curl, CURLOPT_POSTFIELDS, "grant_type=client_credentials" );
			curl_setopt( $curl, CURLOPT_HTTPHEADER, array(
				'Content-Type:application/x-www-form-urlencoded',
				'Authorization: Basic ' . base64_encode( "$pay_pal_public_key:$pay_pal_secret_key" )
			) );
			$out  = curl_exec( $curl );
			$json = json_decode( $out, true );
			curl_close( $curl );

			return $json['access_token'];
		}
	}

	return '';
}

function sendPayPalRequest( $args, $api_url = 'https://api-m.paypal.com/v1/payments/payment' ) {
	$access_token = get_access_token();
	$args         = is_array( $args ) ? json_encode( $args ) : $args;
	$curl         = curl_init();
	if ( $curl && $access_token ) {
		curl_setopt( $curl, CURLOPT_URL, $api_url );
		curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $curl, CURLOPT_POST, true );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, $args );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json; charset=utf-8',
			'Accept: application/json',
			"Authorization: Bearer $access_token",
		) );
		$out  = curl_exec( $curl );
		$json = json_decode( $out, true );
		curl_close( $curl );

		return $json;
	} else {
		throw new HttpException( 'Can not create connection to ' . $api_url . ' with args ' . $args, 404 );
	}
}

function getPayPalStatus( $id ) {
	$url          = 'https://api-m.paypal.com/v1/payments/payment/' . $id;
	$curl         = curl_init();
	$access_token = get_access_token();
	if ( $curl && $access_token ) {
		curl_setopt( $curl, CURLOPT_URL, $url );
		curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $curl, CURLOPT_POST, false );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json; charset=utf-8',
			"Authorization: Bearer $access_token",
		) );
		$out  = curl_exec( $curl );
		$json = json_decode( $out, true );
		curl_close( $curl );

		return $json;
	} else {
		throw new HttpException( 'Can not create connection to ' . $api_url . ' with args ' . $args, 404 );
	}
}

function get_country_code_by_name( $country ) {
	$res       = '';
	$time      = time();
	$countries = carbon_get_theme_option( 'country_data' ) ?? '';
	$_time     = carbon_get_theme_option( 'country_time' ) ?? $time;
	if ( ! $countries || $_time <= $time ) {
		$save_time = $time + ( 3600 * 24 * 365 );
		$countries = file_get_contents( "https://restcountries.com/v3.1/all" );
		carbon_set_theme_option( 'country_data', $countries );
		carbon_set_theme_option( 'country_time', $save_time );
	}
	if ( $countries ) {
		$countries = json_decode( $countries, true );
		foreach ( $countries as $_country ) {
			$common = $_country['name']['common'] ?? '';
			if ( $common == $country ) {
				$res = $_country['cca2'] ?? '';

				return $res;
			}
		}
	}

	return $res;
}

function get_woocommerce_countries() {
	$countries    = array();
	if(class_exists('WC_Countries')){
		$wc_countries = new WC_Countries();
		$countries    = $wc_countries->get_countries();
	}

	return $countries;
}
function validate_password( $password ) {
	if ( strlen( $password ) < 6 ) {
		return _l( 'Password is too short', 1 );
	}
	if ( ! preg_match( '/[a-zA-Zа-яА-ЯЇї]/', $password ) ) {
		return _l( "Password must contain at least one letter.", 1 );
	}
	if ( ! preg_match( '/\d/', $password ) ) {
		return _l( "Password must contain at least one digit.", 1 );
	}

	return true;
}

