<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action( 'carbon_fields_register_fields', 'crb_attach_theme_options' );
function crb_attach_theme_options() {
	$screens_labels = array(
		'plural_name'   => 'sections',
		'singular_name' => 'section',
	);
	$labels         = array(
		'plural_name'   => 'elements',
		'singular_name' => 'element',
	);
	Container::make( 'theme_options', "Site information" )
	         ->add_fields( array(
		         Field::make( "separator", "crb_style_logo", "Logo" ),
		         Field::make( "image", "logo" )->set_required( true ),
		         Field::make( "separator", "crb_style_social", "Networks" ),
		         Field::make( 'complex', 'social_networks' )
		              ->setup_labels( $labels )
		              ->add_fields( array(
			              Field::make( "image", "icon" )->set_required( true )->set_width( 20 ),
			              Field::make( "text", "url" )->set_required( true )->set_width( 80 )->set_attribute( 'type', 'url' ),
		              ) )
	         ) );

	Container::make( 'theme_options', __( "Content zoom" ) )
	         ->set_page_parent( 'options-general.php' )
	         ->add_fields( array(
		         Field::make( "checkbox", "biggest_content" )
	         ) );
	Container::make( 'theme_options', __( "Archive settings" ) )
	         ->set_page_parent( 'edit.php?post_type=news' )
	         ->add_fields( array_merge( get_translations_text(), array(
			         Field::make( "select", "news_appearance", "Appearance" )
			              ->set_options( array(
				              'old' => 'Old theme',
				              'new' => 'New theme',
			              ) )
			              ->set_required( true ),
			         Field::make( "image", "news_image", "Background image" )->set_required( true ),
		         ) )
	         );

	Container::make( 'theme_options', "Page settings" )
	         ->set_page_parent( 'edit.php?post_type=page' )
	         ->add_fields( array(
		         Field::make( 'association', 'thanks_page' )->set_required( true )
		              ->set_types( array(
			              array(
				              'type'      => 'post',
				              'post_type' => 'page',
			              )
		              ) )->set_max( 1 )
	         ) );

	Container::make( 'theme_options', __( "Shop settings" ) )
	         ->set_page_parent( 'edit.php?post_type=products' )
	         ->add_fields( array(
		         Field::make( "separator", "crb_style_currency", "Currency" ),
		         Field::make( 'multiselect', 'currencies' )
		              ->add_options( 'get_currencies' ),
		         Field::make( "separator", "crb_style_payment", "Payment methods" ),

		         Field::make( 'text', 'stripe_public_key' )
		              ->set_width( 50 )->set_required( true ),


		         Field::make( 'text', 'stripe_secret_key' )
		              ->set_width( 50 )->set_required( true ),

		         Field::make( 'text', 'pay_pal_public_key' )
		              ->set_width( 33 )->set_required( true ),

		         Field::make( 'text', 'pay_pal_secret_key' )
		              ->set_width( 33 )->set_required( true ),

	         ) );

}

