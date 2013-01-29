<?php

/**
 * Plugin Name: BuddyPress Security Check
 * Plugin URI: http://bungeshea.com/plugins/bp-security-check/
 * Description: Help combat spam registrations: force the user to answer a simple sum while registering for your BuddyPress-powered site.
 * Author: Shea Bunge
 * Author URI: http://bungeshea.com
 * Version: 1.0-dev
 */

class BuddyPress_security_Check {

	static $prefix = '';

	static function init() {
		add_action( 'bp_signup_validate', array( __CLASS__, 'check_validation' ) );
		add_action( 'bp_after_signup_profile_fields', array( __CLASS__, 'show_input_field' ) );

		self::$prefix = apply_filters( 'bp_security_check_prefix', 'security_question' );
	}


	static function get_field_value( $field_name ) {
		$field_value = intval( $_POST[ self::$prefix . '_' . $field_name ] );
		return apply_filters( 'bp_security_check_get_field_value', $field_value, $field_name );
	}

	static function check_validation(){
		global $bp;

		$number_a = self::get_field_value( 'number_a' );
		$number_b = self::get_field_value( 'number_b' );
		$answer   = self::get_field_value( 'answer' );

		if ( $number_a + $number_b !== $answer ) {
			$bp->signup->errors['security_check'] = __('Sorry, please answer the question again','buddypress');
		}
		elseif ( empty( $answer ) ) {
			$bp->signup->errors['security_check'] = __('This is a required field','buddypress');
		}
	}

	static function show_input_field() {

		$a = rand( 0, 10 );
		$b = rand( 0, 10 );

		?>
		<div style="float:left;clear:left;width:48%;margin:12px 0;" class="Security-question-container">
			<h4><?php esc_html_e('Security Question', 'buddypress'); ?></h4>
			<?php do_action( 'bp_security_check_errors' ); ?>
			<label for="<?php echo self::$prefix; ?>_answer"><?php echo esc_html( sprintf( '%1$d + %2$d =', $a, $b ) ); ?></label>
			<input type="hidden" name="<?php echo self::$prefix; ?>_number_a" value="<?php echo $a; ?>" />
			<input type="hidden" name="<?php echo self::$prefix; ?>_number_b" value="<?php echo $b; ?>" />
			<input type="number" name="<?php echo self::$prefix; ?>_answer" />
		</div>
		<?php
	}
}

add_action( 'bp_init', array( 'BuddyPress_security_Check', 'init' ) );