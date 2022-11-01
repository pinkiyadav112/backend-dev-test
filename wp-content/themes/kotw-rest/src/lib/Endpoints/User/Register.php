<?php
/**
 * This registers a new user to the WordPress database
 *
 * @author     Kings Of The Web
 * @endpoint   /wp-json/kotwrest/user/register
 **
 */

namespace KotwRest\Endpoints\User;

use kotw\Authenticate;
use kotw\Rest\Endpoint;
use WP_REST_Request as WP_REST_Request;
use WP_REST_Response;

class Register extends Endpoint {

	public static bool $public_access;
	/**
	 * @var string[]
	 */
	public static array $allowed_user_roles;
	/**
	 * @var string[]
	 */
	public static array $allowed_domains;

	/**
	 * @var bool
	 */
	public static bool $same_domain_access;

	/**
	 * This initializes the endpoint's data.
	 * @return array
	 */
	public static function init(): array {
		parent::init();
		self::$public_access      = true;
		self::$same_domain_access = true;
		self::$allowed_user_roles = array( 'administrator', 'developer' );
		self::$allowed_domains    = array( 'localhost' );

		return array(
			'kotwrest',
			'user/register',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'callback' ),
				'permission_callback' => array( __CLASS__, 'permission_callback' ),
			),
		);
	}


	/**
	 *  The main callback for this endpoint.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return array|WP_REST_Response
	 */
	public static function callback( WP_REST_Request $request ) {

		$email    = $request->get_param( 'email' );
		$password = $request->get_param( 'password' );

		// Check if the user exists
		$user = get_user_by( 'email', $email );

		if ( $user instanceof \WP_User ) {
			return self::handle_error(
				'User already exists',
				400
			);
		}

		// check if the email is valid
		if ( ! is_email( $email ) ) {
			return self::handle_error(
				'Invalid email',
				400
			);
		}

		// generate unique username from email.
		$username = explode( '@', $email );
		$username = $username[0];

		// check if username exists
		$username = self::does_user_exist( $username );

		// Create new user by email and password.
		$user_id = wp_create_user( $username, $password, $email );

		// Check if user was created.
		if ( is_wp_error( $user_id ) ) {
			return self::handle_error(
				json_encode( array( $user_id->get_error_messages() ) ),
				400
			);
		}


		return self::handle_success(
			array(
				'result' => 'New user has been created',
				'user'   => get_user_by( 'id', $user_id ),
			)
		);

	}

	/**
	 * Keeps checking if the username exists, and adds random numbers to the end of it until it doesn't.
	 *
	 * @param $username
	 *
	 * @return void
	 */
	public static function does_user_exist( $username ) {
		$user = get_user_by( 'login', $username );
		if ( $user instanceof \WP_User ) {
			// if username exists, add a random number to the end of it.
			$username = $username . rand( 1, 1000 );
			self::does_user_exist( $username );
		}

		return $username;
	}

	/**
	 * This function is called before the callback, and it validates the request.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return bool
	 */
	public static function permission_callback( WP_REST_Request $request ): bool {
		// Returns a user, if current it is a valid request.
		$user_array = self::verify_access( $request, __CLASS__ );
		if ( $user_array ) {
			return true;
		}

		return false;
	}
}
