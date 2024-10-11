<?php

namespace FOF\SVGFAVICON\Services;

class i18n
{
    public function loadPluginTextdomain() {

        load_plugin_textdomain(
            'svg-favicon',
            false,
            dirname(plugin_basename(__FILE__), 2) . '/languages/'
        );

    }
}