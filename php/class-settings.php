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

	function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

    public function run() {
        add_action( 'admin_init', array( $this, 'register_settings' ), 15 );
    }

    public function register_settings() {
        add_settings_section(
            'bp_security_check',
            __( 'Security Check Settings', 'bp-security-check' ),
            '',
            'buddypress'
        );

	    foreach ( $this->get_fields() as $field ) {
		    add_settings_field(
			    'bp-security-check-' . $field['id'],
			    $field['name'],
			    array( $this, 'render_field' ),
			    'buddypress',
			    'bp_security_check',
			    $field
		    );
	    }
    }

	public function render_field( $field ) {
		$field['value'] = $field['default'];

		call_user_func(
			array( $this, $field['type'] . '_input_field' ),
			$field
		);
	}

	public function get_fields() {
		static $fields;

		if ( isset( $fields ) ) {
			return $fields;
		}

		$fields = array(
			array(
				'id' => 'recapacha-site-key',
				'name' => __( 'reCAPTCHA site key', 'bp-security-check' ),
				'type' => 'text',
				'default' => '',
			),
			array(
				'id' => 'recapacha-secret',
				'name' => __( 'reCAPTCHA secret', 'bp-security-check' ),
				'type' => 'text',
				'default' => '',
			)
		);

		return $fields;
	}

	public function get_defaults() {
		static $defaults;

		if ( isset( $defaults ) ) {
			return $defaults;
		}

		$defaults = wp_list_pluck( $this->get_fields(), 'default' );
		return $defaults;
	}

	public function text_input_field( $atts ) {
		printf( '<input type="%s" name="%s" value="%s">',
			$atts['type'], $atts['id'], $atts['value']
		);
	}
}
