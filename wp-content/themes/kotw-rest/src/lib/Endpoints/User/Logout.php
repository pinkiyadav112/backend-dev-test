<?php
/**
 * @author     Kings Of The Web
 * @endpoint   /wp-json/kotwrest/user/logout/<user_id>
 **
 */

namespace KotwRest\Endpoints\User;

use kotw\Rest\Authorize;
use kotw\Rest\Endpoint;
use WP_REST_Request as WP_REST_Request;
use \WP_REST_Response as WP_REST_Response;
use \WP_Session_Tokens as WP_Session_Tokens;
use \WP_User as WP_User;

class Logout extends Endpoint {

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
			'user/logout/(?P<user_id>[\d]+)',
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
	 * @return WP_REST_Response|array
	 */
	public static function callback( WP_REST_Request $request ){
		$user_id = $request->get_param( 'user_id' );
		$user    = get_user_by( 'id', $user_id );
		if ( ! $user instanceof WP_User ) {
			return self::handle_error( 'User not found.', 404 );
		}

		// logout the user by id.
		$session_manager = WP_Session_Tokens::get_instance( $user_id );
		if ( ! $session_manager ) {
			self::handle_error( "Session manager not found for user $user_id", 404 );
		}
		$session_manager->destroy_all();

		// Check if there are any sessions available.
		$available_tokens = $session_manager->get_all();
		if ( ! empty( $available_tokens ) ) {
			return self::handle_error( 'Could not logout the user. There are still sessions available.', 500 );
		}

		return self::handle_success(
			array(
				'user_id' => $user_id,
				'result'  => 'User logged out successfully.',
			),
			200
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
			$user_id = $request->get_param( 'user_id' );

			// This is the user returned from the token.
			$token_user_id = Authorize::user_token( $request->get_headers() );

			// Check if both are the same.
			return (int) $token_user_id === (int) $user_id;
		}

		return false;
	}
}

