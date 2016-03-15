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
	 * @var Settings
	 */
	public $settings;

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

		$this->settings = new Settings( $this );

		if ( 'math' === $this->settings->get_setting( 'type' ) ) {
			$this->security_check = new Math_Check( $this );
		} else {
			$this->security_check = new Recaptcha_Check( $this );
		}
	}

	/**
	 * Run the class's actions
	 */
	function run() {
		$this->settings->run();
		$this->security_check->run();

		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( $this->file ), array( $this, 'plugin_settings_link' ) );
	}

	/**
	 * Load up the localization file if WordPress is in a different language.
	 */
	function load_textdomain() {
		load_plugin_textdomain( 'bp-security-check', false, dirname( plugin_basename( $this->file ) ) . '/languages/' );
	}

	/**
	 * Adds a link to the plugin settings
	 *
	 * @since 2.0.0
	 *
	 * @param array $links The existing plugin action links
	 *
	 * @return array The modified plugin action links
	 */
	function plugin_settings_link( $links ) {

		$links['settings'] = sprintf(
			'<a href="%s">%s</a>',
			esc_url( bp_get_admin_url( 'admin.php?page=bp-settings' ) ),
			esc_html__( 'Settings', 'bp-security-check' )
		);

		return $links;
	}
}
