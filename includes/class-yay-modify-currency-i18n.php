<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://vlxx
 * @since      1.0.0
 *
 * @package    Yay_Modify_Currency
 * @subpackage Yay_Modify_Currency/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Yay_Modify_Currency
 * @subpackage Yay_Modify_Currency/includes
 * @author     Onyx <oni_chan_baka@gmail.com>
 */
class Yay_Modify_Currency_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'yay-modify-currency',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
