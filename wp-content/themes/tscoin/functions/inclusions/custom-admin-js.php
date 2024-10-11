<?php
function custom_admin_js() {

	$s = 'input[type="hidden"]';
	$a = variables()['admin_ajax'];

	echo "

        <style>
            .cf-complex__groups {
                z-index: 0!important;
            }
        </style>
       <script>
       var _adminAjax = '$a';
 jQuery(document).ready(function(){
     
     
                                    
                                setTimeout(function () {
                                        jQuery(document).find('.cf-file__inner').each(function () {
                                            var t = jQuery(this);
                                            var id = t.find('$s').eq(0).val();
                                            $.ajax({
                                                type: 'POST',
                                                url: '$a',
                                                data: {
                                                    action: 'get_attach_by_id',
                                                    id: id
                                                }
                                            }).done(function (r) {
                                                t.find('.cf-file__image').attr('src', r)
                        
                                            });
                        
                                        });
                                    }, 1000);
                                
                                
 });
  
    </script>
    ";
}

add_action( 'admin_footer', 'custom_admin_js' );

add_action( 'admin_footer-edit.php', 'add_status_to_pages' );

function add_status_to_pages() {
	echo '<script>';
	$shopping_cart_page = carbon_get_theme_option( 'shopping_cart_page' );
	$shopping_cart_page = $shopping_cart_page ?: 0;
	$wrapper_start      = wrapper_start();
	$wrapper_end        = wrapper_end();
	if ( function_exists( 'pll_languages_list' ) ) {
		if ( $languages = pll_languages_list() ) {
			foreach ( $languages as $language ) {
				if ( $shopping_cart_page ) {
					$shopping_cart_page_id = $shopping_cart_page[0]['id'];
					if ( get_post( $shopping_cart_page_id ) ) {
						$shopping_cart_page_id = pll_get_post( $shopping_cart_page_id, $language );
						if ( $shopping_cart_page_id ) {
							echo "
							jQuery(document).ready( function() {	    
							    jQuery( '#post-' + $shopping_cart_page_id ).find('strong').append( '$wrapper_start — Shopping Cart page [$language] $wrapper_end' );		
							});";
						}
					}
				}
			}
		}
	} else {
		if ( $shopping_cart_page ) {
			$shopping_cart_page_id = $shopping_cart_page[0]['id'];
			if ( get_post( $shopping_cart_page_id ) ) {
				echo "    
				jQuery(document).ready( function() {	    
				    jQuery( '#post-' + $shopping_cart_page_id ).find('strong').append( '$wrapper_start — Shopping Cart page$wrapper_end' );		
				});";
			}
		}
	}
	echo '</script>';
}

function wrapper_start() {
	return '<span class="post-state">';
}

function wrapper_end() {
	return '</span>';
}