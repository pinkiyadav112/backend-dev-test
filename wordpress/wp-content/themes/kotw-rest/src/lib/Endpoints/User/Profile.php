<?php
/**
 * Returns Profile data for a user based on their ID.
 *
 * @author     Kings Of The Web
 * @endpoint   /wp-json/kotwrest/user/profile/<user_id>
 **
 */

namespace KotwRest\Endpoints\User;

use kotw\Rest\Authorize;
use kotw\Rest\Endpoint;
use \WP_REST_Request as WP_REST_Request;
use \WP_REST_Response as WP_REST_Response;
use \WP_User as WP_User;

class Profile extends Endpoint {

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
			'user/profile/(?P<user_id>[\d]+)',
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
	public static function callback( WP_REST_Request $request ): WP_REST_Response|array {
		$user_id = $request->get_param( 'user_id' );
		$user    = get_user_by( 'id', $user_id );

		if ( ! $user instanceof WP_User ) {
			return self::handle_error( 'User not found.', 404 );
		}

		$user_array = array(
			'ID'           => $user->ID,
			'user_login'   => $user->user_login,
			'user_email'   => $user->user_email,
			'display_name' => $user->display_name,
			'first_name'   => $user->first_name,
			'last_name'    => $user->last_name,
			'nickname'     => $user->nickname,
			'roles'        => $user->roles,
			'meta'         => self::get_user_info( $user->ID ),
		);
		$user_info  = apply_filters( 'kotwrest_user_profile', $user_array, $user->ID );

		return self::handle_success( $user_info );
	}

	/**
	 * This retrieves most user custom info from user meta.
	 *
	 * @param $user_id
	 *
	 * @return array
	 */
	public static function get_user_info( $user_id ) {
		$meta_array = array(
			'avatar'      => get_user_meta( $user_id, 'avatar', true ),
			'bio'         => get_user_meta( $user_id, 'description', true ),
			'zoho'        => get_user_meta( $user_id, 'kotw_zoho_contact_id', true ),
			'last_login'  => get_user_meta( $user_id, '_kotw_last_login', false ),
			'last_logout' => get_user_meta( $user_id, '_kotw_last_logout', false ),
		);

		return apply_filters( 'kotwrest_user_profile_meta', $meta_array, $user_id );

	}

	/**
	 * This function is called before the callback, and it validates the request.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return bool
	 */
	public static function permission_callback(
		WP_REST_Request $request
	): bool {
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
