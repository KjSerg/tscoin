<?php
add_action( 'wp_insert_post', 'schedule_product_status_update', 20, 3 );
function schedule_product_status_update( $post_id, $post, $update ) {
	if ( $post->post_type != 'product' ) {
		return;
	}
	$product_item_date = carbon_get_post_meta( $post_id, 'product_item_date' );
	$timestamp         = wp_next_scheduled( 'change_product_to_in_stock', array( $post_id ) );
	if ( $timestamp ) {
		wp_unschedule_event( $timestamp, 'change_product_to_in_stock', array( $post_id ) );
	}
	if ( $product_item_date ) {
		wp_schedule_single_event( (int) $product_item_date, 'change_product_to_in_stock', array( $post_id ) );
	}
}

add_action( 'change_product_to_in_stock', 'set_product_to_in_stock' );
function set_product_to_in_stock( $post_id ) {
	if ( $post_id ) {
		$post_id = (int) $post_id;
		if ( get_post_type( $post_id ) == 'product' ) {
			$product = wc_get_product( $post_id );
			if ( $product ) {
				$product->set_stock_status( 'instock' );
				$product->save();
			}
		}
	}
}
