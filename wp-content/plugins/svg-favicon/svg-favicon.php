<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://a415production.com
 * @since             1.3
 * @package           svg_favicon
 *
 * @wordpress-plugin
 * Plugin Name:       SVG Favicon
 * Plugin URI:        https://a415production.com/products/plugins/svg-plugin/
 * Description:       Quickly upload a SVG favicon to your site
 * Version:           1.3
 * Author:            a 415 Production
 * Author URI:        https://a415production.com
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       svg-favicon
 * Domain Path:       /languages
 */

// If this file is called directly, abort.

if ( ! defined( 'WPINC' ) ) {
	die;
}

use FOF\SVGFAVICON\Init\ActivatorDeactivator;

const SVG_FAVICON = 'SVG Favicon';
const SVG_FAVICON_VERSION = '1.3';
define( 'SVG_FAVICON_PATH', plugin_dir_path( __FILE__ ) );
define( 'SVG_FAVICON_URL', plugin_dir_url( __FILE__ ) );

require SVG_FAVICON_PATH . '/vendor/autoload.php';
require 'FOFSVGFaviconPsr4AutoloaderClass.php';
$autoloader = new FOFSVGFaviconPsr4AutoloaderClass();

$autoloader->addNamespace(
    'FOF\SVGFAVICON',
    'inc/classes'
);

$autoloader->addNamespace(
    'FOF\SVGFAVICON\Admin',
    'inc/classes/admin'
);

$autoloader->addNamespace(
    'FOF\SVGFAVICON\Client',
    'inc/classes/client'
);

$autoloader->addNamespace(
    'FOF\SVGFAVICON\Init',
    'inc/classes/init'
);

$autoloader->addNamespace(
    'FOF\SVGFAVICON\Models',
    'inc/classes/models'
);

$autoloader->addNamespace(
    'FOF\SVGFAVICON\Services',
    'inc/classes/services'
);

$autoloader->addNamespace(
    'FOF\SVGFAVICON\Traits',
    'inc/classes/traits'
);

$autoloader->addNamespace(
    'FOF\SVGFAVICON\Tools',
    'inc/classes/tools'
);

$autoloader->register();

$activateDeactivate = new ActivatorDeactivator();

register_activation_hook(__FILE__, [
    $activateDeactivate,
    'activate',
]);

register_deactivation_hook(__FILE__, [
    $activateDeactivate,
    'deactivate',
]);

\FOF\SVGFAVICON\KERNEL::get_instance()->init()->run();