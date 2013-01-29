<?php

/**
 * Plugin Name: BuddyPress Security Check
 * Plugin URI: http://bungeshea.com/plugins/bp-security-check/
 * Description: Help combat spam registrations: force the user to answer a simple sum while registering for your BuddyPress-powered site.
 * Author: Shea Bunge
 * Author URI: http://bungeshea.com
 * Version: 1.0-dev
 */

class BuddyPress_Security_Check {

	static $prefix = '';

	static function init() {
		add_action( 'bp_signup_validate', array( __CLASS__, 'check_validation' ) );
		add_action( 'bp_after_signup_profile_fields', array( __CLASS__, 'show_input_field' ) );

		self::$prefix = apply_filters( 'bp_security_check_prefix', 'security_question' );
	}


	static function get_field_value( $field_name ) {
		$field_value = $_POST[ self::$prefix . '_' . $field_name ];
		return apply_filters( 'bp_security_check_get_field_value', $field_value, $field_name );
	}

	static function do_sum( $a, $b, $op ) {
		switch( $op ) {
			case 1: // addition
			default:
				return $a + $b;
				break;
			case 2: // subraction
				return $a - $b;
				break;
			case 3: // multiplication
				return $a * $b;
				break;
			case 4: //division
				return $a / $b;
				break;
		}
	}

	static function format_operation( $op ) {

		switch ( $op ) {
			case 1: // addition
			default:
				return '&#43;';
			case 2: // subraction
				return '&#8722;';
			case 3: // multiplication
				return '&#215;';
			case 4: // division
				return '&#247;';
		}
	}

	static function check_validation(){
		global $bp;

		$number_a  = intval( self::get_field_value( 'number_a' ) );
		$number_b  = intval( self::get_field_value( 'number_b' ) );
		$answer    = intval( self::get_field_value( 'answer' ) );
		$operation = self::get_field_value( 'operation' );

		if ( self::do_sum( $number_a, $number_b, $operation ) !== $answer ) {
			/* The submitted answer was incorrect */
			$bp->signup->errors['security_check'] = __('Sorry, please answer the question again','buddypress');
		}
		elseif ( empty( $answer ) ) {
			/* The answer field wasn't filled in */
			$bp->signup->errors['security_check'] = __('This is a required field','buddypress');
		}
	}

	static function show_input_field() {

		/* Get a random number between 0 and 10 (inclusive) */
		$a = rand( 0, 10 );
		$b = rand( 0, 10 );

		/* Make sure that $a is greater then $b; if not, switch them */
		if ( $b > $a ) {
			$_a = $a;     // backup $a
			$a = $b;      // assign $a (lower number) to $b (higher number)
			$b = $_a;     // assign $b to the original $a
			unset( $_a ); // destroy the backup variable
		}

		/* Get a random operation */
		$op = rand( 1, 4 );

		/* Be sure that the answer will always be positive and that we're not dividing by 0 */
		if ( ! ( 4 === $op && 0 === $b ) || self::do_sum( $a, $b, $op ) < 0 ) {
			$op = 1;
		}

		?>
		<div style="float: left; clear: left; width: 48%; margin: 12px 0;" class="security-question-section">
			<h4><?php esc_html_e('Security Question', 'buddypress'); ?></h4>
			<?php do_action( 'bp_security_check_errors' ); ?>
			<label for="<?php echo self::$prefix; ?>_answer">
				<?php printf( '%1$d %3$s %2$d &#61;', $a, $b, self::format_operation( $op ) ); ?>
			</label>
			<input type="hidden" name="<?php echo self::$prefix; ?>_number_a" value="<?php echo $a; ?>" />
			<input type="hidden" name="<?php echo self::$prefix; ?>_number_b" value="<?php echo $b; ?>" />
			<input type="hidden" name="<?php echo self::$prefix; ?>_operation" value="<?php echo $op; ?>" />
			<input type="number" name="<?php echo self::$prefix; ?>_answer" min="0" max="20" />
		</div>
		<?php
	}
}

add_action( 'bp_init', array( 'BuddyPress_Security_Check', 'init' ) );