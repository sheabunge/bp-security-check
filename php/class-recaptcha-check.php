<?php

namespace Shea\BP_Security_Check;


class Recaptcha_Check {

	/**
	 * @var Plugin
	 */
	protected $plugin;

	public $site_key;

	public $secret_key;

	/**
	 * @param Plugin $plugin
	 */
	function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	public function run() {
		$settings = $this->plugin->settings->get();
		$this->site_key = $settings['recaptcha_site_key'];
		$this->secret_key = $settings['recaptcha_secret_key'];

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_script' ) );
		add_action( 'bp_after_signup_profile_fields', array( $this, 'render' ) );
		add_action( 'bp_signup_validate', array( $this, 'validate' ) );
	}

	public function enqueue_script() {
		wp_enqueue_script(
			'google-recaptcha',
			'https://www.google.com/recaptcha/api.js'
		);
	}

	public function render() {
		printf(
			'<div class="g-recaptcha" data-sitekey="%s"></div>',
			$this->site_key
		);
	}

	public function validate() {
		return true;
	}
}