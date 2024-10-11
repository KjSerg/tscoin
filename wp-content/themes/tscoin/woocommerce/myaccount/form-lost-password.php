<?php
$var      = variables();
$set      = $var['setting_home'];
$assets   = $var['assets'];
$url      = $var['url'];
$url_home = $var['url_home'];
$_action  = $_GET['action'] ?? '';
?>

<section class="login-section section first_screen">
    <div class="login-section-container">
        <div class="main_title large ">
	        <?php _l('Forgot password?') ?>
        </div>
        <div class="login-section__subtitle">
	        <?php _l('Enter your e-mail to reset the password') ?>

        </div>
	    <form method="post" novalidate class="form-js login-form"
	          action="<?php echo $var['admin_ajax']; ?>" id="login-form">
		    <input type="hidden" name="action" value="reset__password"/>
            <div class="form_elements">

                <div class="form_element">
	                <div class="fe_title"><?php _l( 'Your e-mail' ) ?></div>
	                <input type="email" required placeholder="" name="email"
	                       data-reg="[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])"
	                />
                </div>
            </div>

            <div class="form_controls centered">
                <button class="main_btn dark_btn" type="submit">
                    <div class="main_btn_inner"> <?php _l('Send') ?></div>
                </button>
            </div>
		    <?php wp_nonce_field( 'reset__password', 'reset__password_nonce' ); ?>
        </form>
    </div>
</section>