<?php

namespace Shea\BP_Security_Check;

/**
 * The main plugin class
 * @package Shea\BP_Security_Check
 */
class Plugin {

	/**
	 * Current plugin version number
	 * @var string
	 */
	public $version = '';

	/**
	 * Full filesytem URL to base plugin file
	 * @var string
	 */
	public $file = '';

	/**
	 * Instance of Settings class
	 * @var Settings
	 */
	public $settings;

	/**
	 * Instance of Security Check class
	 * @var Security_Check
	 */
	public $security_check;

	/**
	 * Constructor
	 * @param string $version Current plugin version number
	 * @param string $file    Full filesystem URL to base plugin file
	 */
	function __construct( $version, $file ) {
		$this->file = $file;
		$this->version = $version;

		$this->settings = new Settings();

		$active_pages = $this->settings->get_setting( 'pages' );
		$check_type = $this->settings->get_setting( 'type' );

		$this->security_check = 'math' === $check_type ?
			new Math_Check( $active_pages ) :
			new Recaptcha_Check( $active_pages );
	}

	/**
	 * Run the class actions
	 */
	function run() {
		$this->settings->run();
		$this->security_check->run();

		$this->load_textdomain();
		add_filter( 'plugin_action_links_' . plugin_basename( $this->file ), array( $this, 'plugin_settings_link' ) );
	}

	/**
	 * Load up the localization file if WordPress is in a different language.
	 */
	function load_textdomain() {
		$domain = 'bp-security-check';
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		// wp-content/languages/bp-security-check/bp-security-check-[locale].mo
		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . "$domain/$domain-$locale.mo" );

		// wp-content/plugins/bp-security-check/languages/bp-security-check-[locale].mo
		$basename = plugin_basename( dirname( __DIR__ ) );
		load_plugin_textdomain( $domain, false, $basename . '/languages' );
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
