<?php

namespace Shea\BP_Security_Check;

/**
 * The main plugin class
 * @package Shea\BP_Security_Check
 */
class Plugin {

    /**
     * @var string
     */
    public $version = '';

    /**
     * @var string
     */
    public $file = '';

    /**
     * @var Security_Check
     */
    public $security_check;

    /**
     * Constructor
     * @param $version
     * @param $file
     */
	function __construct( $version, $file ) {
        $this->file = $file;
        $this->version = $version;

        $this->security_check = new Security_Check();
    }

    /**
     * Run the class's actions
     */
	function run() {
        $this->security_check->run();

		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
	}

	/**
	 * Load up the localization file if WordPress is in a different language.
	 */
	function load_textdomain() {
		load_plugin_textdomain( 'bp-security-check', false, dirname( plugin_basename( $this->file ) ) . '/languages/' );
	}
}
