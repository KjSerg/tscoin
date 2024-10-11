<?php

namespace FOF\SVGFAVICON\Services;

use FOF\SVGFAVICON\Models\PluginInfo;
use FOF\SVGFAVICON\Tools\Ajax;
use FOF\SVGFAVICON\Tools\Utils;

class Requests
{
    private $ajax;
    private $pluginInfo;
    private $sanitize;
    private $svgService;
    private $validate;
    private $utils;

    public function __construct(PluginInfo $pluginInfo, SVGService $svgService, Validate $validate, Utils $utils, Ajax $ajax, Sanitize $sanitize){
        $this->pluginInfo = $pluginInfo;
        $this->svgService = $svgService;
        $this->validate = $validate;
        $this->utils = $utils;
        $this->ajax = $ajax;
        $this->sanitize = $sanitize;
    }

    public function save_option(){
        check_ajax_referer($this->pluginInfo->domain, $this->pluginInfo->query_arg);

        $key = $this->sanitize->string($_POST['key']);
        $value = $this->sanitize->string($_POST['value']);

        $options = get_option( $this->pluginInfo->option );

        $options[$key] = $value;

        $status = update_option( $this->pluginInfo->option, $options );

        $this->ajax->json_header(['status' => $status]);
        wp_die();
    }

    public function svg_to_image(){
        check_ajax_referer($this->pluginInfo->domain, $this->pluginInfo->query_arg);

        $img = $this->sanitize->string($_POST['imgURI']);

        if( empty($img) ){
            wp_send_json_error( ['errors' => 'Missing blob' ], 403 );
            wp_die();
        }

        $response = $this->svgService->svgToImage($img);

        if( $response['status'] === false ){
            wp_send_json_error( ['errors' => "Could not upload apple touch icon" ], 403 );
            wp_die();
        }

        $this->ajax->json_header(['status' => $response['link']]);
        wp_die();
    }

    public function save_checkbox(){

        check_ajax_referer($this->pluginInfo->domain, $this->pluginInfo->query_arg);

        $data = [];

        $data['key'] = sanitize_text_field($_POST['key']);
        $data['checked'] = sanitize_text_field($_POST['checked']);

        $options = get_option( $this->pluginInfo->option );

        $options[$data['key']] = $data['checked'] == 'true';

        $this->extracted($options);
    }

    public function save_color(){

        check_ajax_referer($this->pluginInfo->domain, $this->pluginInfo->query_arg);

        $data = [];

        $data['key'] = sanitize_text_field($_POST['key']);
        $data['color'] = sanitize_text_field($_POST['color']);

        $options = get_option( $this->pluginInfo->option );

        $valid = $this->validate->svg($data['key'], $data['color']);

        if( !$valid ){
            ob_start();

            echo json_encode([
                'error'=> [
                    [
                        'key' => $data['key'],
                        'message' => sprintf( __( 'Please enter a valid hex %s.', $this->pluginInfo->domain ), $this->utils->error_keys()[$data['key']] ),
                    ]
                ]
            ]);

            header('Content-type: application/json');
            ob_end_flush();
            wp_die();
        }

        $options[$data['key']] = $data['color'];

        $options['manifest'] = $this->svgService->handle_manifest( $options, $data['key'], $data['color'] );

        $this->extracted($options);
    }

    public function save_text(){

        check_ajax_referer($this->pluginInfo->domain, $this->pluginInfo->query_arg);

        $data = [];

        $data['key'] = sanitize_text_field($_POST['key']);
        $data['input'] = sanitize_text_field($_POST['input']);

        $options = get_option( $this->pluginInfo->option );

        $valid = $this->validate->svg($data['key'], $data['input']);

        if( !$valid ){
            ob_start();

            echo json_encode([
                'error'=> [
                    [
                        'key' => $data['key'],
                        'message' => __('Please enter a valid name', 'svg-favicon')
                    ]
                ]
            ]);

            header('Content-type: application/json');
            ob_end_flush();
            wp_die();
        }

        $options[$data['key']] = $data['input'];

        $options['manifest'] =  $this->svgService->handle_manifest( $options, $data['key'], $data['input'] );

        $this->extracted($options);
    }

