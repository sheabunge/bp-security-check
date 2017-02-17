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
	 * The color theme of the widget
	 * @var string
	 */
	public $theme;

	/**
	 * The type of CAPTCHA to serbe
	 * @var string
	 */
	public $type;

	/**
	 * Register action hooks
	 */
	public function run() {
		$settings = plugin()->settings;

		$this->site_key = $settings->get_setting( 'recaptcha_site_key' );
		$this->secret_key = $settings->get_setting( 'recaptcha_secret_key' );

		$this->theme = $settings->get_setting( 'recaptcha_theme' );
		$this->type = $settings->get_setting( 'recaptcha_type' );

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

		?>
		<style>
			#login {
				width: 350px !important;
			}
			.security-question-section {
				text-align: center;
				margin-bottom: 10px !important;
			}
			.g-recaptcha {
				display: inline-block;
			}
		</style>
		<?php
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
		$atts = array(
			'data-sitekey' => $this->site_key,
			'data-theme' => $this->theme,
			'data-type' => $this->type,
		);

		$atts = apply_filters( 'bp_security_check_recaptcha_atts', $atts );
		$html = '<div class="g-recaptcha"';

		foreach ( $atts as $att_name => $att_value ) {
			$html .= sprintf( ' %s="%s"', $att_name, esc_attr( $att_value ) );
		}

		echo $html . '></div>';
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