add_action( 'carbon_fields_register_fields', 'crb_attach_in_front_page' );
function crb_attach_in_front_page() {
	$screens_labels = array(
		'plural_name'   => 'sections',
		'singular_name' => 'section',
	);
	$labels         = array(
		'plural_name'   => 'elements',
		'singular_name' => 'element',
	);
	Container::make( 'post_meta', 'Screens' )
	         ->where( 'post_id', '=', get_option( 'page_on_front' ) )
	         ->add_fields( array(
		         Field::make( "image", "default_image", "Default background image" )->set_required( true ),
		         Field::make( 'complex', 'screens', 'Screens' )
		              ->set_layout( 'tabbed-vertical' )
		              ->setup_labels( $screens_labels )
		              ->add_fields( 'screen_1', 'Banners', array(
			              Field::make( "separator", "crb_style_screen_off", "Disable section?" ),
			              Field::make( 'checkbox', 'screen_off', 'Disable section?' ),
			              Field::make( "separator", "crb_style_inform", "Information" ),
			              get_field_id(),
			              Field::make( "text", "subtitle" ),
			              Field::make( 'complex', 'banners' )->set_required( true )
			                   ->set_layout( 'tabbed-vertical' )
			                   ->setup_labels( $labels )
			                   ->add_fields( array(
				                   Field::make( "rich_text", "title" )->set_required( true ),
				                   Field::make( "rich_text", "text" ),
				                   add_button(),
				                   Field::make( "html", "crb_information_text", ' ' )
				                        ->set_html( '<strong>Picture</strong>' ),
				                   Field::make( 'complex', 'picture' )
				                        ->setup_labels( $labels )
				                        ->set_required( true )
				                        ->add_fields( array(
					                        Field::make( "image", "image" )->set_required( true )->set_width( 20 ),
					                        Field::make( "text", "min_width" )->set_attribute( 'type', 'number' )
					                             ->set_width( 20 )
					                             ->set_required( true ),
				                        ) ),
				                   Field::make( "html", "crb_information_text1", ' ' )
				                        ->set_html( '<strong>OR</strong>' ),
				                   Field::make( "image", "image", "Background image" )
				                        ->set_width( 20 ),
			                   ) )->set_header_template( '
				                        Banner #<%- $_index + 1 %>
				                    ' )
		              ) )
		              ->add_fields( 'screen_2', 'Products', array(
			              Field::make( "separator", "crb_style_screen_off", "Disable section?" ),
			              Field::make( 'checkbox', 'screen_off', 'Disable section?' ),
			              Field::make( "separator", "crb_style_inform", "Information" ),
			              get_field_id(),
			              Field::make( 'association', 'products' )
			                   ->set_types( array(
				                   array(
					                   'type'      => 'post',
					                   'post_type' => 'product',
				                   ),
			                   ) )
		              ) )
	         ) );
	Container::make( 'post_meta', 'Information' )
	         ->where( 'post_id', '=', get_option( 'page_on_front' ) )
	         ->add_fields( array(
		         Field::make( "separator", "crb_style_footer", "Information in the footer" ),
		         Field::make( "rich_text", "footer_text_column_1", 'Sales information' )->set_width( 33 ),
		         Field::make( "rich_text", "footer_text_column_2", 'General information' )->set_width( 33 ),
		         Field::make( "rich_text", "footer_text_column_3", 'Contact information' )->set_width( 33 ),
		         Field::make( "text", "copyright" ),
		         Field::make( "text", "short_code_form" ),
	         ) );
}

add_action( 'carbon_fields_register_fields', 'crb_attach_in_contact_page' );
function crb_attach_in_contact_page() {
	$screens_labels = array(
		'plural_name'   => 'sections',
		'singular_name' => 'section',
	);
	$labels         = array(
		'plural_name'   => 'elements',
		'singular_name' => 'element',
	);
	$hidden         = array(
		'about-page.php',
		'employee-page.php',
		'partners-page.php',
		'gallery-page.php'
	);
	Container::make( 'post_meta', 'Contact information' )
	         ->where( 'post_type', '=', 'page' )
	         ->where( 'post_template', '=', 'contact-page.php' )
	         ->where( 'post_template', 'NOT IN', $hidden )
	         ->where( 'post_id', '!=', get_option( 'page_on_front' ) )
	         ->add_tab( 'Contact form', array(
		         Field::make( "text", "contact_form_title", 'Title' ),
		         Field::make( "text", "contact_form_short_code", 'Form short code' ),
	         ) )
	         ->add_tab( 'Contact information', array(
		         Field::make( 'complex', 'contact_list', 'Contact information' )
		              ->setup_labels( $labels )
		              ->add_fields(
			              array(
				              Field::make( "text", "title" ),
				              Field::make( "rich_text", "text" )->set_required( true ),
			              )
		              ),

	         ) )
	         ->add_tab( 'Social networks', array(
		         Field::make( 'complex', 'social_networks' )
		              ->setup_labels( $labels )
		              ->add_fields( array(
			              Field::make( "image", "icon" )->set_required( true )->set_width( 20 ),
			              Field::make( "text", "url" )->set_required( true )->set_width( 80 )->set_attribute( 'type', 'url' ),
		              ) )
	         ) );
}

add_action( 'carbon_fields_register_fields', 'crb_attach_in_about_page' );
function crb_attach_in_about_page() {
	$screens_labels = array(
		'plural_name'   => 'sections',
		'singular_name' => 'section',
	);
	$labels         = array(
		'plural_name'   => 'elements',
		'singular_name' => 'element',
	);
	$hidden         = array(
		'contact-page.php',
		'employee-page.php',
		'partners-page.php',
		'gallery-page.php'
	);
	Container::make( 'post_meta', 'About information' )
	         ->where( 'post_type', '=', 'page' )
	         ->where( 'post_template', '=', 'about-page.php' )
	         ->where( 'post_template', 'NOT IN', $hidden )
	         ->where( 'post_id', '!=', get_option( 'page_on_front' ) )
	         ->add_fields( array(
		         Field::make( 'complex', 'about_sections', 'Sections' )
		              ->setup_labels( $labels )
		              ->set_layout( 'tabbed-vertical' )
		              ->add_fields( 'text_section', 'Text section', array(
			              Field::make( "text", "title" ),
			              Field::make( "rich_text", "text" )->set_required( true ),
		              ) )
		              ->add_fields( 'dark_block', 'Dark block', array(
			              Field::make( "text", "title" )->set_required( true ),
			              Field::make( "textarea", "subtitle" )->set_rows( 2 ),
			              Field::make( "rich_text", "text" )->set_required( true ),
			              add_button(),
			              Field::make( 'complex', 'picture' )
			                   ->setup_labels( $labels )
			                   ->set_required( true )
			                   ->add_fields( array(
				                   Field::make( "image", "image" )->set_required( true )->set_width( 20 ),
				                   Field::make( "text", "min_width" )->set_attribute( 'type', 'number' )
				                        ->set_width( 20 )
				                        ->set_required( true ),
			                   ) )

		              ) )
		              ->add_fields( 'white_block', 'White block', array(
			              Field::make( "text", "title" )->set_required( true ),
			              Field::make( "textarea", "subtitle" )->set_rows( 2 ),
			              Field::make( "rich_text", "text" )->set_required( true ),
			              add_button(),
			              Field::make( 'complex', 'picture' )
			                   ->setup_labels( $labels )
			                   ->set_required( true )
			                   ->add_fields( array(
				                   Field::make( "image", "image" )->set_required( true )->set_width( 20 ),
				                   Field::make( "text", "min_width" )->set_attribute( 'type', 'number' )
				                        ->set_width( 20 )
				                        ->set_required( true ),
			                   ) )
		              ) )
	         ) );
}

add_action( 'carbon_fields_register_fields', 'crb_attach_in_employee_page' );
function crb_attach_in_employee_page() {
	$screens_labels = array(
		'plural_name'   => 'sections',
		'singular_name' => 'section',
	);
	$labels         = array(
		'plural_name'   => 'elements',
		'singular_name' => 'element',
	);
	$hidden         = array(
		'contact-page.php',
		'about-page.php',
		'partners-page.php',
		'gallery-page.php'
	);
	Container::make( 'post_meta', 'Employee information' )
	         ->where( 'post_type', '=', 'page' )
	         ->where( 'post_template', '=', 'employee-page.php' )
	         ->where( 'post_template', 'NOT IN', $hidden )
	         ->where( 'post_id', '!=', get_option( 'page_on_front' ) )
	         ->add_fields( array(
		         Field::make( 'complex', 'employee_sections', 'Sections' )
		              ->setup_labels( $labels )
		              ->set_layout( 'tabbed-vertical' )
		              ->add_fields( 'dark_block', 'Dark block', array(
			              Field::make( "text", "title" )->set_required( true ),
			              Field::make( "text", "subtitle" ),
			              Field::make( "rich_text", "text" )->set_required( true ),
			              Field::make( 'complex', 'picture' )
			                   ->setup_labels( $labels )
			                   ->set_required( true )
			                   ->add_fields( array(
				                   Field::make( "image", "image" )->set_required( true )->set_width( 20 ),
				                   Field::make( "text", "min_width" )->set_attribute( 'type', 'number' )
				                        ->set_width( 20 )
				                        ->set_required( true ),
			                   ) )
		              ) )
		              ->add_fields( 'white_block', 'White block', array(
			              Field::make( "text", "title" )->set_required( true ),
			              Field::make( "text", "subtitle" ),
			              Field::make( "rich_text", "text" )->set_required( true ),
			              Field::make( 'complex', 'picture' )
			                   ->setup_labels( $labels )
			                   ->set_required( true )
			                   ->add_fields( array(
				                   Field::make( "image", "image" )->set_required( true )->set_width( 20 ),
				                   Field::make( "text", "min_width" )->set_attribute( 'type', 'number' )
				                        ->set_width( 20 )
				                        ->set_required( true ),
			                   ) )
		              ) )
		              ->add_fields( 'text_section', 'Text section', array(
			              Field::make( "text", "title" ),
			              Field::make( "text", "subtitle" ),
			              Field::make( "rich_text", "text" )->set_required( true ),
			              Field::make( "color", "background_color" ),
		              ) )
	         ) );
}

add_action( 'carbon_fields_register_fields', 'crb_attach_in_partners_page' );
function crb_attach_in_partners_page() {
	$labels = array(
		'plural_name'   => 'elements',
		'singular_name' => 'element',
	);
	$hidden = array(
		'contact-page.php',
		'about-page.php',
		'employee-page.php',
		'gallery-page.php'
	);
	Container::make( 'post_meta', 'List' )
	         ->where( 'post_type', '=', 'page' )
	         ->where( 'post_template', '=', 'partners-page.php' )
	         ->where( 'post_template', 'NOT IN', $hidden )
	         ->where( 'post_id', '!=', get_option( 'page_on_front' ) )
	         ->add_fields( array(
		         Field::make( 'complex', 'partners_list', 'List' )
		              ->setup_labels( $labels )
		              ->set_layout( 'tabbed-vertical' )
		              ->add_fields( array(
			              Field::make( "text", "title" )->set_required( true ),
			              Field::make( "rich_text", "text" )->set_required( true ),
			              Field::make( 'association', 'coins' )
			                   ->set_types( array(
				                   array(
					                   'type'      => 'post',
					                   'post_type' => 'coins',
				                   ),
			                   ) )
		              ) )->set_header_template( '
				                        <%- $_index + 1 %>.
				                        <% if (title) { %>
				                            "<%- title %>"
				                        <% } %>
				                    ' )
	         ) );
}

add_action( 'carbon_fields_register_fields', 'crb_attach_in_gallery_page' );
function crb_attach_in_gallery_page() {
	$labels = array(
		'plural_name'   => 'elements',
		'singular_name' => 'element',
	);
	$hidden = array(
		'contact-page.php',
		'about-page.php',
		'employee-page.php',
		'partners-page.php'
	);
	Container::make( 'post_meta', 'List' )
	         ->where( 'post_type', '=', 'page' )
	         ->where( 'post_template', '=', 'gallery-page.php' )
	         ->where( 'post_template', 'NOT IN', $hidden )
	         ->where( 'post_id', '!=', get_option( 'page_on_front' ) )
	         ->add_fields( array(
		         Field::make( 'complex', 'gallery_list', 'Gallery list' )
		              ->setup_labels( $labels )
		              ->set_layout( 'tabbed-vertical' )
		              ->add_fields( array(
			              Field::make( "text", "title" )->set_required( true ),
			              Field::make( 'complex', 'subtitles' )
			                   ->setup_labels( $labels )
			                   ->add_fields( array(
				                   Field::make( "text", "subtitle" )->set_required( true ),
			                   ) ),
			              Field::make( "media_gallery", "gallery" )->set_required( true )

		              ) )->set_header_template( '
				                        <%- $_index + 1 %>.
				                        <% if (title) { %>
				                            "<%- title %>"
				                        <% } %>
				                    ' )

	         ) );
}

add_action( 'carbon_fields_register_fields', 'crb_attach_in_product' );
function crb_attach_in_product() {
	$labels = array(
		'plural_name'   => 'elements',
		'singular_name' => 'element',
	);
	Container::make( 'post_meta', 'Banner' )
	         ->where( 'post_type', '=', 'product' )
	         ->add_fields( array(
		         Field::make( "separator", "crb_style_banner_gallery", "Banner gallery" ),
		         Field::make( "html", "crb_information_text", ' ' )
		              ->set_html( '<strong>Picture</strong>' ),
		         Field::make( 'complex', 'product_item_pictures' )
		              ->setup_labels( $labels )
		              ->set_layout( 'tabbed-vertical' )
		              ->add_fields( array(
				              Field::make( 'complex', 'picture' )
				                   ->setup_labels( $labels )
				                   ->set_required( true )
				                   ->add_fields( array(
					                   Field::make( "image", "image" )->set_required( true )->set_width( 20 ),
					                   Field::make( "text", "min_width" )->set_attribute( 'type', 'number' )
					                        ->set_width( 20 )
					                        ->set_required( true ),
				                   ) ),
			              )
		              )->set_header_template( '
				                        Banner #<%- $_index + 1 %>
				                    ' ),
		         Field::make( "html", "crb_information_text1", ' ' )
		              ->set_html( '<strong>OR</strong>' ),
		         Field::make( "media_gallery", "product_item_banner", "Banner gallery" )->set_width( 80 ),
		         Field::make( "image", "product_item_image", 'Banner' )->set_width( 20 ),
	         ) );
	Container::make( 'post_meta', 'Description' )
	         ->where( 'post_type', '=', 'product' )
	         ->add_fields( array(
		         Field::make( "separator", "crb_style_description", "Description" ),
		         Field::make( "rich_text", "product_item_description", 'Description' ),

	         ) );
	Container::make( 'post_meta', 'Tag' )
	         ->where( 'post_type', '=', 'product' )
	         ->add_fields( array(
		         Field::make( "separator", "crb_style_tag", "Tag" ),
		         Field::make( "image", "product_item_tag_image", 'Tag image' ),
	         ) );
	Container::make( 'post_meta', 'Similar products' )
	         ->where( 'post_type', '=', 'product' )
	         ->add_fields( array(
		         Field::make( "text", "product_similar_list_title", 'Title' ),
		         Field::make( 'association', 'similar_products_list' )
		              ->set_types(
			              array(
				              array(
					              'type'      => 'post',
					              'post_type' => 'product',
				              ),
			              )
		              )
	         ) );
	Container::make( 'post_meta', 'Availability' )
	         ->where( 'post_type', '=', 'product' )
	         ->add_fields( array(
		         Field::make( "text", "product_item_availability_text", 'Availability text' ),
		         Field::make( "date_time", "product_item_date", 'Availability date' )->set_storage_format( 'U' )

	         ) );
}

add_action( 'carbon_fields_register_fields', 'crb_attach_in_users' );
function crb_attach_in_users() {
	Container::make( 'user_meta', 'Settings' )
	         ->add_fields( array(
		         Field::make( "hidden", "user_reset_key", " " ),
	         ) );
	Container::make( 'user_meta', 'Company' )
	         ->add_fields( array(
		         Field::make( "text", "company_address" ),
		         Field::make( "text", "company_contact_name" ),
		         Field::make( "text", "company_city" ),
		         Field::make( "text", "company_number" ),
		         Field::make( "text", "company_postcode" ),
		         Field::make( "text", "company_email" ),
		         Field::make( "text", "company_country" ),
		         Field::make( "text", "company_phone_number" ),
	         ) );
}

add_action( 'carbon_fields_register_fields', 'crb_attach_in_products' );
function crb_attach_in_products() {
	$labels = array(
		'plural_name'   => 'elements',
		'singular_name' => 'element',
	);
	Container::make( 'post_meta', 'Price' )
	         ->where( 'post_type', '=', 'products' )
	         ->add_fields( get_price_fields() );
	Container::make( 'post_meta', 'Information' )
	         ->where( 'post_type', '=', 'products' )
	         ->add_fields( array(
		         Field::make( "separator", "crb_style_banner_gallery", "Banner gallery" ),
		         Field::make( "html", "crb_information_text", ' ' )
		              ->set_html( '<strong>Picture</strong>' ),
		         Field::make( 'complex', 'product_pictures_banner' )
		              ->setup_labels( $labels )
		              ->set_layout( 'tabbed-vertical' )
		              ->set_required( true )
		              ->add_fields( array(
				              Field::make( 'complex', 'picture' )
				                   ->setup_labels( $labels )
				                   ->set_required( true )
				                   ->add_fields( array(
					                   Field::make( "image", "image" )->set_required( true )->set_width( 20 ),
					                   Field::make( "text", "min_width" )->set_attribute( 'type', 'number' )
					                        ->set_width( 20 )
					                        ->set_required( true ),
				                   ) ),
			              )
		              )->set_header_template( '
				                        Banner #<%- $_index + 1 %>
				                    ' ),
		         Field::make( "html", "crb_information_text1", ' ' )
		              ->set_html( '<strong>OR</strong>' ),
		         Field::make( "media_gallery", "product_banner", "Banner gallery" )->set_width( 80 ),
		         Field::make( "image", "product_image", 'Banner' )->set_width( 20 ),
		         Field::make( "separator", "crb_style_information", "General information" ),
		         Field::make( "text", "product_qnt", 'Number of coins' )->set_required( true )->set_width( 50 ),
		         Field::make( "text", "product_reserved_qnt", 'Number of reserved coins' )->set_width( 50 ),
		         Field::make( "text", "product_code", 'Code' )->set_required( true ),
		         Field::make( "select", "product_availability", "Availability" )
		              ->add_options( array(
			              'on_stock'  => 'Available on stock',
			              'sold_out'  => 'Sold Out',
			              'pre_order' => 'In Pre-Order',
		              ) ),
		         Field::make( "text", "product_coming_on", 'Coming on' )
		              ->set_conditional_logic(
			              array(
				              array(
					              'field'   => 'product_availability',
					              'value'   => 'pre_order',
					              'compare' => '=',
				              )
			              )
		              ),
		         Field::make( "text", "product_availability_text", 'Availability text' ),
		         Field::make( "separator", "crb_style_description", "Description" ),
		         Field::make( "rich_text", "product_description", 'Description' ),
		         Field::make( "textarea", "product_short_description", 'Short description' )->set_rows( 4 ),
		         Field::make( "separator", "crb_style_banner", "Images" ),
		         Field::make( "media_gallery", "product_gallery", 'Gallery' )->set_required( true ),
		         Field::make( "separator", "crb_style_list", "Product information" ),
		         Field::make( 'complex', 'product_list', 'Product information' )
		              ->setup_labels( $labels )
		              ->add_fields( array(
			              Field::make( "text", "characteristic", 'Characteristic name' )->set_width( 50 )->set_required( true ),
			              Field::make( "text", "val", 'Characteristic value' )->set_width( 50 )->set_required( true ),
		              ) ),
		         Field::make( "separator", "crb_style_tag", "Tag" ),
		         Field::make( "image", "product_tag_image", 'Tag image' ),
	         ) );
	Container::make( 'post_meta', 'Similar products' )
	         ->where( 'post_type', '=', 'products' )
	         ->add_fields( array(
		         Field::make( "text", "product_similar_title", 'Title' ),
		         Field::make( 'association', 'similar_products' )
		              ->set_types(
			              array(
				              array(
					              'type'      => 'post',
					              'post_type' => 'products',
				              ),
			              )
		              )
	         ) );
}

add_action( 'carbon_fields_register_fields', 'crb_attach_in_news' );
function crb_attach_in_news() {
	$labels = array(
		'plural_name'   => 'elements',
		'singular_name' => 'element',
	);
	Container::make( 'post_meta', 'Images' )
	         ->where( 'post_type', '=', 'news' )
	         ->add_fields( array(
		         Field::make( "image", "news_item_image", "Background image" )->set_width( 50 ),
		         Field::make( "image", "news_item_banner", "Banner" )->set_width( 50 ),
		         Field::make( "media_gallery", "news_item_gallery", "Gallery" ),
	         ) );
	Container::make( 'post_meta', 'Description' )
	         ->where( 'post_type', '=', 'news' )
	         ->add_fields( array(
		         Field::make( "text", "news_item_description", "Description" )
	         ) );
}

add_action( 'after_setup_theme', 'crb_load' );
function crb_load() {
	get_template_part( 'vendor/autoload' );
	\Carbon_Fields\Carbon_Fields::boot();
}

add_filter( 'crb_media_buttons_html', function ( $html, $field_name ) {
	if (
		$field_name === 'product_item_description' ||
		$field_name === 'thanks_text' ||
		$field_name === 'product_description' ||
		$field_name === 'footer_text_column_3' ||
		$field_name === 'footer_text_column_2' ||
		$field_name === 'footer_text_column_1' ||
		$field_name === 'text' ||
		$field_name === 'subtitle' ||
		$field_name === 'title'
	) {
		return;
	}

	return $html;
}, 10, 2 );

function get_field_id() {
	return Field::make( "text", "id", "Section ID (unique value)" )
	            ->set_attribute( 'pattern', '^[a-z0-9\-]+$' )
	            ->set_help_text( 'Latin word without spaces. Possible symbol: "-" <br> <strong>ID value must not be repeated!</strong>' )
	            ->set_required( true );
}

function add_button( $args = array() ) {
	$var      = variables();
	$set      = $var['setting_home'];
	$assets   = $var['assets'];
	$url      = $var['url'];
	$url_home = $var['url_home'];
	$id       = $args['id'] ?? 'links';
	$name     = $args['name'] ?? 'Buttons';
	$max      = $args['max'] ?? 1;
	$labels   = array(
		'plural_name'   => 'elements',
		'singular_name' => 'element',
	);

	return Field::make( 'complex', $id, $name )
	            ->setup_labels( $labels )
	            ->set_max( $max )
	            ->add_fields( 'link', 'Link', array(
		            Field::make( 'text', 'button_text' )
		                 ->set_width( 50 )
		                 ->set_required( true ),
		            Field::make( 'text', 'link' )
		                 ->set_width( 50 )
		                 ->set_attribute( 'type', 'url' )
		                 ->set_required( true ),
	            ) );
}

function get_price_fields() {
	$res        = array();
	$currencies = carbon_get_theme_option( 'currencies' );
	if ( $currencies ) {
		foreach ( $currencies as $currency ) {
			$_currency = strtolower( $currency );
			$res[]     = Field::make( "text", "product_price_$_currency", "Price [$currency]" )->set_width( 33 )->set_attribute( 'type', 'number' );
			$res[]     = Field::make( "text", "product_package_price_$_currency", "The price of the package [$currency]" )->set_width( 33 )->set_attribute( 'type', 'number' );
			$res[]     = Field::make( "text", "product_saved_$_currency", "Saved [$currency]" )->set_width( 33 )->set_attribute( 'type', 'number' );
		}
	} else {
		$res[] = Field::make( "text", "product_price", "Price" )->set_width( 33 )->set_attribute( 'type', 'number' );
		$res[] = Field::make( "text", "product_package_price", "The price of the package" )->set_width( 33 )->set_attribute( 'type', 'number' );
		$res[] = Field::make( "text", "product_saved", "Saved" )->set_width( 33 )->set_attribute( 'type', 'number' );
	}

	return $res;
}

function get_delivery_price_fields() {
	$res        = array();
	$currencies = carbon_get_theme_option( 'currencies' );
	if ( $currencies ) {
		foreach ( $currencies as $currency ) {
			$_currency = strtolower( $currency );
			$res[]     = Field::make( "text", "price_$_currency", "Price [$currency]" )->set_width( 50 )->set_attribute( 'type', 'number' );
		}
	} else {
		$res[] = Field::make( "text", "price", "Price" )->set_attribute( 'type', 'number' );
	}

	return $res;
}

function get_translations_text() {
	$res = array();
	if ( function_exists( 'pll_languages_list' ) ) {
		if ( $languages = pll_languages_list() ) {
			foreach ( $languages as $language ) {
				$res[] = Field::make( 'rich_text', "seo_text_$language", "Seo text $language" )->set_required( true );
			}
		}
	} else {
		$res[] = Field::make( 'rich_text', "seo_text", "Seo text" )->set_required( true );
	}

	return $res;
}