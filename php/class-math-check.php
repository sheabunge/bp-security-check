<?php

namespace Shea\BP_Security_Check;

class Math_Check {

	/**
	 * Holds the instance of the plugin class
	 * @var Plugin
	 */
	private $plugin;

    public $prefix = 'bp-security-check_';

    function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
    }

    function run() {
        add_action( 'bp_after_signup_profile_fields', array( $this, 'render' ) );
        add_action( 'bp_signup_validate', array( $this, 'validate' ) );
    }

    /**
     * Check if the user's input was correct
     */
    function validate() {
    	global $bp;

    	$uid = $_POST['bp-security-check-uid'];
    	$sum = get_transient( $this->prefix . $uid );

    	$a  = $sum[0];
    	$op = $sum[1];
    	$b  = $sum[2];

    	$answer = intval( $_POST['bp-security-check'] );

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
    		delete_transient( $this->prefix . $uid );
    	}
    }

    /**
     * Render the input fields
     */
    function render() {

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
    			$a = $b;      // assign $a (lower number) to $b (higher number)
    			$b = $_a;     // assign $b to the original $a
    			unset( $_a ); // destroy the backup variable
    		}

    		/* If the numbers are equal then the result will be zero, which will cause an error */
    		elseif ( $a == $b ) {
    			$a++;
    		}
    	}

    	/* Generate a unique ID to save the sum information under */
    	$uid = uniqid();

    	/* Save sum information (expiry = 12 hours) */
    	set_transient( $this->prefix . $uid, array( $a, $op, $b ), 12 * HOUR_IN_SECONDS );

    	?>
    	<div style="float: left; clear: left; width: 48%; margin: 12px 0;" class="security-question-section">
    		<h4><?php esc_html_e( 'Security Question', 'bp-security-check' ); ?></h4>
    		<?php do_action( 'bp_security_check_errors' ); ?>
    		<label for="bp-security-check" style="display: inline;">
    			<?php

    			// &#61; = equals
    			// &#43; = addition
    			// &#8722; = subtraction

    			// a + b =
    			printf(
    				'%1$d %3$s %2$d &#61;',
    				$a, $b,
    				'sub' == $op ? '&#8722;' : '&#43;'
    			);

    			?>
    		</label>
    		<input type="hidden" name="bp-security-check-uid" value="<?php echo $uid; ?>">
    		<input type="number" name="bp-security-check" id="bp-security-check" required="required">
    	</div>
    	<?php
    }
}
