<?php

/**
 * Plugin Name: BuddyPress Security Check
 * Plugin URI:  http://bungeshea.com/plugins/bp-security-check/
 * Description: Help combat spam registrations by forcing the user to answer a simple math sum while registering for your BuddyPress-powered site
 * Author:      Shea Bunge
 * Author URI:  http://bungeshea.com
 * Version:     1.2.0
 * License:     MIT
 * License URI: http://opensource.org/licenses/MIT
 * Text Domain: bp-security-check
 * Domain Path: /languages/
 */

/**
 * Adds a maths sum to the BuddyPress registration page that the user
 * must answer correctly before registering
 * @version 1.2.0
 * @license http://opensource.org/licenses/MIT MIT
 * @author Shea Bunge (http://bungeshea.com)
 */

/**
 * Load up the localization file if we're using WordPress in a different language.
 */
function bp_security_check_load_textdomain() {
	load_plugin_textdomain( 'bp-security-check', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action( 'plugins_loaded', 'bp_security_check_load_textdomain' );

/**
 * Check if the user's input was correct
 */
function bp_security_check_validate(){
	global $bp;

	$sum = get_transient( 'bp-security-check' );

	$number_a  = $sum[0];
	$operation = $sum[1];
	$number_b  = $sum[2];

	$answer = intval( $_POST( 'bp-security-check' ) );

	/* Calculate the actual answer */
	if ( 2 == $op ) {
		$result = $a - $b;
	} else {
		$result = $a + $b;
	}

	/* The submitted answer was incorrect */
	if ( $result !== $answer ) {
		$bp->signup->errors['security_check'] = __( 'Sorry, please answer the question again', 'bp-security-check' );
	}

	/* The answer field wasn't filled in */
	elseif ( empty( $answer ) ) {
		$bp->signup->errors['security_check'] = __( 'This is a required field', 'bp-security-check' );
	}

	/* Clean up the transient if the answer was correct */
	else {
		delete_transient( 'bp-security-check' );
	}
}

add_action( 'bp_signup_validate', 'bp_security_check_validate' );


/**
 * Render the input fields
 */
function bp_security_check_field() {

	/* Get a random number between 0 and 10 (inclusive) */
	$a = mt_rand( 0, 10 );
	$b = mt_rand( 0, 10 );

	/* Get a random operation */
	$op = mt_rand( 1, 2 );

	/* Make adjustments to the numbers for subtraction */
	if ( 2 == $op ) {

		/* Make sure that $a is greater then $b; if not, switch them */
		if ( $b > $a ) {
			$_a = $a;     // backup $a
			$a = $b;      // assign $a (lower number) to $b (higher number)
			$b = $_a;     // assign $b to the original $a
			unset( $_a ); // destroy the backup variable
		}

		/* If the numbers are equal then the result will be zero, which will cause an error */
		elseif ( $a == $b ) {
			$a++;
		}
	}

	/* Save sum information */
	set_transient( 'bp-security-check', array( $a, $op, $b ) );

	?>
	<div style="float: left; clear: left; width: 48%; margin: 12px 0;" class="security-question-section">
		<h4><?php esc_html_e( 'Security Question', 'bp-security-check' ); ?></h4>
		<?php do_action( 'bp_security_check_errors' ); ?>
		<label for="bp-security-check" style="display: inline;">
			<?php

			/* First number */
			echo $a;

			/* Print operation as proper HTML entity */
			if ( 2 == $op ) {
				echo ' &#8722; '; // subtraction
			} else {
				echo ' &#43; '; // addition
			}

			/* Second number */
			echo $b;

			/* Equals symbol */
			echo ' &#61;';

			?>
		</label>
		<input type="number" name="bp-security-check" required="required" />
	</div>
	<?php
}

add_action( 'bp_after_signup_profile_fields', 'bp_security_check_field' );
