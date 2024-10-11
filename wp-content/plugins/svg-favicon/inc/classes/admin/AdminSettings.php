<?php

namespace FOF\SVGFAVICON\Admin;

use FOF\SVGFAVICON\Models\PluginInfo;

class AdminSettings
{
    private $pluginInfo;
    private $slug = 'svg-favicon';
    private $options = 'svg_favicon_plugin_options';
//    private $sections = 'svg_favicon';
    private $domain = 'svg-favicon';

    public function __construct(PluginInfo $pluginInfo){
        $this->pluginInfo = $pluginInfo;
    }

    public function section_text(){

        echo '
		<p>Here you can set all the options for using SVG Favicon</p>
		<div id="svg-settings-tabs"></div>';
    }

    public function add_settings_page(){

        add_theme_page(
            __('SVG Favicon',$this->domain),
            __('SVG Favicon',$this->domain),
            'manage_options',
            $this->slug,
            [$this,'settings_page']
        );
    }

    public function register_settings(){

        register_setting( $this->options, $this->options, [$this,'validate'] );

        add_settings_section(
            'svg-favicon-settings-api',
            __('Settings', $this->domain),
            [$this,'section_text'],
            $this->slug
        );
    }

    private function run_settings(){

        $options = get_option( $this->options );

        foreach( $this->settings() as $v ){
            echo '<input type="'.$v['type'].'" id="'.$v['id'].'" name="'.$v['id'].'" value="'.$v['value']($options).'" title="'.$v['title'].'" data-invalid="'.$v['invalid'].'" data-description="'.$v['description'].'" data-default="'.(empty($v['default']) ? '' : $v['default']).'" />';
        }
    }

    public function settings_page(){
         ?>
        <h2>SVG Favicon</h2>
        <form id="submit-svg-favicon" action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="svgFaviconSec" value="<?php echo wp_create_nonce( "svg-favicon" ); ?>" />
            <?php
            settings_fields( $this->options );
            $this->run_settings();
            do_settings_sections( $this->slug ); ?>
<!--            <button id="svg-favicon-submit" name="svg_favicon_submit" class="button button-primary">-->
<!--                <span class="msg">--><?php //esc_attr_e( 'Save' ); ?><!--</span>-->
<!--                <span class="loader">-->
<!--                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 53 10" xml:space="preserve" class="loader-dots"><circle fill="#000" stroke="none" cx="6" cy="5" r="5"><animate attributeName="opacity" dur="1s" values="0;1;0" repeatCount="indefinite" begin="0.1"></animate></circle><circle fill="#000" stroke="none" cx="26" cy="5" r="5"><animate attributeName="opacity" dur="1s" values="0;1;0" repeatCount="indefinite" begin="0.2"></animate></circle><circle fill="#000" stroke="none" cx="46" cy="5" r="5"><animate attributeName="opacity" dur="1s" values="0;1;0" repeatCount="indefinite" begin="0.3"></animate></circle></svg>-->
<!--                </span>-->
<!--            </button>-->
            <!-- 	        <input id="svg-favicon-submit" name="svg_favicon_submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" /> -->
<!--            <button id="reset-svg-favicon">--><?php //_e('Reset SVG Favicon Settings?','svg-favicon'); ?><!--</button>-->
        </form>
        <?php
    }

    private function settings(): array {

        return [
            [
                'id' => 'svg_favicon_reset',
                'type' => 'hidden',
                'title' => __('Reset SVG Favicon Settings','svg-favicon'),
                'section' => 'svg-favicon-settings-api',
                'invalid' => '',
                'description' => 'This action is undoable.',
                'alert' => 'This action is undoable.',
                'value' => function($options){
                    return null;
                },

            ],[
                'id' => 'svg_favicon_clear',
                'type' => 'hidden',
                'title' => __('Remove SVG Favicon','svg-favicon'),
                'section' => 'svg-favicon-settings-api',
                'invalid' => '',
                'description' => null,
                'value' => function($options){
                    return null;
                },

            ],[
                'id' => 'section_manifest',
                'type' => 'hidden',
                'title' => __('Manifest','svg-favicon'),
                'section' => 'svg-favicon-settings-api',
                'invalid' => '',
                'description' => null,
                'value' => function($options){
                    return null;
                },

            ],[
                'id' => 'svg_favicon',
                'type' => 'hidden',
                'title' => 'Upload SVG Favicon',
                'page' => $this->slug,
                'section' => 'svg-favicon-settings-api',
                'invalid' => '',
                'description' => null,
                'value' => function($options){
                    return ( isset($options['svg_favicon']) ? $options['svg_favicon'] : '');
                },

            ],[
                'id' => 'mask_icon',
                'type' => 'hidden',
                'title' => 'Upload Mask Icon',
                'page' => $this->slug,
                'section' => 'svg-favicon-settings-api',
                'invalid' => '',
                'description' => 'For Safari Pinned Tabs...',
                'default' => 'Safari Pinned Tab',
                'value' => function($options){
                    return ( isset($options['mask_icon']) ? $options['mask_icon'] : '');
                },

            ],[
                'id' => 'name',
                'type' => 'hidden',
                'title' => 'Name',
                'page' => $this->slug,
                'section' => 'svg-favicon-settings-api',
                'invalid' => __('Please enter a valid name','svg-favicon'),
                'default' => get_bloginfo('name'),
                'description' => null,
                'value' => function($options){
                    return ( empty($options['name']) ? get_bloginfo('name') : $options['name'] );
                },
            ],[
                'id' => 'mask_color',
                'type' => 'hidden',
                'title' => 'Mask color',
                'page' => $this->slug,
                'section' => 'svg-favicon-settings-api',
                'invalid' => __('Please enter a valid mask color','svg-favicon'),
                'description' => __('Set the pinned tab favicon color for Safari.','svg-favicon'),
                'value' => function($options){
                    return ( isset($options['mask_color']) ? $options['mask_color'] : '#000000');
                }
            ],[
                'id' => 'theme_color',
                'type' => 'hidden',
                'title' => 'Theme color',
                'page' => $this->slug,
                'section' => 'svg-favicon-settings-api',
                'invalid' => __('Please enter a valid theme color','svg-favicon'),
                'description' => __('Set theme color for Android browsers.','svg-favicon'),
                'value' => function($options){
                    return ( isset($options['theme_color']) ? $options['theme_color'] : '#FFFFFF');
                }
            ],[
                'id' => 'background_color',
                'type' => 'hidden',
                'title' => 'Background color',
                'page' => $this->slug,
                'section' => 'svg-favicon-settings-api',
                'invalid' => __('Please enter a valid background color','svg-favicon'),
                'description' => __('Set background color for Android browsers.','svg-favicon'),
                'value' => function($options){
                    return ( isset($options['background_color']) ? $options['background_color'] : '#FFFFFF');
                }
            ],[
                'id' => 'use_manifest',
                'type' => 'hidden',
                'title' => 'Use this site manifest',
                'page' => $this->slug,
                'section' => 'svg-favicon-settings-api',
                'invalid' => '',
                'description' => __('Use this site manifest instead of the default generated site manifest.','svg-favicon'),
                'value' => function($options){
                    return isset($options['use_manifest']) ? $options['use_manifest'] : false;
                    //return ( isset($options['use_manifest']) ? $options['use_manifest'] : true );
                }
            ]
        ];
    }
}