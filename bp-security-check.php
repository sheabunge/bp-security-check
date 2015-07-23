<?php

/**
 * Plugin Name: BuddyPress Security Check
 * Plugin URI:  http://bungeshea.com/plugins/bp-security-check/
 * Description: Help combat spam registrations by forcing the user to answer a simple math sum while registering for your BuddyPress-powered site
 * Author:      Shea Bunge
 * Author URI:  http://bungeshea.com
 * Version:     1.3.2
 * License:     MIT
 * License URI: http://opensource.org/licenses/MIT
 * Text Domain: bp-security-check
 * Domain Path: /languages/
 */

namespace Shea\BP_Security_Check;

/**
 * Adds a maths sum to the BuddyPress registration page that the user
 * must answer correctly before registering
 * @version 1.3.2
 * @license http://opensource.org/licenses/MIT MIT
 * @author Shea Bunge (http://bungeshea.com)
 */

/**
 * Should be set to the Plugin Version defined above
 * @var string
 */
const VERSION = '1.3.2';

/**
 * Enable autoloading of plugin classes in namespace
 * @param $class_name
 */
function autoload( $class_name ) {

	/* Only autoload classes from this namespace */
	if ( false === strpos( $class_name, __NAMESPACE__ ) ) {
		return;
	}

	/* Remove namespace from class name */
	$class_file = str_replace( __NAMESPACE__ . '\\', '', $class_name );

	/* Convert class name format to file name format */
	$class_file = strtolower( $class_file );
	$class_file = str_replace( '_', '-', $class_file );

	/* Convert sub-namespaces into directories */
	$class_path = explode( '\\', $class_file );
	$class_file = array_pop( $class_path );
	$class_path = implode( '/', $class_path );

	/* Load the class */
	require_once __DIR__ . '/php/' . $class_path . '/class-' . $class_file . '.php';
}

spl_autoload_register( __NAMESPACE__ . '\autoload' );

/* Initialise the plugin class */
$plugin = new Plugin( VERSION, __FILE__ );
add_action( 'plugins_loaded', array( $plugin, 'run' ) );

/* Make class accessible to other plugins */
add_filter(
	'bp_security_check',
	function () use ( $plugin ) {
		return $plugin;
	}
);
