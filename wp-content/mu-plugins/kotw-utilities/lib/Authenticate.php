<?php

namespace kotw;

use WP_User as WP_User;
use WP_Error as WP_Error;

class Authenticate {


	/**
	 * @var
	 */
	private static $username;

	/**
	 * @var
	 */
	private static $password;

	/**
	 * @var
	 */
	private static $real_password;

	/**
	 * @var array
	 */
	private static array $errors;


	public static function init( $username, $password, $real_password = false ) {
		self::$username      = $username ?? null;
		self::$password      = $password ?? null;
		self::$real_password = $real_password;
		self::$errors        = array();

		if ( self::$real_password ) {
			return self::verify_real_password();
		}

		return self::verify_application_password();
	}

	/**
	 *
	 * Verified an application password that is added in the CMS.
	 * Read: https://make.wordpress.org/core/2020/11/05/application-passwords-integration-guide/
	 *
	 * @return false|WP_User
	 */
	public static function verify_application_password() {
		if ( self::$username === null || self::$password === null ) {
			self::$errors['verify_application_password'][] = 'Username or Password are null.';
			self::log_errors();

			return false;
		}

		$is_user = get_user_by( 'login', self::$username );
		if ( ! $is_user ) {
			self::$errors['verify_real_password'][] = 'User does not exist.';
			self::log_errors();
		}

		// Treat the current request as an api request.
		add_filter( 'application_password_is_api_request', '__return_true' );

		$user = wp_authenticate_application_password( null, self::$username, self::$password );

		if ( $user instanceof WP_User ) {
			return $user;
		}

		if ( $user instanceof WP_Error ) {
			self::$errors['verify_real_password'][] = $user->get_error_message();
			self::log_errors();
		}

		return false;

	}

	/**
	 * Verifies an actual real password.
	 * @return false|WP_User
	 */
	public static function verify_real_password() {
		if ( self::$username === null || self::$password === null ) {
			self::$errors['verify_real_password'][] = 'Username or Password are null.';
			self::log_errors();

			return false;
		}

		$is_user = get_user_by( 'login', self::$username );
		if ( ! $is_user ) {
			self::$errors['verify_real_password'][] = 'User does not exist.';
			self::log_errors();
		}

		$user = wp_authenticate_username_password( null, self::$username, self::$password );

		if ( $user instanceof WP_User ) {
			return $user;
		}

		if ( $user instanceof WP_Error ) {
			self::$errors['verify_real_password'][] = $user->get_error_message();
			self::log_errors();
		}

		return false;
	}

	/**
	 * This reports to the logger if there are any errors collected before.
	 * @return void
	 */
	public static function log_errors() {
		if ( count( self::$errors ) > 0 ) {
			new Logger( __CLASS__, self::$errors, '$errors' );
		}
	}
}
