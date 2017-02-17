<?php

/**
 * Plugin Name: BuddyPress Security Check
 * Plugin URI:  https://bungeshea.com/plugins/bp-security-check/
 * Description: Help combat spam registrations by forcing the user to answer a simple math sum while registering for your BuddyPress-powered site
 * Author:      Shea Bunge
 * Author URI:  https://bungeshea.com
 * Version:     3.2.0
 * License:     MIT
 * License URI: https://opensource.org/licenses/MIT
 * Text Domain: bp-security-check
 * Domain Path: /languages/
 */

/**
 * Adds a maths sum to the BuddyPress registration page that the user
 * must answer correctly before registering
 * @version 3.2.0
 * @license https://opensource.org/licenses/MIT MIT
 * @author Shea Bunge (https://bungeshea.com)
 */

namespace Shea\BP_Security_Check;

require __DIR__ . '/vendor/autoload.php';

/**
 * Retrieve the instance of the plugin class
 * @return Plugin
 */
function plugin() {
	static $plugin;

	if ( is_null( $plugin ) ) {
		$plugin = new Plugin( '3.2.0', __FILE__ );
	}

	return $plugin;
}

add_action( 'plugins_loaded', array( plugin(), 'run' ) );
