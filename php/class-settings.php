<?php

namespace Shea\BP_Security_Check;

/**
 * Settings class
 * @package Shea\BP_Security_Check
 */
class Settings {

	/**
	 * Run the class actions
	 */
	public function run() {
		add_action( 'admin_init', array( $this, 'register_settings' ), 15 );
		add_action( 'admin_init', array( $this, 'save_settings') );
	}

	/**
	 * Retrieve a setting value
	 *
	 * @param string $setting_id The (unprefixed) setting ID
	 *
	 * @return mixed|null The setting value, null if non-existent setting
	 */
	public function get_setting( $setting_id ) {
		$defaults = $this->get_defaults();

		if ( ! isset( $defaults[ $setting_id ] ) ) {
			return null;
		}

		return get_option( 'bp_security_check_' . $setting_id, $defaults[ $setting_id ] );
	}

	/**
	 * Retrieve a list of the setting field properties
	 * @return array
	 */
	public function get_fields() {
		static $fields;

		if ( isset( $fields ) ) {
			return $fields;
		}

		$fields = array(

			'pages' => array(
				'name' => __( 'Enable security check on these pages', 'bp-security-check' ),
				'type' => 'checkboxes',
				'default' => array( 'register' ),
				'options' => array(
					'register' => __( 'Registration Page', 'bp-security-check' ),
					'lost-password' => __( 'Lost Password Page', 'bp-security-check' ),
					'login' => __( 'Login Page', 'bp-security-check' ),
				),
			),

			'type' => array(
				'name' => __( 'Security check type', 'bp-security-check' ),
				'type' => 'radio',
				'default' => 'math',
				'options' => array(
					'math' => __( 'Legacy math check', 'bp-security-check' ),
					'recaptcha' => __( 'New <a href="https://www.google.com/recaptcha">reCAPTCHA</a> check', 'bp-security-check' ),
				),
				'desc' => __( 'If you choose to use the reCAPTCHA check, you will need to <a href="https://www.google.com/recaptcha/admin">register your site with Google</a> and enter the site key and secret here.' ),
			),

			'recaptcha_site_key' => array(
				'name' => __( 'reCAPTCHA site key', 'bp-security-check' ),
				'type' => 'text',
				'default' => '',
				'size' => 40,
			),

			'recaptcha_secret_key' => array(
				'name' => __( 'reCAPTCHA secret key', 'bp-security-check' ),
				'type' => 'text',
				'default' => '',
				'size' => 40,
			),
		);

		return $fields;
	}

	/**
	 * Retrieve a list of default setting values
	 * @return array The default values, keyed by (unprefixed) setting ID
	 */
	public function get_defaults() {
		static $defaults;

		if ( isset( $defaults ) ) {
			return $defaults;
		}

		$defaults = array();

		foreach ( $this->get_fields() as $id => $field ) {
			$defaults[ $id ] = $field[ 'default' ];
		}

		return $defaults;
	}

	/**
	 * Register the setting sections and fields
	 */
	public function register_settings() {
		add_settings_section(
			'bp_security_check',
			__( 'Security Check Settings', 'bp-security-check' ),
			'',
			'buddypress'
		);

		$fields = $this->get_fields();

		if ( 'recaptcha' !== $this->get_setting( 'type' ) ) {
			unset( $fields['type']['desc'] );
			unset( $fields['recaptcha_site_key'] );
			unset( $fields['recaptcha_secret_key'] );
		}

		foreach ( $fields as $id => $field ) {

			$field = array_merge( array(
				'id' => '',
				'name' => '',
				'type' => 'text',
				'default' => null,
				'sanitize' => '',
			), $field );

			$field['short_id'] = $id;
			$field['id'] = 'bp_security_check_' . $id;
			$field['value'] = get_option( $field['id'], $field['default'] );

			register_setting( 'buddypress', $field['id'], $field['sanitize'] );

			add_settings_field(
				$field['id'],
				$field['name'],
				array( $this, $field['type'] . '_input_field' ),
				'buddypress',
				'bp_security_check',
				$field
			);
		}
	}

	/**
	 * Save the updated setting values
	 *
	 * (I thought that register_setting() was supposted to enable this to happen
	 * automatically, but it seems not.)
	 */
	function save_settings() {

		if ( isset( $_GET['page'] ) && 'bp-settings' === $_GET['page'] && ! empty( $_POST['submit'] ) ) {
			check_admin_referer( 'buddypress-options' );

			foreach ( $this->get_fields() as $id => $field ) {
				$id = 'bp_security_check_' . $id;
				$type = $field['type'];
				$valid_types = array( 'text', 'radio', 'checkboxes' );

				if ( ! isset( $_POST[ $id ] ) || ! in_array( $type, $valid_types ) ) {
					continue;
				}

				$value = $_POST[ $id ];

				if ( 'text' === $type ) {
					$value = trim( $value );
				}

				update_option( $id, $value );
			}
		}
	}

	/**
	 * Render a text input field
	 * @param array $field
	 */
	public function text_input_field( $field ) {
		$extra = '';

		if ( ! empty( $field['size'] ) ) {
			$extra .= sprintf( ' size="%d"', $field['size'] );
		}

		printf( '<input type="%s" name="%s" value="%s"%s>',
			$field['type'], $field['id'], $field['value'], $extra
		);
	}

	/**
	 * Render a radio input field
	 * @param array $field
	 */
	public function radio_input_field( $field ) {
		echo '<p>';
		$radios = array();

		foreach ( $field['options'] as $option => $label ) {
			$radios[] = sprintf(
				'<label><input name="%s" type="radio" value="%s"%s> %s</label>',
				$field['id'],
				$option,
				checked( $option, $field['value'], false ),
				$label
			);
		}

		echo implode( '<br>', $radios ), '</p>';

		if ( isset( $field['desc'] ) ) {
			echo '<p class="description">', $field['desc'], '</p>';
		}
	}

	/**
	 * Render a checkboxes input field
	 * @param array $field
	 */
	public function checkboxes_input_field( $field ) {
		echo '<p>';
		$boxes = array();

		foreach ( $field['options'] as $option => $label ) {
			$boxes[] = sprintf(
				'<label><input name="%s[]" type="checkbox" value="%s"%s> %s</label>',
				$field['id'],
				$option,
				checked( in_array( $option, $field['value'] ), true, false ),
				$label
			);
		}

		echo implode( "\n<br>\n", $boxes ), '</p>';

		if ( isset( $field['desc'] ) ) {
			echo '<p class="description">', $field['desc'], '</p>';
		}
	}
}