    public function resetSVG(){

        check_ajax_referer($this->pluginInfo->domain, $this->pluginInfo->query_arg);

        $base = sanitize_text_field($_POST['base']);

        $data['options'] = $_POST['options'];
        $data['options'] = $this->utils->array_map_recursive('sanitize_text_field', $data['options']);

        if( isset($data['options']['svg_favicon']) ){
            $this->svgService->remove_svg('svg-favicon');
            $this->svgService->remove_manifest();
        }

        if( isset($data['options']['mask_icon']) ){
            $this->svgService->remove_svg('mask-icon');
        }

        if( isset($data['options']['use_manifest']) ){
            $data['options']['use_manifest'] = $data['options']['use_manifest'] == 'true';
        }

        $options = get_option( $this->pluginInfo->option );
        $new = array_replace($options, $data['options']);

        if ( $options !== false ) {

            update_option( $this->pluginInfo->option, $new );

        } else {

            add_option( $this->pluginInfo->option, $new, null, 'no' );
        }

        ob_start();
        echo json_encode([ 'success'=> get_option( $this->pluginInfo->option ) ]);
        header('Content-type: application/json');
        ob_end_flush();

        wp_die();

    }

    public function save_svg_favicon(){

        check_ajax_referer($this->pluginInfo->domain, $this->pluginInfo->query_arg);

        $data = [];

        $data['which'] = sanitize_text_field($_POST['which']);
        $data['svg'] = sanitize_text_field($_POST['svg']);

        $check = $this->svgService->enable_svg_upload();

        if( !$check ){
            ob_start();
            echo json_encode(['error'=> [ __('SVG uploads not permitted!', 'svg-favicon') ] ]);
            header('Content-type: application/json');
            ob_end_flush();
            wp_die();
        }

        $valid = $this->validate->svg($data['which'], $data['svg']);

        if( !$valid ){
            ob_start();
            echo json_encode([
                'error'=> [
                    [
                        'key' => $data['which'],
                        'message' => __('This is not a valid SVG', 'svg-favicon')
                    ]
                ]
            ]);
            header('Content-type: application/json');
            ob_end_flush();
            wp_die();
        }

        $options = get_option( $this->pluginInfo->option );

        $options[$data['which']] = $this->svgService->handle_svg( $data['svg'], $data['which'] );

        $options['date'] = date( DATE_ATOM, time() );


        if( $data['which'] == 'svg_favicon' ){
            $options['manifest'] = $this->svgService->remove_manifest();
            $options['manifest'] = $this->svgService->handle_manifest( $options, '', '', $options[$data['which']] );
        }

        if ( $options !== false ) {
            update_option( $this->pluginInfo->option, $options );
        }

        ob_start();
        echo json_encode([ 'success'=> get_option( $this->pluginInfo->option ) ]);
        header('Content-type: application/json');
        ob_end_flush();

        wp_die();
    }

    public function remove_svg_favicon(){

        check_ajax_referer($this->pluginInfo->domain, $this->pluginInfo->query_arg);

        $data = [];

        $data['which'] = sanitize_text_field($_POST['which']);

        $options = get_option( $this->pluginInfo->option );

        $options[$data['which']] = null;
        $options['date'] = date( DATE_ATOM, time() );

        update_option( $this->pluginInfo->option, $options );

        $this->svgService->remove_svg($data['which']);

        if( $data['which'] == 'svg_favicon' ){
            $this->svgService->remove_manifest();
        }

        ob_start();
        echo json_encode([ 'success'=> get_option( $this->pluginInfo->option ) ]);
        header('Content-type: application/json');
        ob_end_flush();

        wp_die();
    }

    public function get_options() {
        check_ajax_referer($this->pluginInfo->domain, $this->pluginInfo->query_arg);

        $options = get_option( $this->pluginInfo->option );

        if( empty($options['name']) ){
            $options['name'] = get_bloginfo( 'name' );
        }

        if( empty($options['background_color']) ){
            $options['background_color'] = '#ffffff';
        }

        if( empty($options['theme_color']) ){
            $options['theme_color'] = '#ffffff';
        }

        if( empty($options['mask_color']) ){
            $options['mask_color'] = '#000000';
        }

        if( !empty($options['svg_favicon']) ){
            $svgRaw = $this->svgService->getSVGFromUrl($options['svg_favicon']);

            if( $svgRaw ){
                $options = array_merge($options,[
                    'svg_favicon_raw' => $svgRaw,
                ]);
            }
        }

        ob_start();
        echo json_encode([ 'success'=> $options ]);
        header('Content-type: application/json');
        ob_end_flush();

        wp_die();
    }

    /**
     * @param $options
     * @return void
     */
    private function extracted($options): void
    {
        if ($options !== false) {

            update_option($this->pluginInfo->option, $options);

        } else {

            add_option($this->pluginInfo->option, false, null, 'no');
        }

        ob_start();
        echo json_encode(['success' => get_option($this->pluginInfo->option)]);
        header('Content-type: application/json');
        ob_end_flush();

        wp_die();
    }
}