<?php
/* Template Name: Shopping Cart page template */
$stripe_status = $_GET['stripe_status'] ?? '';
$paypal_status = $_GET['paypal_status'] ?? '';
if ( $stripe_status != '' || $paypal_status != '' ) {
	get_template_part( 'functions/components/thanks' );
	die();
}
$cart           = stripcslashes( $_COOKIE['ts_coin_cart'] ?? '{}' );
$cart           = json_decode( $cart, true );
$id             = get_the_ID();
$page_permalink = get_the_permalink( $id );
$step           = $_GET['step'] ?? ( $_POST['step'] ?? '' );
if ( ! $cart && $step != '' ) {
	header( 'Location:' . $page_permalink );
	die();
}
get_header();
$post             = $_POST;
$country_post     = $post['country'] ?? '';
$delivery_price   = $post['delivery_price'] ?? 0;
$var              = variables();
$set              = $var['setting_home'];
$assets           = $var['assets'];
$url              = $var['url'];
$url_home         = $var['url_home'];
$isLighthouse     = isLighthouse();
$size             = isLighthouse() ? 'thumbnail' : 'full';
$screens          = carbon_get_post_meta( $id, 'screens' );
$default_image    = get_the_post_thumbnail_url() ?: $assets . 'img/head_img6.jpg';
$remove_cross     = _s( _i( 'remove_cross' ), 1 );
$total_discount   = 0;
$items_total      = 0;
$total            = 0;
$currency         = get_current_currency();
$currency_symbol  = $currency ? get_currency_symbol_by_code( $currency ) : '';
$_currency        = strtolower( $currency );
$remove_packaging = $_COOKIE['remove_packaging'] ?? '';
if ( $remove_packaging ) {
	$remove_packaging = explode( ",", $remove_packaging );
}
$count_test = 0;
if ( $step != '' && $step != 'checkout' ) {
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
				$qnt          = $item['qnt'] ?? 1;
				$is_active    = $item['is_active'] ?? 'true';
				$price        = carbon_get_post_meta( $_id, "product_price_$_currency" ) ?: carbon_get_post_meta( $_id, "product_price" );
				$saved        = carbon_get_post_meta( $_id, "product_saved_$_currency" ) ?: carbon_get_post_meta( $_id, "product_saved" );
				$package      = carbon_get_post_meta( $_id, "product_package_price_$_currency" ) ?: carbon_get_post_meta( $_id, "product_package_price" );
				$saved        = $saved ?: 0;
				$percent      = 0;
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
}
if ( $delivery_price ) {
	$delivery_price = (int) $delivery_price;
	$total          = $total + $delivery_price;
}
$stripe_status  = carbon_get_theme_option( 'stripe_status' );
$pay_pal_status = carbon_get_theme_option( 'pay_pal_status' );
?>

    <script>
        var page_id = <?php echo $id; ?>;
    </script>
    <div class="bg_frame lozad" data-background-image="<?php echo $default_image; ?>">
        <div class="bg_frame_bottom"></div>
    </div>
    <section class="section-head first_screen animated_block dark_bg">
        <div class="section_bg">
            <div class="screen_content">
                <div class="head_title"><?php _l( 'attention to details' ); ?></div>
            </div>
        </div>
        <div class="screen_content">
            <div class="main_title large ttu animation_up delay1"><?php echo get_the_title(); ?></div>
            <div class="simple_text larger_text animation_up delay2">
				<?php the_post();
				the_content(); ?>
            </div>
        </div>
    </section>
    <section class="section-cart remove_pt">
        <div class="screen_content cart-container-js">
            <div class="cart_steps dark_bg">
                <div data-step="first" class="cart_step ch_block large <?php echo $step == '' ? 'active' : ''; ?> ">
                    <div class="ch_block_icon large <?php echo get_head_item_class( $step, 1 ); ?> "></div>
                    <span class="cart_step_txt"><?php _l( 'Cart' ); ?></span>
                </div>
                <div data-step="addresses"
                     class="cart_step ch_block large <?php echo $step == 'addresses' ? 'active' : ''; ?>">
                    <div class="ch_block_icon large "></div>
                    <span class="cart_step_txt"><?php _l( 'Addresses' ); ?></span>
                </div>
                <div data-step="shipping"
                     class="cart_step ch_block large <?php echo $step == 'shipping_method' ? 'active' : ''; ?>">
                    <div class="ch_block_icon large"></div>
                    <span class="cart_step_txt"><?php _l( 'Shipping Method' ); ?></span>
                </div>
                <div data-step="payment"
                     class="cart_step ch_block large <?php echo $step == 'payment_methods' ? 'active' : ''; ?>">
                    <div class="ch_block_icon large"></div>
                    <span class="cart_step_txt"><?php _l( 'Payment' ); ?></span>
                </div>
                <div data-step="complete"
                     class="cart_step ch_block large <?php echo $step == 'checkout' ? 'active' : ''; ?>">
                    <div class="ch_block_icon large"></div>
                    <span class="cart_step_txt"><?php _l( 'Complete' ); ?></span>
                </div>
            </div>
            <div class="cart_sides">
				<?php if ( $step == '' ): ?>
                    <div class="cart_side main_side">
						<?php
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
									$img          = get_the_post_thumbnail_url( $_id );
									$permalink    = get_the_permalink( $_id );
									$description  = carbon_get_post_meta( $_id, 'product_short_description' );
									$availability = carbon_get_post_meta( $_id, 'product_availability' );
									$coming_on    = carbon_get_post_meta( $_id, 'product_coming_on' );
									$qnt          = $item['qnt'] ?? 1;
									$is_active    = $item['is_active'] ?? 'true';
									$price        = carbon_get_post_meta( $_id, "product_price_$_currency" ) ?: carbon_get_post_meta( $_id, "product_price" );
									$saved        = carbon_get_post_meta( $_id, "product_saved_$_currency" ) ?: carbon_get_post_meta( $_id, "product_saved" );
									$package      = carbon_get_post_meta( $_id, "product_package_price_$_currency" ) ?: carbon_get_post_meta( $_id, "product_package_price" );
									$saved        = $saved ?: 0;
									$percent      = 0;
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
									?>
                                    <div class="cart_block <?php echo $is_active == 'false' ? 'unactive' : ''; ?>"
                                         data-id="<?php echo $_id; ?>"
                                         data-discount="<?php echo $saved; ?>"
                                         data-price="<?php echo $price; ?>"
                                         data-sub-total="<?php echo $sub_price; ?>"
                                         data-sub-price="<?php echo( $price * $qnt ); ?>"
                                         data-ids="<?php echo implode( ';', $_ids ); ?>"
                                    >
                                        <a class="cart_block_remove" data-id="<?php echo $_id; ?>"
                                           data-discount="<?php echo $saved; ?>"
                                           data-price="<?php echo $price; ?>"
                                           data-sub-total="<?php echo $sub_price; ?>"
                                           data-sub-price="<?php echo( $price * $qnt ); ?>"
                                           data-ids="<?php echo implode( ';', $_ids ); ?>"
                                           href="#">
                                            <span class="remove_txt"><?php _l( 'backorder' ); ?></span>
											<?php echo $remove_cross; ?>
                                        </a>
                                        <label class="ch_block cart_block_check">
                                            <input type="checkbox" class="deactivate-product"
                                                   data-id="<?php echo $_id; ?>"
                                                   data-discount="<?php echo $saved; ?>"
                                                   data-price="<?php echo $price; ?>"
                                                   data-sub-total="<?php echo $sub_price; ?>"
                                                   data-sub-price="<?php echo( $price * $qnt ); ?>"
                                                   data-ids="<?php echo implode( ';', $_ids ); ?>"
												<?php echo $is_active == 'false' ? '' : 'checked="checked"'; ?>
                                            />
                                            <div class="ch_block_icon large"></div>
                                        </label>
                                        <a href="<?php echo $permalink; ?>"
                                           class="cart_block_img">
											<?php if ( $img ): ?>
                                                <img src="<?php echo $img; ?>"
                                                     alt="<?php echo get_the_title( $_id ); ?>"/>
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
                                                            <a class="quant_btn plus_btn transition"
                                                               data-id="<?php echo $_id; ?>"
                                                               data-ids="<?php echo implode( ';', $_ids ); ?>"
                                                               href="javascript:void(0)"></a>
                                                            <a class="quant_btn minus_btn transition"
                                                               data-id="<?php echo $_id; ?>"
                                                               data-ids="<?php echo implode( ';', $_ids ); ?>"
                                                               href="javascript:void(0)"></a>
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
							endforeach;
						endif;
						?>
                    </div>
				<?php elseif ( $step == 'addresses' ): ?>
                    <div class="cart_side main_side cart_form_side">
                        <div class="main_title">
							<?php _l( 'Billing address' ); ?>
                        </div>
                        <form method="post" novalidate class="form-js checkout-form"
                              action="<?php echo $page_permalink; ?>" id="checkout-form">
                            <input type="hidden" name="step" value="shipping_method"/>
                            <div class="form_elements">
                                <div class="form_element half">
                                    <div class="fe_title">
										<?php _l( 'First name' ); ?>
                                    </div>
                                    <input type="text" required placeholder="" name="first_name"/>
                                </div>
                                <div class="form_element half">
                                    <div class="fe_title">
										<?php _l( 'Last name' ); ?>
                                    </div>
                                    <input type="text" required placeholder="" name="last_name"/>
                                </div>
                                <div class="form_element half">
                                    <div class="fe_title">Email</div>
                                    <input type="email" required placeholder="" name="email"
                                           data-reg="[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])"
                                    />
                                </div>
                                <div class="form_element half">
                                    <div class="fe_title">
										<?php _l( 'Company' ); ?>
                                    </div>
                                    <input type="text" placeholder="" name="company"/>
                                </div>
                                <div class="form_element">
                                    <div class="fe_title">
										<?php _l( 'Street address' ); ?>
                                    </div>
                                    <input type="text" required placeholder="" name="address"/>
                                </div>
								<?php if ( $countries = get_current_countries( $id ) ): ?>
                                    <div class="form_element">
                                        <div class="fe_title">
											<?php _l( 'Country' ); ?>
                                        </div>
                                        <div class="select_wrapper">
                                            <select class="select" required name="country">
                                                <option disabled="disabled" selected="selected">
													<?php _l( 'Select' ); ?>
                                                </option>
												<?php foreach ( $countries as $country ): ?>
                                                    <option value="<?php echo $country; ?>">
														<?php echo $country; ?>
                                                    </option>
												<?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
								<?php endif; ?>
                                <div class="form_element half">
                                    <div class="fe_title">
										<?php _l( 'City' ); ?>
                                    </div>
                                    <input type="text" required placeholder="" name="city"/>
                                </div>
                                <div class="form_element half">
                                    <div class="fe_title">
										<?php _l( 'Postcode' ); ?>
                                    </div>
                                    <input type="text" required placeholder="" name="postcode"/>
                                </div>
                                <div class="form_element">
                                    <div class="fe_title">
										<?php _l( 'Phone number' ); ?>
                                    </div>
                                    <input type="tel" required placeholder="" name="phone_number"/>
                                </div>
                                <div id="delivery-address-container" style="display: none" class="form_element">
                                    <div class="fe_title">
										<?php _l( 'Delivery address' ); ?>
                                    </div>
                                    <input type="text" placeholder="" name="delivery_address"/>
                                </div>
                            </div>
                            <div class="form_controls sides">
                                <div class="mfv_checker">
                                    <label class="ch_block">
                                        <input class="mfv_checker_input switch-input" type="checkbox"
                                               value="#delivery-address-container"/>
                                        <div class="ch_block_icon toggler"></div>
                                        <span class="mfv_checker_text"><?php _l( 'Use different address for shipping?' ); ?></span>
                                    </label>
                                </div>
                                <button class="main_btn green_btn slide_btn" type="submit">
                                    <div class="slide_btn_ico">
                                        <img src="<?php echo $assets; ?>img/slide_next.svg" alt="ico"/>
                                    </div>
                                    <div class="main_btn_inner">
										<?php _l( 'NEXT' ); ?>
                                    </div>
                                </button>
                            </div>
                        </form>
                    </div>
				<?php
                elseif ( $step == 'shipping_method' ):
					$current_currency = get_current_currency();
					$_current_currency = strtolower( $current_currency );
					?>
                    <div class="cart_side main_side">
                        <div class="shipment_head">
                            <div class="main_title"><?php _l( 'Shipment' ); ?> </div>
                        </div>
                        <form method="post" action="<?php echo $page_permalink; ?>"
                              class="shipment_container shipment-form-js" id="shipment-form">
							<?php if ( $_POST ) {
								foreach ( $_POST as $key => $value ) {
									if ( $value !== '' && $key !== 'step' ) {
										echo "<input type='hidden' name='$key' value='$value'/>";
									}
								}
							} ?>
                            <input type="hidden" name="step" value="payment_methods"/>
                            <input type="hidden" name="delivery_price" class="delivery-price-js" value=""/>
                            <div class="shipment_lines">
								<?php if ( $shipment_list = get_shipment_list_by_country( $country_post, $id ) ):
									foreach ( $shipment_list as $i => $item ):
										$_countries = $item['countries'];
										$_price = 0;
										if ( $_countries ) {
											foreach ( $_countries as $_country ) {
												if ( $_country['country'] == $country_post ) {
													$_price = $_country["price_$_current_currency"] ?: $_country["price"] ?: 0;
												}
											}
										}
										?>
                                        <div class="shipment_line">
                                            <div class="shipment_line_head">
                                                <label class="ch_block large">
                                                    <input type="radio" required
														<?php echo $i == 0 ? 'checked' : ''; ?>
                                                           name="delivery_method"
                                                           data-price="<?php echo $_price; ?>"
                                                           value="<?php echo $item['title']; ?>"/>
                                                    <div class="ch_block_icon"></div>
                                                    <span><?php echo $item['title']; ?></span>
                                                </label>
                                                <div class="shipment_price_description">
                                                    <div class="shipment_price_text">
														<?php _t( $item['text'] ); ?>
                                                    </div>
                                                    <div class="shipment_price">
														<?php echo $current_currency . $_price; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
									<?php endforeach; endif; ?>
                            </div>
                            <div class="shipment_controls">
                                <a class="main_btn gray_btn slide_btn"
                                   href="<?php echo $page_permalink . '?step=addresses' ?>">
                                    <div class="slide_btn_ico">
                                        <img src="<?php echo $assets; ?>img/slide_prev.svg" alt="ico"/>
                                    </div>
                                    <div class="main_btn_inner"><?php _l( 'Back' ); ?></div>
                                </a>
                                <button class="main_btn green_btn slide_btn">
                                    <div class="slide_btn_ico">
                                        <img src="<?php echo $assets; ?>img/slide_next.svg" alt=""/>
                                    </div>
                                    <div class="main_btn_inner">
										<?php _l( 'NEXT' ); ?>
                                    </div>
                                </button>
                            </div>
                        </form>
                    </div>
				<?php elseif ( $step == 'payment_methods' ): ?>
                    <div class="cart_side main_side">
                        <div class="shipment_head">
                            <div class="main_title">
								<?php _l( 'Payment methods' ); ?>
                            </div>
                        </div>
                        <form action="<?php echo $page_permalink; ?>"
                              class="shipment_container payments-form-js"
                              method="post"
                              id="payments-form">
                            <input type="hidden" name="step" value="checkout"/>
							<?php
							if ( $_POST ) {
								foreach ( $_POST as $key => $value ) {
									if ( $value !== '' && $key !== 'step' ) {
										echo "<input type='hidden' name='$key' value='$value'/>";
									}
								}
							} ?>
                            <?php the_payment_methods(); ?>
                            <div class="shipment_controls no_offset">
                                <a class="main_btn gray_btn slide_btn prev-step-js"
                                   data-step="shipping_method"
                                   href="<?php echo $page_permalink; ?>">
                                    <div class="slide_btn_ico">
                                        <img src="<?php echo $assets; ?>img/slide_prev.svg" alt="ico"/>
                                    </div>
                                    <div class="main_btn_inner"><?php _l( 'Back' ); ?></div>
                                </a>
                                <button class="main_btn green_btn slide_btn">
                                    <div class="slide_btn_ico">
                                        <img src="<?php echo $assets; ?>img/slide_next.svg" alt="ico"/>
                                    </div>
                                    <div class="main_btn_inner"><?php _l( 'NEXT' ); ?></div>
                                </button>
                            </div>
                        </form>
                    </div>
				<?php elseif ( $step == 'checkout' ): ?>
                    <div class="light_frame cart_summary">
                        <div class="cart_summary_head">
                            <div class="summary_head_ico">
                                <img src="<?php echo $assets; ?>img/cart.svg" alt="ico"/>
                            </div>
                            <div class="summary_head_body">
                                <div class="main_title no_offset">
									<?php _l( 'Summary of your order' ); ?>
                                </div>
                                <p><?php echo get_current_currency(); ?></p>
                            </div>
                        </div>
                        <div class="cart_summary_address_sides">
                            <div class="cart_summary_address_side">
                                <div class="cart_summary_address_title">
									<?php _l( 'Billing address' ); ?>
                                </div>
                                <div class="cart_summary_address">
									<?php if ( $_POST ) {
										foreach ( $_POST as $key => $value ) {
											if ( $value !== '' && $key !== 'step' && $key != 'delivery_address' ) {
												echo "<p>$value</p>";
											}
										}
									} ?>
                                </div>
                            </div>
                            <div class="cart_summary_address_side">
                                <div class="cart_summary_address_title">
									<?php _l( 'Shipping address' ); ?>
                                </div>
                                <div class="cart_summary_address">
									<?php
									$delivery_address = $_POST['delivery_address'] ?? '';
									if ( $delivery_address ) {
										echo "<p>$delivery_address</p>";
									} else {
										if ( $_POST ) {
											foreach ( $_POST as $key => $value ) {
												if ( $value !== '' && $key !== 'step' && $key != 'delivery_address' ) {
													echo "<p>$value</p>";
												}
											}
										}
									}
									?>
                                </div>
                            </div>
                        </div>
                        <div class="cart_summary_items">
							<?php
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
										$img          = get_the_post_thumbnail_url( $_id );
										$permalink    = get_the_permalink( $_id );
										$description  = carbon_get_post_meta( $_id, 'product_short_description' );
										$availability = carbon_get_post_meta( $_id, 'product_availability' );
										$coming_on    = carbon_get_post_meta( $_id, 'product_coming_on' );
										$qnt          = $item['qnt'] ?? 1;
										$is_active    = $item['is_active'] ?? 'true';
										$price        = carbon_get_post_meta( $_id, "product_price_$_currency" ) ?: carbon_get_post_meta( $_id, "product_price" );
										$saved        = carbon_get_post_meta( $_id, "product_saved_$_currency" ) ?: carbon_get_post_meta( $_id, "product_saved" );
										$package      = carbon_get_post_meta( $_id, "product_package_price_$_currency" ) ?: carbon_get_post_meta( $_id, "product_package_price" );
										$saved        = $saved ?: 0;
										$percent      = 0;
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
										if ( $is_active == 'true' ):
											?>
                                            <div class="cart_summary_item">
                                                <div class="cart_summary_body">
                                                    <div class="cart_summary_img">
														<?php if ( $img ): ?>
                                                            <img src="<?php echo $img; ?>"
                                                                 alt="<?php echo get_the_title( $_id ); ?>"/>
														<?php endif; ?>
                                                    </div>
                                                    <div class="cart_summary_item_content">
                                                        <div class="cart_summary_item_title">
															<?php echo get_the_title( $_id ); ?>
                                                        </div>
														<?php echo _t( $description, 1 ); ?>
                                                    </div>
                                                </div>
                                                <div class="cart_summary_item_cols">
                                                    <div class="cart_summary_item_col">
                                                        <p>
															<?php _l( 'Unit price' ); ?>
                                                        </p>
                                                        <div class="cart_summary_item_col_val">
															<?php the_product_price( $_id ); ?>
                                                        </div>
                                                    </div>
                                                    <div class="cart_summary_item_col">
                                                        <p>
															<?php _l( 'Quantity' ); ?>
                                                        </p>
                                                        <div class="cart_summary_item_col_val">
															<?php echo $qnt; ?>
                                                        </div>
                                                    </div>
                                                    <div class="cart_summary_item_col">
                                                        <p><?php _l( 'Subtotal' ); ?></p>
                                                        <div class="cart_summary_item_col_val">
															<?php echo $currency_symbol . $sub_price; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
										<?php
										endif;
									endif;
								endforeach;
							endif;
							?>
                        </div>
                        <div class="cart_summary_line">
                            <div class="cart_summary_item_cols">
                                <div class="cart_summary_item_col">
                                    <p>
										<?php _l( 'selected payment method' ); ?>
                                    </p>
                                    <div class="cart_summary_item_col_val"><?php echo $_POST['payment_method'] ?? '-'; ?></div>
                                </div>
                                <div class="cart_summary_item_col">
                                    <p>
										<?php _l( 'selected delivery method' ); ?>
                                    </p>
                                    <div class="cart_summary_item_col_val"><?php echo $_POST['delivery_method'] ?? '-'; ?></div>
                                </div>
                                <div class="cart_summary_item_col">
                                    <p>
										<?php _l( 'Items total:' ); ?>
                                    </p>
                                    <div class="cart_summary_item_col_val">
                                        <strong>
											<?php echo $currency_symbol . $total; ?>
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="cart_summary_note_frame">
                            <form class="form-js finish-checkout-form" id="finish-checkout-form" method="post"
                                  data-url="<?php echo $page_permalink . '?step=thanks'; ?>">
                                <input type="hidden" name="action" value="new_order">
								<?php if ( $_POST ) {
									foreach ( $_POST as $key => $value ) {
										if ( $value !== '' && $key !== 'step' ) {
											echo "<input type='hidden' name='$key' value='$value'/>";
										}
									}
								}
								if ( $cart ) {
									$_cart = $_COOKIE['ts_coin_cart'] ?? '{}';
									echo "<input type='hidden' name='cart' value='$_cart'/>";
								}
								$cookie_remove_packaging = $_COOKIE['remove_packaging'] ?? '';
								echo "<input type='hidden' name='remove_packaging' value='$cookie_remove_packaging'/>";
								echo "<input type='hidden' name='page_id' value='$id'/>";
								?>
                                <div class="form_elements large_offset">
                                    <div class="form_element no_offset">
                                        <div class="fe_title color_title font_reg">
											<?php _l( 'Extra notes' ); ?>
                                        </div>
                                        <textarea class="note_area" name="comment" placeholder=""></textarea>
                                    </div>
                                </div>
                                <div class="form_checks">
                                    <div class="form_checks_title">
										<?php echo carbon_get_post_meta( $id, 'acceptance_title' ); ?>
                                    </div>
									<?php if ( $acceptance_list = carbon_get_post_meta( $id, 'acceptance_list' ) ): ?>
                                        <div class="ch_blocks">
											<?php foreach ( $acceptance_list as $item ): ?>
                                                <label class="ch_block to_top">
                                                    <input type="checkbox"
                                                           name="acceptance"
                                                           value="<?php echo $item['text']; ?>"
                                                           checked="checked"/>
                                                    <div class="ch_block_icon squered larger"></div>
                                                    <span><?php echo $item['text']; ?></span>
                                                </label>
											<?php endforeach; ?>
                                        </div>
									<?php endif; ?>
                                </div>
                                <div class="form_controls">
                                    <button class="main_btn green_btn wide" type="submit">
                                        <div class="main_btn_inner"><?php _l( 'PLACE ORDER' ); ?></div>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
				<?php elseif ( $step == 'thanks' ): ?>
                    <div class="light_frame cart_last">
                        <div class="cart_last_container">
                            <div class="main_title">
								<?php echo carbon_get_post_meta( $id, 'thanks_title' ); ?>
                            </div>
                            <div class="simple_text">
								<?php _t( carbon_get_post_meta( $id, 'thanks_text' ) ); ?>
                            </div>
                        </div>
                    </div>
				<?php endif; ?>
				<?php if ( $step != 'checkout' ): ?>
                    <div class="cart_side aside_side">
                        <div class="cart_aside_line">
                            <div class="main_title">
								<?php _l( 'Summary' ); ?>
                            </div>
                        </div>
                        <div class="cart_aside_line">
							<?php
							if ( $cart ):
								foreach ( $cart as $_id => $item ):
									if ( get_post( $_id ) ):
										the_side_cart_item( $_id, $item );
									endif;
								endforeach;
							endif;
							?>
                        </div>
                        <div class="cart_aside_line">
                            <div class="cart_aside_sides">
                                <div class="cart_aside_side">
                                    <strong>
										<?php _l( 'total discount' ); ?>
                                    </strong>
                                </div>
                                <div class="cart_aside_side">
                                    <strong>
										<?php echo $currency_symbol . $total_discount; ?>
                                    </strong>
                                </div>
                            </div>
                            <div class="cart_aside_sides">
                                <div class="cart_aside_side">
                                    <strong>
										<?php _l( 'items total:' ); ?>
                                    </strong>
                                </div>
                                <div class="cart_aside_side">
                                    <strong><?php echo $currency_symbol . $items_total; ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="cart_aside_line">
                            <div class="total_sides">
                                <div class="total_side">
									<?php _l( 'ORDER TOTAL:' ); ?>
                                </div>
                                <div class="total_side">
									<?php echo $currency_symbol . $total; ?>
                                </div>
                            </div>
                        </div>
                        <div class="cart_aside_line ">
							<?php if ( $cart && $count_test > 0 && $step == '' ): ?>
                                <a class="main_btn green_btn wide"
                                   href="<?php echo $page_permalink . '?step=addresses'; ?>">
                                    <span class="main_btn_inner"><?php _l( 'PROCEED TO CHECKOUT' ); ?></span>
                                </a>
							<?php endif; ?>
                        </div>
                    </div>
				<?php endif; ?>
            </div>
        </div>
    </section>
<?php get_footer(); ?>