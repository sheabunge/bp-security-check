<?php

namespace Shea\BP_Security_Check;
use \ReCaptcha\ReCaptcha;

/**
 * Handles the ReCaptcha security check
 * @package Shea\BP_Security_Check
 */
class Recaptcha_Check extends Security_Check {

	/**
	 * The ReCaptcha site key
	 * @var string
	 */
	public $site_key;

	/**
	 * The ReCaptcha secret key
	 * @var string
	 */
	public $secret_key;

	/**
	 * Register action hooks
	 */
	public function run() {
		$this->site_key = plugin()->settings->get_setting( 'recaptcha_site_key' );
		$this->secret_key = plugin()->settings->get_setting( 'recaptcha_secret_key' );

		if ( ! $this->site_key || ! $this->secret_key ) {
			return;
		}

		parent::run();

		if ( $this->display_on_register ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ) );
		}

		if ( $this->display_on_login || $this->display_on_lostpassword ) {
			add_action( 'login_enqueue_scripts', array( $this, 'enqueue_login_scripts' ) );
		}
	}

	/**
	 * Enqueue scripts and styles on the site front-end
	 */
	public function enqueue_frontend_scripts() {
		$page_ids = bp_core_get_directory_page_ids();

		/* Only load script on registration page */
		if ( is_page( $page_ids['register'] ) ) {
			$this->enqueue_script();
		}
	}

	/**
	 * Enqueue scripts and styles on the login page
	 */
	public function enqueue_login_scripts() {
		$this->enqueue_script();
		echo '<style> #login { width: 350px !important; } </style>';
	}

	/**
	 * Enqueue the reCAPTCHA script
	 */
	public function enqueue_script() {
		$recaptcha = 'https://www.google.com/recaptcha/api.js';
		$recaptcha = add_query_arg( 'hl', $this->get_language_code(), $recaptcha );
		wp_enqueue_script( 'google-recaptcha', $recaptcha );
	}

	/**
	 * Render the security question field
	 */
	public function render() {
		printf( '<div class="g-recaptcha" data-sitekey="%s"></div>', esc_attr( $this->site_key ) );
	}

	/**
	 * Validate the security question
	 */
	public function validate() {

		if ( empty( $_POST['g-recaptcha-response'] ) ) {
			return __( 'Please answer the security question', 'bp-security-check' );
		}

		$recaptcha = new ReCaptcha( $this->secret_key );
		$response = $recaptcha->verify( $_POST['g-recaptcha-response'] );

		if ( ! $response->isSuccess() ) {
			return __( 'Please complete the security check again', 'bp-security-check' );
		}

		return '';
	}

	/**
	 * Infer the language code from WordPress
	 *
	 * @param string $locale WordPress locale code (optional)
	 * 
	 * @return string reCAPTCHA language code
	 */
	function get_language_code( $locale = '' ) {

		/* If $locale is not set, use WP locale */
		if ( empty( $locale ) ) {
			$locale = get_locale();
		}

		/* reCAPTCHA uses a hyphen separator instead of underscore */
		$locale = str_replace( '_', '-', $locale );

		/* Save original locale in case we need it later */
		$original_locale = $locale;

		/* Use UK English for Australian English instead of US English */
		if ( 'en-AU' === $locale ) {
			$locale = 'en-GB';
		}

		/* If it's not Spanish (Spain), then use Spanish (Latin America) */
		if ( 'es' === substr( $locale, 0, 2 ) && 'es-ES' !== $locale ) {
			$locale = 'es-419';
		}

		/* Two-part locales supported by reCAPTCHA */
		$long_locales = array( 'zh-HK', 'zh-CN', 'zh-TW', 'en-GB', 'fr-CA', 'pt-BR', 'pt-PT' );

		/* Extract first part of locale */
		if ( 2 === strpos( $locale, '-', 2 ) && ! in_array( $locale, $long_locales ) ) {
			$locale = substr( $locale, 0, 2 );
		}

		return apply_filters( 'bp_security_check_recaptcha_lang', $locale, $original_locale );
	}
}
