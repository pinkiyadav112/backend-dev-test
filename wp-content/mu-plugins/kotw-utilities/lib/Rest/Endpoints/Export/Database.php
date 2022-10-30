<?php

namespace kotw\Rest\Endpoints\Export;

use kotw\Authenticate;
use kotw\Rest\Endpoint;
use WP_REST_Request as WP_REST_Request;

/**
 * @author     Kings Of The Web
 * @year       2022
 * @package    kotw-base
 * @subpackage kotw\Rest\Endpoints\Export
 *
 * @endpoint: /{wp-json}/kotw/exportAssets
 */
class Database extends Endpoint {

	/**
	 * @var bool
	 */
	public static bool $public_access;
	/**
	 * @var string[]
	 */
	public static array $allowed_user_roles;


	/**
	 * This initializes the endpoint's data.
	 * @return array
	 */
	public static function init(): array {
		parent::init();
		self::$public_access      = true;
		self::$allowed_user_roles = array( 'administrator', 'developer' );

		return array(
			'kotw',
			'exportDatabase',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'callback' ),
				'permission_callback' => array( __CLASS__, 'permission_callback' ),
			),
		);
	}


	public static function callback( WP_REST_Request $request ) {
		$username      = $request->get_param( 'username' ) ?? null;
		$password      = $request->get_param( 'password' ) ?? null;
		$real_password = $request->get_param( 'real_password' ) ?? null;
		$authenticate  = Authenticate::init( $username, $password, $real_password );

		if ( ! $authenticate ) {
			return 'Not Authorized!';
		}

		$admin_request = false;
		// Check if the current user has the ability to view the requested user.
		$intersect_current = array_intersect( $authenticate->roles, self::$allowed_user_roles );
		if ( $intersect_current && count( $intersect_current ) > 0 ) {
			$admin_request = true;
		}

		if ( ! $admin_request ) {
			return 'Not enough permission, Check your user role!';
		}

		$file_name = Helper::get_latest_export_file( 'db' );
		return $file_name ?? 'Fail, export does not exist.';

	}

	public static function permission_callback( WP_REST_Request $request ) {
		$logged_in_user = self::verify_access( $request, __CLASS__ );
		if ( $logged_in_user ) {
			return true;
		}

		return false;
	}

}
