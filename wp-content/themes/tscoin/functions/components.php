<?php

function the_product_price( $id ) {
	$res             = '';
	$currency        = get_current_currency();
	$currency_symbol = $currency ? get_currency_symbol_by_code( $currency ) : '';
	$_currency       = strtolower( $currency );
	$price           = carbon_get_post_meta( $id, "product_price_$_currency" ) ?: carbon_get_post_meta( $id, "product_price" );
	if ( $price ) {
		$res = $currency_symbol . $price;
	}
	echo $res;
}

function the_cart_item( $_id, $item, $remove_cross ) {
	if ( get_post( $_id ) ):
		$img = get_the_post_thumbnail_url( $_id );
		$permalink = get_the_permalink( $_id );
		$description = carbon_get_post_meta( $_id, 'product_short_description' );
		$availability = carbon_get_post_meta( $_id, 'product_availability' );
		$coming_on = carbon_get_post_meta( $_id, 'product_coming_on' );
		$qnt = $item['qnt'] ?? 1;
		$wish_list = $_COOKIE['wish_list'] ?? '';
		if ( $wish_list ) {
			$wish_list = explode( ",", $wish_list );
		}
		$currency        = get_current_currency();
		$currency_symbol = $currency ? get_currency_symbol_by_code( $currency ) : '';
		$_currency       = strtolower( $currency );
		$saved           = carbon_get_post_meta( $_id, "product_saved_$_currency" ) ?: carbon_get_post_meta( $_id, "product_saved" );
		$price           = carbon_get_post_meta( $_id, "product_price_$_currency" ) ?: carbon_get_post_meta( $_id, "product_price" );
		$package         = carbon_get_post_meta( $_id, "product_package_price_$_currency" ) ?: carbon_get_post_meta( $_id, "product_package_price" );
		$saved           = $saved ?: 0;
		$percent         = 0;
		if ( in_array( $_id, $wish_list ) ) {
			$saved = $saved + $package;
		}
		if ( $saved > 0 ) {
			$percent = get_number_percent( $price, $saved );
		}
		$sub_total = $price - $saved;
		$sub_price = $sub_total * $qnt;
		?>
        <div class="cart_block" data-id="<?php echo $_id; ?>">
            <a class="cart_block_remove" data-id="<?php echo $_id; ?>" href="#">
                <span class="remove_txt"><?php _l( 'backorder' ); ?></span>
				<?php echo $remove_cross; ?>
            </a>
            <label class="ch_block cart_block_check">
                <input type="checkbox" checked="checked"/>
                <div class="ch_block_icon large"></div>
            </label>
            <a href="<?php echo $permalink; ?>"
               class="cart_block_img">
				<?php if ( $img ): ?>
                    <img src="<?php echo $img; ?>" alt="<?php echo get_the_title( $_id ); ?>"/>
				<?php endif; ?>
            </a>
            <div class="cart_block_body">
                <div class="cart_block_main_description">
                    <div class="main_title small ttu">
						<?php echo get_the_title( $_id ); ?>
                    </div>
					<?php echo _t( $description, 1 ); ?>
                </div>
                <div class="cart_block_cols">
                    <div class="cart_block_col descr_col">
                        <div class="product_state">
							<?php if ( $availability == 'on_stock' ): ?>
                                <div class="cart_block_col_title green">
									<?php _l( 'Available on stock' ); ?>
                                </div>
							<?php elseif ( $availability == 'sold_out' ): ?>
                                <div class="cart_block_col_title " style="color: #E32D2B;">
									<?php _l( 'Sold Out' ); ?>
                                </div>
							<?php elseif ( $availability == 'pre_order' ): ?>
                                <div class="cart_block_col_title " style="color: #1A73D7;">
									<?php _l( 'In Pre-Order' ); ?>
									<?php
									if ( $coming_on ) {
										echo "<br> $coming_on";
									}
									?>
                                </div>
							<?php endif; ?>
                        </div>
                        <div class="cart_block_quant">
                            <div class="product__price">
                                <strong><?php the_product_price( $_id ); ?></strong>
                            </div>
                            <div class="quantity_wrapper">
                                <input class="quantity_input" type="text"
                                       name="quantity of items"
                                       value="<?php echo $qnt; ?>"
                                       readonly=""/>
                                <a class="quant_btn plus_btn transition" href="javascript:void(0)"></a>
                                <a class="quant_btn minus_btn transition" href="javascript:void(0)"></a>
                            </div>
                        </div>
                    </div>
                    <div class="cart_block_col price_col">
                        <div class="cart_block_col_head">
                            <div class="cart_block_col_title">
								<?php _l( "YOU'VE SAVED" ); ?>
                            </div>
                            <p><?php echo $percent; ?>%</p>
                        </div>
                        <div class="product__price">
                            <strong>
								<?php echo $currency_symbol . $saved; ?>
                            </strong>
                        </div>
                    </div>
                    <div class="cart_block_col price_col">
                        <div class="cart_block_col_head">
                            <div class="cart_block_col_title">
								<?php _l( 'YOUR PRICE' ); ?>
                            </div>
                            <p>
								<?php _l( 'discount price' ); ?>
                            </p>
                        </div>
                        <div class="product__price">
                            <strong>
								<?php echo $currency_symbol . $sub_price; ?>
                            </strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	<?php
	endif;
}

