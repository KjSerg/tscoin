<?php
$route                = $_GET['route'] ?? '';
$var                  = variables();
$set                  = $var['setting_home'];
$assets               = $var['assets'];
$url                  = $var['url'];
$url_home             = $var['url_home'];
$user_id              = get_current_user_id();
$user                 = get_user_by( 'id', $user_id );
$billing_phone        = get_user_meta( $user_id, 'billing_phone', true );
$billing_country      = get_user_meta( $user_id, 'billing_country', true );
$billing_postcode     = get_user_meta( $user_id, 'billing_postcode', true );
$billing_city         = get_user_meta( $user_id, 'billing_city', true );
$billing_address_1    = get_user_meta( $user_id, 'billing_address_1', true );
$billing_company      = get_user_meta( $user_id, 'billing_company', true );
$company_address      = carbon_get_user_meta( $user_id, 'company_address' );
$company_contact_name = carbon_get_user_meta( $user_id, 'company_contact_name' );
$company_city         = carbon_get_user_meta( $user_id, 'company_city' );
$company_number       = carbon_get_user_meta( $user_id, 'company_number' );
$company_postcode     = carbon_get_user_meta( $user_id, 'company_postcode' );
$company_email        = carbon_get_user_meta( $user_id, 'company_email' );
$company_country      = carbon_get_user_meta( $user_id, 'company_country' );
$company_phone_number = carbon_get_user_meta( $user_id, 'company_phone_number' );
$countries            = get_woocommerce_countries();
$my_orders_url = wc_get_account_endpoint_url('orders');

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
                           class="<?php echo $route == '' ? 'active' : ''; ?>">
							<?php _l( 'My Information' ) ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $my_orders_url; ?>">
							<?php _l( 'My Orders' ) ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ); ?>?route=company"
                           class="<?php echo $route == 'company' ? 'active' : ''; ?>">
							<?php _l( 'Company information' ) ?>
                        </a>
                    </li>
                </ul>
                <a href="<?php echo esc_url( wc_logout_url() ); ?>" class="logaut-button">
                    <span class="icon"><img src="<?php echo $assets; ?>img/logout.svg" alt=""></span>
					<?php _l( 'Log out' ) ?>
                </a>
            </div>
			<?php if ( $route == '' ): ?>
                <div class="account-section-box ">
                    <div class="account-section-box__head">
                        <div class="account-section-box__title">
							<?php _l( 'My Information' ) ?>
                        </div>
                        <a href="#" class="account-section-box__edit">
							<?php _l( 'Edit' ) ?> <span class="icon"><img src="<?php echo $assets; ?>img/edit.svg"
                                                                          alt=""></span>
                        </a>
                    </div>
                    <form method="post" class="account-form personal-data-form form-js form"
                          novalidate
                          action="<?php echo $var['admin_ajax']; ?>" id="personal-data-form">
                        <input type="hidden" name="action" value="change_user_data"/>
						<?php wp_nonce_field( 'change_user_data', 'change_user_data_nonce' ); ?>
                        <input type="hidden" id="lat" name="lat">
                        <input type="hidden" id="lng" name="lng">
                        <div class="form_elements">
                            <div class="form_element half">
                                <div class="fe_title"><?php _l( 'First name' ) ?></div>
                                <input type="text"
                                       value="<?php echo $user->first_name ?>"
                                       placeholder="<?php _l( 'First name' ) ?>" required name="first_name"/>
                            </div>
                            <div class="form_element half">
                                <div class="fe_title"><?php _l( 'Last name' ) ?></div>
                                <input type="text"
                                       value="<?php echo $user->last_name ?>"
                                       placeholder="<?php _l( 'Last name' ) ?>" required name="last_name"/>
                            </div>
                            <div class="form_element half">
                                <div class="fe_title"><?php _l( 'Your e-mail' ) ?></div>
                                <input type="email" required placeholder=""
                                       value="<?php echo $user->user_email ?>"
                                       name="email"
                                       data-reg="[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])"
                                />
                            </div>
                            <div class="form_element half">
                                <div class="fe_title"><?php _l( 'Phone number' ) ?></div>
                                <input type="tel"

                                       value="<?php echo $billing_phone ?>"
                                       placeholder="<?php _l( 'Phone number' ) ?>" required name="phone_number"/>
                            </div>
                            <div class="form_element half">
                                <div class="fe_title"><?php _l( 'Country' ) ?></div>
								<?php if ( $countries ): ?>
                                    <select name="country" class="select">
										<?php foreach ( $countries as $code => $country ) {
											$attr = $code == $billing_country ? 'selected' : '';
											echo "<option $attr value='$code'>$country</option>";
										} ?>
                                    </select>
								<?php else: ?>
                                    <input type="text"
                                           value="<?php echo $billing_country; ?>"
                                           required placeholder="" name="country"/>
								<?php endif; ?>
                            </div>
                            <div class="form_element half">
                                <div class="fe_title"><?php _l( 'City' ) ?></div>
                                <input type="text" id="city"
                                       class="address-js"
                                       value="<?php echo $billing_city; ?>"
                                       placeholder="" required name="city"/>
                            </div>

                            <div class="form_element half">
                                <div class="fe_title"><?php _l( 'Postcode' ) ?></div>
                                <input type="email" required placeholder=""
                                       value="<?php echo $billing_postcode; ?>"
                                       class="address-js" id="post_code"
                                       name="postcode"/>
                            </div>

                            <div class="form_element half">
                                <div class="fe_title"><?php _l( 'Address' ) ?> </div>
                                <input type="text"
                                       id="address"
                                       class="address-js"
                                       value="<?php echo $billing_address_1; ?>"
                                       placeholder="" name="address"/>
                            </div>
                        </div>
                        <div class="form_controls ">
                            <button class="main_btn dark_btn" type="submit">
                                <div class="main_btn_inner">  <?php _l( 'Save' ) ?></div>
                            </button>
                        </div>
                    </form>
                </div>
			<?php elseif ( $route == 'company' ): ?>
                <div class="account-section-box ">
                    <div class="account-section-box__head">
                        <div class="account-section-box__title">
							<?php _l( 'Company information' ) ?>
                        </div>
                        <a href="#" class="account-section-box__edit">
							<?php _l( 'Edit' ) ?> <span class="icon"><img src="<?php echo $assets; ?>img/edit.svg"
                                                                          alt=""></span>
                        </a>
                    </div>
                    <form method="post" class="account-form company-data-form form-js form personal-data-form"
                          novalidate
                          action="<?php echo $var['admin_ajax']; ?>" id="company-data-form">
                        <input type="hidden" name="action" value="change_company_data"/>
						<?php wp_nonce_field( 'change_company_data', 'change_company_data_nonce' ); ?>
                        <div class="form_elements">
                            <div class="form_element half">
                                <div class="fe_title">
									<?php _l( 'Company name (Optional)' ) ?>
                                </div>
                                <input type="text" placeholder="" value="<?php echo $billing_company; ?>"
                                       name="company_name"/>
                            </div>
                            <div class="form_element half">
                                <div class="fe_title"><?php _l( 'Address' ) ?></div>
                                <input type="text" placeholder="" value="<?php echo $company_address ?>"
                                       name="company_address"/>
                            </div>
                            <div class="form_element half">
                                <div class="fe_title"><?php _l( 'Contact Name' ) ?></div>
                                <input type="text" placeholder="" name="company_contact_name"
                                       value="<?php echo $company_contact_name ?>"/>
                            </div>
                            <div class="form_element half">
                                <div class="fe_title"><?php _l( 'City, State' ) ?></div>
                                <input type="text" placeholder="City" name="company_city"
                                       value="<?php echo $company_city ?>"/>
                            </div>
                            <div class="form_element half">
                                <div class="fe_title"><?php _l( 'Company Number' ) ?></div>
                                <input type="text" placeholder="" name="company_number"
                                       value="<?php echo $company_number ?>"/>
                            </div>
                            <div class="form_element half">
                                <div class="fe_title"><?php _l( 'Postcode' ) ?></div>
                                <input type="text" placeholder="" name="company_postcode"
                                       value="<?php echo $company_postcode ?>"/>
                            </div>
                            <div class="form_element half">
                                <div class="fe_title">Email</div>
                                <input type="email" required placeholder=""
                                       value="<?php echo $company_email ?: $user->user_email ?>"
                                       name="company_email"
                                       data-reg="[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])"
                                />
                            </div>
                            <div class="form_element half">
                                <div class="fe_title"><?php _l( 'Country' ) ?></div>
								<?php if ( $countries ): ?>
                                    <select name="company_country" class="select">
										<?php foreach ( $countries as $code => $country ) {
											$attr = $code == $company_country ? 'selected' : '';
											echo "<option $attr value='$code'>$country</option>";
										} ?>
                                    </select>
								<?php else: ?>
                                    <input type="text"
                                           value="<?php echo $company_country; ?>"
                                           required placeholder="" name="company_country"/>
								<?php endif; ?>
                            </div>
                            <div class="form_element half">
                                <div class="fe_title"><?php _l( 'Phone number' ) ?></div>
                                <input type="tel"
                                       value="<?php echo $company_phone_number ?: $billing_phone ?>"
                                       placeholder="<?php _l( 'Phone number' ) ?>" required
                                       name="company_phone_number"/>
                            </div>
                        </div>
                        <div class="form_controls ">
                            <button class="main_btn dark_btn" type="submit">
                                <div class="main_btn_inner"><?php _l( 'Save' ) ?></div>
                            </button>
                        </div>
                    </form>
                </div>
			<?php endif; ?>
        </div>
    </div>
</section>


