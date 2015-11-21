<?php

namespace Shea\BP_Security_Check;
use \ReCaptcha\ReCaptcha;

/**
 * Handles the ReCaptcha security check
 * @package Shea\BP_Security_Check
 */
class Recaptcha_Check {

	/**
	 * The main plugin class
	 * @var Plugin
	 */
	protected $plugin;

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
	 * @param Plugin $plugin
	 */
	function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Register hooks
	 */
	public function run() {
		$this->site_key = $this->plugin->settings->get_setting( 'recaptcha_site_key' );
		$this->secret_key = $this->plugin->settings->get_setting( 'recaptcha_secret_key' );

		if ( ! $this->site_key || ! $this->secret_key ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_script' ) );
		add_action( 'bp_after_signup_profile_fields', array( $this, 'render' ) );
		add_action( 'bp_signup_validate', array( $this, 'validate' ) );
	}

	/**
	 * Enqueue the reCAPTCHA script
	 */
	public function enqueue_script() {
		wp_enqueue_script( 'google-recaptcha', 'https://www.google.com/recaptcha/api.js' );
	}

	/**
	 * Render the security question
	 */
	public function render() {
		?>

		<div style="float: left; clear: left; margin: 12px 0;" class="security-question-section">
			<?php do_action( 'bp_security_check_errors' ); ?>

			<div class="g-recaptcha" data-sitekey="<?php echo esc_attr( $this->site_key ); ?>"></div>
		</div>

		<?php
	}

	/**
	 * Validate the security question
	 */
	public function validate() {
		global $bp;

		if ( empty( $_POST['g-recaptcha-response'] ) ) {
			$bp->signup->errors['security_check'] = __( 'Please answer the security question', 'bp-security-check' );
			return;
		}

		$recaptcha = new ReCaptcha( $this->secret_key );
		$response = $recaptcha->verify( $_POST['g-recaptcha-response'] );

		if ( ! $response->isSuccess() ) {
			$bp->signup->errors['security_check'] = __( 'Please complete the security check again', 'bp-security-check' );
		}

	}
}