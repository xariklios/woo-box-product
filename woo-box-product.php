<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://valtzis.gr
 * @since             1.0.0
 * @package           Woo_Box_Product
 *
 * @wordpress-plugin
 * Plugin Name:       Woocommerce Box Product
 * Plugin URI:        https://valtzis.gr
 * Description:       A plugin that helps you sell the same product as box or as individual with variants, sharing the same stock quantity.
 * Version:           1.0.0
 * Author:            Charis Valtzis
 * Author URI:        https://valtzis.gr/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-box-product
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
define( 'WOO_BOX_PRODUCT_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woo-box-product-activator.php
 */
function activate_woo_box_product() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-box-product-activator.php';
	Woo_Box_Product_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woo-box-product-deactivator.php
 */
function deactivate_woo_box_product() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-box-product-deactivator.php';
	Woo_Box_Product_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woo_box_product' );
register_deactivation_hook( __FILE__, 'deactivate_woo_box_product' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woo-box-product.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woo_box_product() {

	$plugin = new Woo_Box_Product();
	$plugin->run();

}
run_woo_box_product();
