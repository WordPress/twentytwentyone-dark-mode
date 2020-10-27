<?php
/**
 * Plugin Name: TwentyTwentyOne Dark Mode
 *
 * @package wordpress/twentytwentyone-dark-mode
 */

add_action( 'after_setup_theme', function() {
	if ( ! function_exists( 'twenty_twenty_one_setup' ) ) {
		return;
	}
	include 'functions.php';
} );
