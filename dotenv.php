<?php
/**
 * Plugin Name: dotenv
 * Description: Set WordPress options from a .env file.
 * Version:     1.0.1
 * Author:      Brad Parbs
 * Author URI:  https://bradparbs.com/
 * License:     GPLv2
 * Text Domain: dotenv
 * Domain Path: /lang/
 *
 * @package dotenv
 */

namespace DotEnvWP;

use \Dotenv;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\\dotenv_init', 1 );

/**
 * Kick it off.
 */
function dotenv_init() {

	require_once __DIR__ . '/vendor/autoload.php';

	// Grab all the values to process.
	$envs = load_and_get_env();

	if ( ! is_array( $envs ) ) {
		return;
	}

	// Loop through all the envs and load them up.
	foreach ( $envs as $key => $value ) {

		if ( check_if_our_env( $key ) ) {
			filter_option_with_env( str_ireplace( get_key_prefix() . '_', '', $key ), $value );

			// If the env var isn't in lowercase, then process that.
			if ( strtolower( $key ) === $key ) {
				filter_option_with_env( str_ireplace( get_key_prefix() . '_', '', strtolower( $key ) ), $value );
			}
		}
	}
}

/**
 * Get the location of the .env file.
 *
 * @return string Path to .env file.
 */
function get_env_location() {

	// Allow filtering the full location to short circuit normal checks.
	$location = apply_filters( 'dotenv_location', false );

	// If the filter was set, then use that, otherwise find the file.
	return $location ? $location : find_env_file();
}

/**
 * Find the location of a .env file.
 *
 * @return string Path to .env file.
 */
function find_env_file() {

	// List of locations to check for the existence of a .env file.
	$locations = apply_filters(
		'dotenv_locations_to_check',
		[
			ABSPATH,
			dirname( ABSPATH ),
			WP_CONTENT_DIR,
		]
	);

	// Loop through and find our file.
	foreach ( $locations as $location ) {
		if ( file_exists( trailingslashit( $location ) . '.env' ) ) {
			return $location;
		}
	}

	return false;
}

/**
 * Load the env file & get the values.
 *
 * @return array Env values.
 */
function load_and_get_env() {

	// Grab the location of our env file and check it.
	$location = get_env_location();
	if ( ! $location ) {
		return false;
	}

	// Load our .env file and return it as an array.
	return Dotenv\Dotenv::createArrayBacked( $location )->load();
}

/**
 * Check to see if an env is something we want to process.
 *
 * @param string $key Key to check.
 *
 * @return bool Whether or not it's valid.
 */
function check_if_our_env( $key ) {
	return ( 0 === stripos( $key, get_key_prefix() ) );
}

/**
 * Get the key prefix for the .env vars.
 *
 * @return string Prefix.
 */
function get_key_prefix() {
	// Allow filtering the prefix of the key to check.
	return apply_filters( 'wpdotev_key_prefix', 'WPENV' );
}

/**
 * Filter options using env values.
 *
 * @param string $name  Name of option.
 * @param string $value Value.
 */
function filter_option_with_env( $name, $value ) {
	add_filter(
		"pre_option_{$name}",
		function () use ( $value ) {
			return $value;
		}
	);
}
