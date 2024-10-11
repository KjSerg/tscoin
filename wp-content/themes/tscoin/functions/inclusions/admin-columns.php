<?php

// <orders>

function true_add_post_columns( $my_columns ) {
	$my_columns['email']  = 'Email';
	$my_columns['items']  = 'Products';
	$my_columns['amount'] = 'Sum';

	return $my_columns;
}

add_filter( 'manage_edit-orders_columns', 'true_add_post_columns', 10, 1 );

function true_fill_post_columns( $column ) {
	global $post;
	$var      = variables();
	$url      = $var['url'];
	$set      = $var['setting_home'];
	$html     = '';
	$items    = carbon_get_post_meta( $post->ID, 'order_cart' );
	$currency = carbon_get_post_meta( $post->ID, 'order_currency' );
	if ( $items ) {
		$items_count = count( $items );
		$i           = 1;
		foreach ( $items as $item ) {
			$sum      = $item['subtotal'];
			$n        = $item['title'];
			$str_name = $n . ' ' . ' (' . $item['qnt'] . ' x ' . $item['price'] . ' = ' . $sum;
			$html     .= '<a target="_blank" href="' . $url . 'wp-admin/post.php?post=' . $item['id'] . '&action=edit">' . $str_name . ')</a> <br>';
			if ( $i !== $items_count ) {
				$html .= '<hr>';
			}
			$i ++;
		}
	}
	$sum   = carbon_get_post_meta( $post->ID, 'order_total' );
	$sum   = $currency . $sum;
	$email = carbon_get_post_meta( $post->ID, 'order_email' );
	$email = '<a href="mailto:' . $email . '">' . $email . '</a>';
	switch ( $column ) {
		case 'email':
			echo $email;
			break;
		case 'items':
			echo $html;
			break;
		case 'amount':
			echo $sum;
			break;
	}
}

add_action( 'manage_posts_custom_column', 'true_fill_post_columns', 10, 1 );

// </orders>
