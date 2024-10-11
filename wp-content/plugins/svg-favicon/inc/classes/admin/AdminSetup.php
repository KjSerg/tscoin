<?php

namespace FOF\SVGFAVICON\Admin;

use FOF\SVGFAVICON\Models\PluginInfo;

class AdminSetup
{
    private $pluginInfo;
    private $screens = [
        'appearance_page_svg-favicon',
    ];

    public function __construct(PluginInfo $pluginInfo){
        $this->pluginInfo = $pluginInfo;
    }

    public function enqueueAdminStyles($screen) {

        if( !in_array($screen, $this->screens) ){
            return;
        }

        wp_enqueue_style(
            'tippy',
            SVG_FAVICON_URL . 'admin/css/tippy.css',
            [],
            $this->pluginInfo->version()
        );

        wp_enqueue_style(
            'spectrum',
            SVG_FAVICON_URL . 'admin/css/spectrum.min.css',
            [], $this->pluginInfo->version()
        );

        wp_enqueue_style(
            $this->pluginInfo->domain,
            SVG_FAVICON_URL . 'admin/css/svg-favicon-admin.css',
            [], $this->pluginInfo->version()
        );
    }

    public function enqueueScripts($screen) {

        if( !in_array($screen, $this->screens) ){
            return;
        }

        wp_enqueue_script(
            'spectrum-js',
            SVG_FAVICON_URL . 'admin/js/spectrum.min.js',
            [ 'jquery' ],
            $this->pluginInfo->version(),
            true
        );

        wp_enqueue_script(
            $this->pluginInfo->domain,
            SVG_FAVICON_URL . 'admin/js/svg-favicon-admin-bundle.js',
            [
                'jquery',
                'wp-i18n',
            ],
            $this->pluginInfo->version(),
            true
        );

        $script  = 'version = "'. $this->pluginInfo->version() .'"; ';
        $script .= 'SVG_FAVICON = "'.SVG_FAVICON.'";';

        $utils = array_merge($this->pluginInfo->ajaxUtils(), []);

        $script .= 'utils = '.json_encode($utils).';';

        wp_add_inline_script($this->pluginInfo->domain, $script, 'before');
    }
}