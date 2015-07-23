<?php

namespace Shea\BP_Security_Check;

/**
 * Settings class
 * @package Shea\BP_Security_Check
 */
class Settings {

	/**
	 * Holds the instance of the plugin class
	 * @var Plugin
	 */
	protected $plugin;

	/**
	 * Class constructor
	 * @param Plugin $plugin
	 */
	function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

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
	 * @return mixed|null The setting value, null if non-existant setting
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
			array(
				'id' => 'recaptcha_site_key',
				'name' => __( 'reCAPTCHA site key', 'bp-security-check' ),
				'type' => 'text',
				'default' => '',
				'size' => 40,
			),
			array(
				'id' => 'recaptcha_secret_key',
				'name' => __( 'reCAPTCHA secret key', 'bp-security-check' ),
				'type' => 'text',
				'default' => '',
				'size' => 40,
			)
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

		$defaults = wp_list_pluck( $this->get_fields(), 'default', 'id' );
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

		foreach ( $this->get_fields() as $field ) {

			$field = array_merge( array(
				'id' => '',
				'name' => '',
				'type' => 'text',
				'default' => '',
				'sanitize' => '',
			), $field );

			if ( empty( $field['id'] ) ) {
				continue;
			}

			$field['short_id'] = $field['id'];
			$field['id'] = 'bp_security_check_' . $field['id'];
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

			foreach ( $this->get_fields() as $field ) {
				$id = 'bp_security_check_' . $field['id'];

				if ( ! isset( $_POST[ $id ] ) ) {
					continue;
				}

				if ( 'text' === $field['type'] ) {
					$value = preg_replace( '/[^A-Za-z0-9_]/', '', $_POST[ $id ] );
					update_option( $id, $value );
				}
			}
		}
	}

	/**
	 * Render a text input field
	 * @param $field
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
}