function the_side_cart_item( $_id, $item ) {
	$description = carbon_get_post_meta( $_id, 'product_short_description' );
	$qnt         = $item['qnt'] ?? 1;
	?>

    <div class="aside_item_line" data-id="<?php echo $_id; ?>">
        <div class="main_title small ttu"><?php echo get_the_title( $_id ); ?></div>
		<?php echo _t( $description, 1 ); ?>
        <div class="cart_aside_sides">
            <div class="cart_aside_side">
                <strong>
					<?php _l( 'piece count:' ); ?>
                </strong>
            </div>
            <div class="cart_aside_side">
                <strong><?php echo $qnt; ?></strong>
            </div>
        </div>
    </div>

	<?php
}

function the_package_price( $id ) {
	$res             = '';
	$currency        = get_current_currency();
	$currency_symbol = $currency ? get_currency_symbol_by_code( $currency ) : '';
	$_currency       = strtolower( $currency );
	$price           = carbon_get_post_meta( $id, "product_package_price_$_currency" ) ?: carbon_get_post_meta( $id, "product_package_price" );
	if ( $price ) {
		$remove_packaging = $_COOKIE['remove_packaging'] ?? '';
		if ( $remove_packaging ) {
			$remove_packaging = explode( ",", $remove_packaging );
		}
		$attr = $remove_packaging && in_array( $id, $remove_packaging ) ? 'checked' : '';
		$str  = _l( 'Without presentation packaging', 1 );
		$res  = $currency_symbol . $price;
		$res  = '<label class="ch_block">
                                <input type="checkbox" ' . $attr . ' class="remove-packaging" name="remove_packaging" value="' . $id . '"/>
                                <div class="ch_block_icon"></div>
                                <span>' . $str . ' (-' . $res . ')</span>
                            </label>';
	}
	echo $res;
}

function the_product( $_id = false ) {
	$_id = $_id ?: get_the_ID();
	if ( get_post_type( $_id ) === 'product' ) {
		the_wc_product();

		return;
	}
	$img          = get_the_post_thumbnail_url( $_id );
	$title        = get_the_title( $_id );
	$permalink    = get_the_permalink( $_id );
	$description  = carbon_get_post_meta( $_id, 'product_short_description' );
	$availability = carbon_get_post_meta( $_id, 'product_availability' );
	$coming_on    = carbon_get_post_meta( $_id, 'product_coming_on' );
	$tag_image    = carbon_get_post_meta( $_id, 'product_tag_image' );
	$wish_list    = $_COOKIE['wish_list'] ?? '';
	if ( $wish_list ) {
		$wish_list = explode( ",", $wish_list );
	}
	?>
    <div class="product_item">
		<?php if ( $tag_image ): ?>
            <img class="label_img" src="<?php _u( $tag_image ); ?>" alt="label">
		<?php endif; ?>
		<?php if ( $img ): ?>
            <a href="<?php echo $permalink; ?>" class="product_item_img">
                <img src="<?php echo $img; ?>" alt="img"/>
            </a>
		<?php endif; ?>
        <div class="product_item_">
            <div class="product_item_title">
				<?php echo $title;
				echo ' ' . strip_tags( _t( $description, 1 ), '<br>' ); ?>
            </div>
            <div class="product_item_controls">
                <div class="product_item_price">
					<?php the_product_price( $_id ); ?>
                </div>
				<?php if ( $availability == 'on_stock' || $availability == 'pre_order' ): ?>
                    <a class="darken pi_btn add-to-cart" data-id="<?php echo $_id; ?>" href="#">
						<?php _s( _i( 'cart_ico' ) ); ?>
                    </a>
				<?php endif; ?>
                <a class="pi_btn add-to-wish-list <?php echo $wish_list && in_array( $_id, $wish_list ) ? 'active' : ''; ?>"
                   data-id="<?php echo $_id; ?>" href="#">
					<?php _s( _i( 'favorite' ) ); ?>
                </a>
            </div>
			<?php if ( $availability == 'on_stock' ): ?>
                <div class="product_item_status green">
					<?php _l( 'Available on stock' ); ?>
                </div>
			<?php elseif ( $availability == 'sold_out' ): ?>
                <div class="product_item_status " style="color: #E32D2B;">
					<?php _l( 'Sold Out' ); ?>
                </div>
			<?php elseif ( $availability == 'pre_order' ): ?>
                <div class="product_item_status " style="color: #1A73D7;">
					<?php _l( 'In Pre-Order' ); ?>
					<?php
					if ( $coming_on ) {
						echo "<br> $coming_on";
					}
					?>
                </div>
			<?php endif; ?>

        </div>
    </div>
	<?php
}

