<?php

namespace Shea\BP_Security_Check;

/**
 * Handles the legacy math sum check
 * @package Shea\BP_Security_Check
 */
class Math_Check extends Security_Check {

	/**
     * Prefix to use for storing options
	 * @var string
	 */
	public $prefix = 'bp-security-check_';

	/**
	 * Check if the user's input was correct
	 */
	function validate() {
		$uid = $_POST['bp-security-check-uid'];
		$sum = get_transient( $this->prefix . $uid );

		$a  = $sum[0];
		$op = $sum[1];
		$b  = $sum[2];

		$answer = intval( $_POST['bp-security-check'] );

		/* Calculate the actual answer */
		if ( 'sub' == $op ) {
			$result = $a - $b;
		} else {
			$result = $a + $b;
		}

		/* The submitted answer was incorrect */
		if ( $result !== $answer ) {
			return __( 'Sorry, please answer the question again', 'bp-security-check' );
		} /* The answer field wasn't filled in */
		elseif ( empty( $answer ) ) {
			return __( 'This is a required field', 'bp-security-check' );
		} /* Clean up the transient if the answer was correct */
		else {
			delete_transient( $this->prefix . $uid );
		}

		return '';
	}

	/**
     * Generate a new security check question
	 * @return array
	 */
	private function generate_question() {
		/* Get a random number between 0 and 10 (inclusive) */
		$a = mt_rand( 0, 10 );
		$b = mt_rand( 0, 10 );

		/* Get a random operation */
		$op = mt_rand( 0, 100 ) > 50 ? 'add' : 'sub';

		/* Make adjustments to the numbers for subtraction */
		if ( 'sub' === $op ) {

			/* Make sure that $a is greater then $b; if not, switch them */
			if ( $b > $a ) {
				$_a = $a;     // backup $a
				$a  = $b;     // assign $a (lower number) to $b (higher number)
				$b  = $_a;    // assign $b to the original $a
				unset( $_a ); // destroy the backup variable
			} /* If the numbers are equal then the result will be zero, which will cause an error */
			elseif ( $a == $b ) {
				$a ++;
			}
		}

		/* Generate a unique ID to save the sum information under */
		$uid = uniqid();

		/* Save sum information (expiry = 12 hours) */
		set_transient( $this->prefix . $uid, array( $a, $op, $b ), 12 * HOUR_IN_SECONDS );

		/* Generate formatted sum */

		// &#61; = equals
		// &#43; = addition
		// &#8722; = subtraction

		// a + b =
		$sum = sprintf(
			'%1$d %3$s %2$d &#61;',
			$a, $b,
			'sub' == $op ? '&#8722;' : '&#43;'
		);

		return array(
			'a'   => $a,
			'b'   => $b,
			'op'  => $op,
			'sum' => $sum,
			'uid' => $uid
		);
	}

	/**
	 * Render the input fields
	 */
	function render() {}

	/**
	 * Render the input fields
	 */
	public function render_register() {
    	$question = $this->generate_question();

	    ?>
        <div style="float: left; clear: left; width: 48%; margin: 12px auto;" class="security-question-section">
            <h4><?php esc_html_e( 'Security Question', 'bp-security-check' ); ?></h4>
            <label for="bp-security-check">
                <?php echo $question['sum']; ?>
            </label>
            <input type="hidden" name="bp-security-check-uid" value="<?php echo $question['uid']; ?>">
            <input type="number" name="bp-security-check" id="bp-security-check" required="required">
        </div>

		<?php
	}

	/**
	 * Render the input fields
	 */
	public function render_login() {
		$question = $this->generate_question();

		?>
        <p>
            <label for="bp-security-check">
                Security Check<br><strong><?php echo $question['sum']; ?></strong>
                <input type="number" name="bp-security-check" class="input" id="bp-security-check" required="required">
                <input type="hidden" name="bp-security-check-uid" value="<?php echo $question['uid']; ?>">
            </label>
        </p>
		<?php
	}
}
