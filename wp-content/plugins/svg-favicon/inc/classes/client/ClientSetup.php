<?php

namespace FOF\SVGFAVICON\Client;

use FOF\SVGFAVICON\Models\PluginInfo;
use FOF\SVGFAVICON\Services\SiteIcons;

class ClientSetup
{
    private $pluginInfo;
    private $siteIcons;

    public function __construct(PluginInfo $pluginInfo, SiteIcons $siteIcons) {
        $this->pluginInfo = $pluginInfo;
        $this->siteIcons = $siteIcons;
    }

    public function enqueue_scripts() {

        wp_enqueue_script(
            $this->pluginInfo->domain,
            SVG_FAVICON_URL . 'public/js/svg-favicon-public.js',
            [ 'jquery' ],
            $this->pluginInfo->version(),
            false
        );

    }

    public function enqueue_styles() {

        wp_enqueue_style(
            $this->pluginInfo->domain,
            SVG_FAVICON_URL . 'public/css/svg-favicon-public.css',
            [],
            $this->pluginInfo->version()
        );

    }

    public function svg_meta_tags(){

        if( has_site_icon() ){
            return;
        }

        $siteIcons = $this->siteIcons->generateIcons();

        if( $siteIcons === false ){
            return;
        }

        $siteIcons = implode("\n", $siteIcons);

        echo $siteIcons."\n";
    }

    public function svg_favicon($meta_tags) {

        $siteIcons = $this->siteIcons->generateIcons();

        if( $siteIcons === false ){
            return $meta_tags;
        }

        return $siteIcons;
    }}