function the_wc_product( $_id = false ) {
	$_id            = $_id ?: get_the_ID();
	$img            = get_the_post_thumbnail_url( $_id );
	$title          = get_the_title( $_id );
	$permalink      = get_the_permalink( $_id );
	$product        = wc_get_product( $_id );
	$stock_status   = $product->get_stock_status();
	$stock_quantity = $product->get_stock_quantity();
	$tag_image      = carbon_get_post_meta( $_id, 'product_item_tag_image' );
	$description    = $product->get_short_description();
	$price          = $product->get_price_html();
	$wish_list      = $_COOKIE['wish_list'] ?? '';
	if ( $wish_list ) {
		$wish_list = explode( ",", $wish_list );
	}
	?>
    <div class="product_item">
		<?php if ( $tag_image ): ?>
            <img class="label_img" src="<?php _u( $tag_image ); ?>" alt="label">
		<?php endif; ?>
		<?php if ( $img ): ?>
            <a href="<?php echo $permalink; ?>" class="product_item_img">
                <img src="<?php echo $img; ?>" alt="img"/>
            </a>
		<?php endif; ?>
        <div class="product_item_">
            <div class="product_item_title">
				<?php echo $title;
				echo ' ' . strip_tags( _t( $description, 1 ), '<br>' ); ?>
            </div>
            <div class="product_item_controls">
                <div class="product_item_price">
					<?php echo $price; ?>
                </div>
				<?php if ( $product->is_purchasable() ): ?>
                    <a class="darken pi_btn add-to-cart-button" data-product_id="<?php echo $_id; ?>" href="#">
						<?php _s( _i( 'cart_ico' ) ); ?>
                    </a>
				<?php endif; ?>
                <a class="pi_btn add-to-wish-list <?php echo $wish_list && in_array( $_id, $wish_list ) ? 'active' : ''; ?>"
                   data-id="<?php echo $_id; ?>" href="#">
					<?php _s( _i( 'favorite' ) ); ?>
                </a>
            </div>
			<?php if ( $stock_status == 'instock' ): ?>
                <div class="product_item_status green">
					<?php _l( 'Available on stock' ); ?>
                </div>
			<?php elseif ( $stock_status == 'outofstock' ): ?>
                <div class="product_item_status " style="color: #E32D2B;">
					<?php _l( 'Sold Out' ); ?>
                </div>
			<?php elseif ( $stock_status == 'onbackorder' ): ?>
                <div class="product_item_status " style="color: #1A73D7;">
					<?php _l( 'In Pre-Order' ); ?>
                </div>
			<?php endif; ?>

        </div>
    </div>
	<?php
}

function the_buttons( $complex, $class_list = '' ) {
	$links = $complex;
	if ( $links ): foreach ( $links as $link ):
		if ( $link['_type'] == 'link' ):
			?>

            <a class="main_btn <?php echo $class_list; ?>" href="<?php echo $link['link']; ?>">
                <span class="main_btn_inner"><?php echo $link['button_text']; ?></span>
            </a>

		<?php endif; endforeach; endif;
}

