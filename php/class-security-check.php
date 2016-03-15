<?php
/**
 * Created by PhpStorm.
 * User: shea
 * Date: 15/03/16
 * Time: 3:06 PM
 */

namespace Shea\BP_Security_Check;


abstract class Security_Check {

	/**
	 * The main plugin class
	 * @var Plugin
	 */
	protected $plugin;

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
		add_action( 'bp_signup_validate', array( $this, 'validate' ) );
		add_action( 'bp_after_signup_profile_fields', array( $this, 'render' ) );
	}

	/**
	 * Render the security question
	 */
	public abstract function render();

	/**
	 * Validate the security question
	 */
	public abstract function validate();
}
