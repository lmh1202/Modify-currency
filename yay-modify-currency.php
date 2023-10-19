<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://vlxx
 * @since             1.0.0
 * @package           Yay_Modify_Currency
 *
 * @wordpress-plugin
 * Plugin Name:       ModifyCurrency
 * Plugin URI:        https://vlxx
 * Description:       Modify woocommerce currency
 * Version:           1.0.0
 * Author:            Onyx
 * Author URI:        https://vlxx/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       yay-modify-currency
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
define( 'YAY_MODIFY_CURRENCY_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-yay-modify-currency-activator.php
 */
function activate_yay_modify_currency() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-yay-modify-currency-activator.php';
	Yay_Modify_Currency_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-yay-modify-currency-deactivator.php
 */
function deactivate_yay_modify_currency() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-yay-modify-currency-deactivator.php';
	Yay_Modify_Currency_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_yay_modify_currency' );
register_deactivation_hook( __FILE__, 'deactivate_yay_modify_currency' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-yay-modify-currency.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_yay_modify_currency() {

	$plugin = new Yay_Modify_Currency();
	$plugin->run();

}
run_yay_modify_currency();