function the_product_filter() {
	$var      = variables();
	$set      = $var['setting_home'];
	$assets   = $var['assets'];
	$url      = $var['url'];
	$url_home = $var['url_home'];
	?>

    <div class="product_filters">
        <form action="<?php echo $url; ?>" method="get" id="filter-form"
              class="product_filters_side filter-form">
			<?php
			$attribute_taxonomies = wc_get_attribute_taxonomies();
			if ( $attribute_taxonomies ) {
				foreach ( $attribute_taxonomies as $taxonomy ) {
					$taxonomy_name = wc_attribute_taxonomy_name( $taxonomy->attribute_name );
					$terms         = get_terms( array(
						'taxonomy'   => $taxonomy_name,
						'hide_empty' => false,
					) );
					$taxonomy_name = str_replace( 'pa_', '__', $taxonomy_name );
					if ( $terms ):
						$_get = '';
						if ( isset( $_GET[ $taxonomy_name ] ) ) {
							$_get = explode( ',', $_GET[ $taxonomy_name ] );
						}

						?>

                        <div class="product_filter">
                            <a class="product_filter__js product_filter_title" href="#">
								<?php echo $taxonomy->attribute_label; ?>
                            </a>
                            <div class="product_filters_dropdown">
                                <div class="ch_blocks">
									<?php foreach ( $terms as $term ):
										$test = $_get && in_array( $term->term_id, $_get );
										$attr = $test ? 'checked' : '';
										?>
                                        <label class="ch_block">
                                            <input type="checkbox" data-name="<?php echo $taxonomy_name; ?>"
												<?php echo $attr; ?>
                                                   value="<?php echo $term->term_id; ?>"/>
                                            <div class="ch_block_icon squered"></div>
                                            <span><?php echo $term->name; ?></span>
                                        </label>
									<?php endforeach; ?>
                                </div>
                            </div>
                        </div>

					<?php
					endif;
				}
			}
			?>


            <div class="filter-form-box"></div>
        </form>

    </div>
    <div class="mobile_filters_bottom">
        <a class="main_btn bordered wide" href="#">
            <span class="main_btn_inner"><?php _l( 'apply filter' ); ?></span>
        </a>
    </div>
	<?php
}

function the_products( $products_associations = false ) {
	$paged                  = get_query_var( 'paged' ) ?: 1;
	$default_posts_per_page = get_option( 'posts_per_page' );
	$arr                    = array(
		'post_type'      => 'product',
		'posts_per_page' => (int) $default_posts_per_page,
		'paged'          => $paged,
		'posts_status'   => 'publish',
	);
	if ( $products_associations ) {
		$list = array();
		foreach ( $products_associations as $item ) {
			$_id = $item['id'];
			if ( get_post( $_id ) ) {
				$list[] = $_id;
			}
		}
		if ( ! empty( $list ) ) {
			$arr['post__in'] = $list;
		}
	}
	$attribute_taxonomies = wc_get_attribute_taxonomies();
	if ( $attribute_taxonomies ) {
		foreach ( $attribute_taxonomies as $taxonomy ) {
			$taxonomy_name  = wc_attribute_taxonomy_name( $taxonomy->attribute_name );
			$_taxonomy_name = str_replace( 'pa_', '__', $taxonomy_name );
			if ( isset( $_GET[ $_taxonomy_name ] ) ) {
				$_get      = explode( ',', $_GET[ $_taxonomy_name ] );
				$tax_query = array(
					'taxonomy' => $taxonomy_name,
					'field'    => 'id',
					'terms'    => $_get,
				);
				if ( isset( $arr['tax_query'] ) ) {
					$arr['tax_query'][] = $tax_query;
				} else {
					$arr['tax_query'] = array( $tax_query );
				}
			}

		}
	}
	$query         = new WP_Query( $arr );
	$max_num_pages = $query->max_num_pages;
	?>
    <div class="container-js" id="container-product">
        <div class="product_items ">
			<?php
			if ( $query->have_posts() ):
				while ( $query->have_posts() ):
					$query->the_post();
					the_product();
				endwhile;
			else:
				?>
                <div class="main_title centered">
					<?php _l( 'Not found' ) ?>
                </div>
			<?php
			endif;
			?>
        </div>
		<?php if ( $max_num_pages > 1 ): ?>
            <div class="pagination_block dark_bg container-product-pagination">
                <div class="pagination_count">
					<?php echo _l( 'Page', 1 ) . ' ' . $paged . ' ' . _l( 'of', 1 ) . ' ' . $max_num_pages; ?>
                </div>
				<?php if ( function_exists( 'wp_pagenavi' ) ) {
					$nav = str_replace(
						array(
							'<a',
							'</a>',
							'<span',
							'</span>',
							'wp-pagenavi',
							'current',
							'class="page',
							'nextpostslink',
							'previouspostslink',
						),
						array(
							'<li><a',
							'</a></li>',
							'<li><span',
							'</span></li>',
							'wp-pagenavi page-numbers pagination_list',
							'current page-numbers',
							'class="page page-numbers',
							'nextpostslink page-numbers',
							'previouspostslink page-numbers',
						),
						wp_pagenavi( array( 'echo' => 0, 'query' => $query ) ) );
					echo $nav;
				} ?>
            </div>
		<?php endif; ?>
    </div>
	<?php
	wp_reset_postdata();
	wp_reset_query();
}

