<?php
namespace FOF\SVGFAVICON;

use FOF\SVGFAVICON\Admin\AdminSettings;
use FOF\SVGFAVICON\Admin\AdminSetup;
use FOF\SVGFAVICON\Client\ClientSetup;
use FOF\SVGFAVICON\Services\i18n;
use FOF\SVGFAVICON\Services\Requests;
use FOF\SVGFAVICON\Services\Sanitize;
use FOF\SVGFAVICON\Services\SiteIcons;
use FOF\SVGFAVICON\Services\SVGService;
use FOF\SVGFAVICON\Services\Validate;
use FOF\SVGFAVICON\Tools\Ajax;
use FOF\SVGFAVICON\Tools\Utils;
use \FOF\SVGFAVICON\Traits\Singleton;
use \FOF\SVGFAVICON\Services\Loader;
use \FOF\SVGFAVICON\Models\PluginInfo;

class KERNEL
{
    use Singleton;

    private $adminSettings;
    private $adminSetup;
    private $ajax;
    private $clientSetup;
    private $i18n;
    private $loader;
    private $pluginInfo;
    private $requests;
    private $sanitize;
    private $siteIcons;
    private $svgServices;
    private $validate;
    private $utils;

    static public function init(){
        $self = self::$instance;

        $self->ajax             = new Ajax();
        $self->i18n             = new i18n();
        $self->loader           = new Loader();
        $self->pluginInfo       = new PluginInfo();
        $self->sanitize         = new Sanitize();
        $self->validate         = new Validate();
        $self->utils            = new Utils();
        $self->siteIcons        = new SiteIcons($self->pluginInfo);
        $self->adminSettings    = new AdminSettings($self->pluginInfo);
        $self->adminSetup       = new AdminSetup($self->pluginInfo);
        $self->clientSetup      = new ClientSetup($self->pluginInfo, $self->siteIcons);
        $self->svgServices      = new SVGService($self->pluginInfo);
        $self->requests         = new Requests($self->pluginInfo, $self->svgServices, $self->validate, $self->utils, $self->ajax, $self->sanitize);

        $self->setLocale();
        $self->defineAdminHooks();
        $self->define_admin_requests();
        $self->define_public_hooks();

        return $self;
    }
    private function define_public_hooks() {

        $this->loader->add_filter( 'wp_head', $this->clientSetup, 'svg_meta_tags', 25);
        $this->loader->add_filter( 'site_icon_meta_tags', $this->clientSetup, 'svg_favicon', 25, 1 );
        $this->loader->add_action( 'wp_enqueue_scripts', $this->clientSetup, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $this->clientSetup, 'enqueue_scripts' );
    }

    private function  define_admin_requests(){

        $request_methods = array_values( array_filter(get_class_methods($this->requests), function($v){
            return $v != '__construct';
        }) );

        if( !empty($request_methods) )
        {
            foreach($request_methods as $method)
            {
                $this->loader->add_action(
                    'wp_ajax_'.$this->pluginInfo->domain.'-'.$method,
                    $this->requests,
                    $method
                );
            }
        }
    }

    private function setLocale(){

        $this->loader->add_action(
            'plugins_loaded',
            $this->i18n,
            'loadPluginTextdomain'
        );
    }

    private function defineAdminHooks(){

        $this->loader->add_filter( 'admin_head', $this->clientSetup, 'svg_meta_tags', 25);

        $this->loader->add_action(
            'plugin_row_meta',
            $this->pluginInfo,
            'addMetaToAdminPluginsPage',
            10,
            2
        );

        $this->loader->add_action(
            'admin_enqueue_scripts',
            $this->adminSetup,
            'enqueueAdminStyles'
        );

        $this->loader->add_action(
            'admin_enqueue_scripts',
            $this->adminSetup,
            'enqueueScripts'
        );

        $this->loader->add_action(
            'admin_menu',
            $this->adminSettings,
            'add_settings_page'
        );

        $this->loader->add_action(
            'admin_init',
            $this->adminSettings,
            'register_settings'
        );
    }

    static public function run(){
        $self = self::$instance;
        $self->loader->run();
    }
}