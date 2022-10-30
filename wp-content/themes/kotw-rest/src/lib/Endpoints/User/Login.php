<?php
/**
 * @author     Kings Of The Web
 * @endpoint   /wp-json/kotwrest/user/login
 **
 */

namespace KotwRest\Endpoints\User;

use kotw\Authenticate;
use kotw\Logger;
use kotw\Rest\Endpoint;
use WP_REST_Request as WP_REST_Request;
use WP_REST_Response;

class Login extends Endpoint {

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
			'user/login',
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

		$email_or_username = $request->get_param( 'email_or_username' );
		$password          = $request->get_param( 'password' );
		$remember_me       = $request->get_param( 'remember_me' ) === 'on';

		if ( ! $email_or_username || ! $password ) {
			return self::handle_error(
				'Username or password are empty',
				403
			);
		}

		$authenticate_user = Authenticate::init( $email_or_username, $password, true );

		if ( $authenticate_user ) {

			// set a new cookie. for the user.
			wp_set_auth_cookie( $authenticate_user->ID, $remember_me, true );

			// return the last session token in user's sessions list and its key.
			$session_tokens = get_user_meta( $authenticate_user->ID, 'session_tokens', true );

			if ( ! $session_tokens ) {
				return self::handle_error(
					'No session tokens found',
					403
				);
			}

			$session_tokens_keys    = array_keys( $session_tokens );
			$last_session_token_key = end( $session_tokens_keys );

			if ( $last_session_token_key ) {
				$response = array(
					'user_id'           => $authenticate_user->ID,
					'session_token_key' => $last_session_token_key,
					'session_token'     => $session_tokens[ $last_session_token_key ],
				);

				return self::handle_success( $response );
			}
		}

		return self::handle_error(
			'Something wrong happened.',
			400
		);

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
