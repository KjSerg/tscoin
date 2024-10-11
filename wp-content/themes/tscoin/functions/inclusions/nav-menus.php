<?php
 add_action('after_setup_theme',
     function () {
         register_nav_menus(
             array(
                 'header_menu' => 'Header menu',
                 'footer_menu' => 'Footer menu 1',
                 'policy_menu' => 'Footer menu 2',
             )
         );
     }
 );