function the_payment_methods() {
	?>
    <div class="ch_blocks payment_checks">
        <label class="ch_block large">
            <input type="radio" name="payment_method" value="stripe"/>
            <div class="ch_block_icon"></div>
            <span class="payment_title">Credit/Debit Card</span>
        </label>
        <label class="ch_block large">
            <input type="radio" name="payment_method" value="paypal"/>
            <div class="ch_block_icon"></div>
            <span class="payment_title">PayPal</span>
        </label>

    </div>
	<?php
}

function the_wc_attributes( $product ) {
	$attributes = $product->get_attributes();
	if ( $attributes ): ?>
        <div class="product_features_block wow fadeInUp" data-wow-duration="2s">
            <div class="main_title"><?php _l( 'Product information' ); ?></div>
            <ul class="product_features">
				<?php foreach ( $attributes as $attribute ):
					if ( $attribute->get_visible() ):
						$attribute_name = wc_attribute_label( $attribute->get_name() );
						if ( $attribute->is_taxonomy() ) {
							$terms            = wc_get_product_terms( $product->get_id(), $attribute->get_name(), array( 'fields' => 'names' ) );
							$attribute_values = implode( ', ', $terms );
						} else {
							$attribute_values = $attribute->get_options();
							$attribute_values = implode( ', ', $attribute_values );
						}
						?>
                        <li class="product_feature">
                            <p><?php echo esc_html( $attribute_name ); ?></p>
                            <p><?php echo esc_html( $attribute_values ); ?></p>
                        </li>
					<?php endif; endforeach; ?>
            </ul>
        </div>
	<?php endif;
}

