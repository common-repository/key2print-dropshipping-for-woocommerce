<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://dropshipping.key2print.com
 * @since             1.0.0
 * @package           K2P_Products
 *
 * @wordpress-plugin
 * Plugin Name:       Key2Print dropshipping for WooCommerce
 * Plugin URI:        http://dropshipping.key2print.com
 * Description:       A WooCommerce plugin for commercial printing that allows you to sell high quality printed products for variety of businesses.
 * Version:           1.0.1
 * Author:            Key2Print
 * Author URI:        http://key2print.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       k2p-products
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'K2P_PRODUCTS_VERSION', '1.0.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-k2p-products-activator.php
 */
function activate_k2p_products() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-k2p-products-activator.php';
	K2P_Products_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-k2p-products-deactivator.php
 */
function deactivate_k2p_products() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-k2p-products-deactivator.php';
	K2P_Products_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_k2p_products' );
register_deactivation_hook( __FILE__, 'deactivate_k2p_products' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-k2p-products.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_k2p_products() {

	$plugin = new K2P_Products();
	$plugin->run();

}
run_k2p_products();
