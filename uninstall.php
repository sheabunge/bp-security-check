<?php

/* Ensure this plugin is actually being uninstalled */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

delete_option( 'bp_security_check_type' );
delete_option( 'bp_security_check_recaptcha_site_key' );
delete_option( 'bp_security_check_recaptcha_secret_key' );
