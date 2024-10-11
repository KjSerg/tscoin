<?php

namespace FOF\SVGFAVICON\Models;

class PluginInfo
{
    public $domain = 'svg-favicon';
    public $option = 'svg_favicon_plugin_options';
    public $query_arg = 'svgFaviconSec';
    public $referrer_check = 'page=svg-favicon';
    public $svg_map = [
        'svg_favicon' => 'svg-favicon',
        'mask_icon' => 'mask-icon',
    ];

    public function ajaxUtils(): array {

        return [
            'domain' => $this->domain,
            'nonce' => wp_create_nonce("svg-favicon"),
            'site_name' => get_bloginfo( 'name' ),
            'urls' => [
                'docs' => 'https://a415production.com/products/plugins/svg-plugin/svg-favicon-documentation/',
                'support' => 'https://a415production.com/products/support/forum/svg-favicon/',
                'plugin_page' => 'https://a415production.com/products/plugins/svg-plugin/',
                'rate_it' => 'https://wordpress.org/support/plugin/'.$this->svg_map['svg_favicon'].'/reviews/?filter=5',
                'donation' => 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=DBMA4K32F6BAY&source=url',
            ],
            'version' => $this->version(),
        ];
    }

    private function getVersionNumber() {

        $transient = $this->domain.'_'.__FUNCTION__;

        if ( false === ( $tokens = get_transient( $transient ) ) ) {
            $tokens = token_get_all(file_get_contents(SVG_FAVICON_PATH.'svg-favicon.php'));
            set_transient( $transient, $tokens );
        }

        $comments = [];

        foreach($tokens as $token)
        {
            if( !empty($token[0]) ) {
                if($token[0] === T_DOC_COMMENT) {
                    $comments[] = $token[1] ?? [];
                }
            }
        }

        $re = '/\* version:\s+(?P<version_number>.*)/mi';

        preg_match_all($re, $comments[0] ?? [], $matches, PREG_SET_ORDER);

        return $matches[0]['version'] ?? '1.3';
    }

    public function addMetaToAdminPluginsPage( $links, $file ) {

        if ( strpos( $file, $this->domain ) !== false ) {

            $donate_url = 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=DBMA4K32F6BAY&source=url';
            $donate_url = esc_url($donate_url);

            $new_links = array(
                '<a href="'.$donate_url.'" rel="noreferrer noopener" target="_blank">'.__('Thanks for 415 Plugins!','svg-favicon').' </a>'
            ); //REVIEW & DONATE


            $links = array_merge( $links, $new_links );

        }

        return $links;
    }

    public function version(){

        if( defined('WP_ENVIRONMENT_TYPE') && WP_ENVIRONMENT_TYPE === 'local' ){
            delete_transient( $this->domain.'_getVersionNumber' );
            return rand();
        }

        return $this->getVersionNumber();
    }
}