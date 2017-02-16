<?php

namespace Shea\BP_Security_Check;
use \WP_User;
use \WP_Error;

/**
 * Base class for different security check types
 * @package Shea\BP_Security_Check
 */
abstract class Security_Check {

	/**
	 * List of pages to display the check on
	 * @var array
	 */
    public $active_pages;

	/**
	 * Whether to display on the login or register page
	 * @var bool
	 */
    public $display_on_login, $display_on_register;

	/**
	 * Constructor function
	 *
	 * @param array $active_pages
	 */
    public function __construct( array $active_pages ) {
	    $this->active_pages = $active_pages;
	    $this->display_on_login = in_array( 'login', $this->active_pages );
	    $this->display_on_register = in_array( 'register', $this->active_pages );
	    $this->display_on_lostpassword = in_array( 'lost-password', $this->active_pages );
    }

	/**
	 * Run the class actions
	 */
	public function run() {

		if ( $this->display_on_register ) {
			add_action( 'bp_signup_validate', array( $this, 'validate_register' ) );
			add_action( 'bp_after_signup_profile_fields', array( $this, 'render_register' ) );
		}

		if ( $this->display_on_login ) {
			add_action( 'login_form', array( $this, 'render_login' ) );
			add_action( 'wp_authenticate_user', array( $this, 'validate_login' ) );
		}

		if ( $this->display_on_lostpassword ) {
			add_action( 'lostpassword_form', array( $this, 'render_login' ) );
			add_action( 'lostpassword_post', array( $this, 'validate_lostpassword' ) );
		}
	}

	/**
	 * Validate the security question
	 */
	public abstract function validate();

	/**
	 * Render the security question
	 */
	public abstract function render();

	/**
	 * Validate the security question on the login page
	 *
	 * @param WP_User $user
	 *
	 * @return WP_Error|WP_User
	 */
	public function validate_login( $user ) {
		$result = $this->validate();

		if ( $result ) {
			return new WP_Error( 'security_check_error', $result );
		}

		return $user;
	}

	/**
	 * Render the security question on the login page
	 */
	public function render_login() {
		echo '<div style="margin-bottom: 15px;" class="security-question-section">';
		$this->render();
		echo '</div>';
	}

	/**
	 * Validate the security question on the register page
	 */
	public function validate_register() {
		global $bp;

		$result = $this->validate();

		if ( $result ) {
			$bp->signup->errors['security_check'] = $result;
		}
	}

	/**
	 * Render the security question on the register page
	 */
	public function render_register() {
		echo '<div style="float: left; clear: left; margin: 12px auto;" class="security-question-section">';
		$this->render();
		do_action( 'bp_security_check_errors' );
		echo '</div>';
	}

	/**
	 * Validate the security question on the lost password page
	 *
	 * @param WP_Error $errors WP_Error object
	 *
	 * @return WP_Error
	 */
	public function validate_lostpassword( $errors ) {
		$result = $this->validate();

		if ( $result ) {
			$errors->add( 'security_check_error', $result );
		}

		return $errors;
	}}