function the_user_order( $customer_order ) {
	$order                = wc_get_order( $customer_order );
	if ( $order ):
		$item_count = $order->get_item_count() - $order->get_item_count_refunded();
		$status           = $order->get_status();
		$total            = $order->get_formatted_order_total();
		$order_items      = $order->get_items();
		$actions          = wc_get_account_orders_actions( $order );
		$first_name       = $order->get_shipping_first_name();
		$last_name        = $order->get_shipping_last_name();
		$shipping_address = array(
			'company'   => $order->get_shipping_company(),
			'address_1' => $order->get_shipping_address_1(),
			'address_2' => $order->get_shipping_address_2(),
			'city'      => $order->get_shipping_city(),
			'state'     => $order->get_shipping_state(),
			'postcode'  => $order->get_shipping_postcode(),
			'country'   => $order->get_shipping_country(),
		);
		$shipping_method  = $order->get_shipping_method();
		$payment_method   = $order->get_payment_method_title();
		?>

        <div id="order" class="account-order" data-status="<?php echo esc_attr( $status ); ?>">
            <div class="account-order-head">
                <div class="account-order-head-column">
                    <div class="account-order__number">
						<?php _l( 'Order' ) ?> #<?php echo $order->get_order_number(); ?>
                    </div>
                    <div class="account-order__status <?php echo esc_attr( $status ); ?>">
						<?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
                    </div>
                </div>
                <div class="account-order-head-column">
                    <div class="account-order-total">
                        <div class="account-order-total__head">
							<?php _l( 'Total' ) ?>
                        </div>
                        <div class="account-order-total__value">
							<?php echo $total; ?>
                        </div>
                    </div>
                </div>
                <div class="account-order-head-column">
                    <div class="account-order-products">
						<?php if ( $order_items ):foreach ( $order_items as $item ):
							$ID = $item->get_product_id();
							$img = get_the_post_thumbnail_url( $ID, 'medium' );
							?>
                            <a href="<?php echo get_the_permalink( $ID ) ?>" class="account-order-product">
                                <img src="<?php echo $img; ?>" alt="">
                            </a>
						<?php endforeach; endif; ?>
                    </div>
                </div>
                <a href="#" class="account-order-head-button">
                    <svg width="30" height="30" viewBox="0 0 30 30" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <mask id="mask0_670_1788" style="mask-type:alpha" maskUnits="userSpaceOnUse"
                              x="0" y="0" width="30" height="30">
                            <rect width="30" height="30" fill="#D9D9D9"/>
                        </mask>
                        <g mask="url(#mask0_670_1788)">
                            <path d="M15 22.8982L21.25 29.0001H8.75L15 22.8982Z" fill="#1C1B1F"/>
                        </g>
                    </svg>
                </a>
            </div>
            <div class="account-order-body-container">
                <div class="account-order-body">
                    <div class="account-order-body-column">
                        <div class="account-order-body__head">
							<?php _l( 'Delivery address' ) ?>
                        </div>
                        <div class="account-order-body__text">
							<?php
							echo $order->get_formatted_shipping_address();
							?>
                        </div>
                    </div>
                    <div class="account-order-body-column">
                        <div class="account-order-body__head">
							<?php _l( 'Delivery' ) ?>
                        </div>
                        <div class="account-order-body__text">
							<?php echo $shipping_method ?>
                        </div>
                    </div>
                    <div class="account-order-body-column">
                        <div class="account-order-body__head">
							<?php _l( 'Payment' ) ?>
                        </div>
                        <div class="account-order-body__text">
							<?php echo $payment_method ?>
                        </div>
                    </div>
                    <div class="account-order-body-column">
                        <div class="account-order-body__head">
							<?php _l( 'Personal information' ) ?>
                        </div>
                        <div class="account-order-body__text">
							<?php echo $first_name; ?> <br>
							<?php echo $last_name; ?> <br>
							<?php echo $order->get_shipping_phone(); ?>
                        </div>
                    </div>
					<?php if ( ! empty( $actions ) ) : ?>
                        <div class="account-order-body-column">
                            <div class="account-order-body__head">
								<?php _l( 'Actions' ) ?>
                            </div>
                            <div class="account-order-body__text">
								<?php
								foreach ( $actions as $key => $action ) {
									if ( $key != 'view' ) {
										echo '<a href="' . esc_url( $action['url'] ) . '" class="woocommerce-button' . esc_attr( $wp_button_class ) . ' button ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a>';

									}
								}
								?>
                            </div>
                        </div>
					<?php endif; ?>
                </div>
            </div>
        </div>
	<?php
	endif;
}

function the_article() {
	$_id      = get_the_ID();
	$_title   = get_the_title();
	$var      = variables();
	$set      = $var['setting_home'];
	$assets   = $var['assets'];
	$url      = $var['url'];
	$url_home = $var['url_home'];
	?>
    <div class="articles-item">
        <a href="<?php echo get_the_permalink() ?>" class="articles-item__image">
            <img class="lozad"
                 data-src="<?php echo get_the_post_thumbnail_url() ?: $assets . 'img/article1.jpg'; ?>"
                 alt="<?php echo $_title; ?>"/>
        </a>
        <div class="articles-item__date">
		    <?php echo get_the_date( 'd/m/Y' ); ?>
        </div>
        <a href="<?php echo get_the_permalink() ?>" class="articles-item__title">
		    <?php echo $_title; ?>
        </a>
        <div class="articles-item__text">
		    <?php echo carbon_get_post_meta( $_id, 'news_item_description' ) ?: get_the_excerpt( $_id ); ?>
        </div>
        <a href="<?php echo get_the_permalink() ?>" class="articles-item__link">
            <?php _l('Read More') ?>
        </a>
    </div>
	<?php
}