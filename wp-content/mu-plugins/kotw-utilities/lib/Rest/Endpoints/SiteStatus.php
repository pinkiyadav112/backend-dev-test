<?php

namespace kotw\Rest\Endpoints;

use kotw\Rest\Endpoint;
use WP_REST_Request as WP_REST_Request;

/**
 * @author     Kings Of The Web
 * @year       2022
 * @package    kotw-base
 * @subpackage kotw\Rest\Endpoints
 *
 * @endpoint: /{wp-json}/kotw/siteStatus
 */
class SiteStatus extends Endpoint {
	/**
	 * @var false
	 */
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
	 * This initializes the endpoint's data.
	 * @return array
	 */
	public static function init(): array {

		parent::init();
		self::$public_access      = false; // This endpoint is not open to the public.
		self::$allowed_user_roles = array( 'administrator', 'developer' );

		return array(
			'kotw',
			'sitestatus',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'callback' ),
				'permission_callback' => array( __CLASS__, 'permission_callback' ),
			),
		);
	}


	public static function callback( WP_REST_Request $request ) {

		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		require_once ABSPATH . 'wp-admin/includes/update.php';

		$plugin_updates = array();

		$plugin_updates_full = json_decode( wp_json_encode( get_plugin_updates() ), true );
		$core_updates_full   = get_core_updates( array( 'dismissed' => true ) );

		foreach ( $plugin_updates_full as $key => $update ) {
			$plugin_updates[] = array(
				'name'       => $update['Name'],
				'oldVersion' => $update['Version'],
				'newVersion' => $update['update']['new_version'],
			);
		}

		return array(
			'pluginUpdatesAvailable' => $plugin_updates,
			'coreUpdatesAvailable'   => $core_updates_full ?? null,
		);
	}

	public static function permission_callback( WP_REST_Request $request ) {
		$logged_in_user = self::verify_access( $request, __CLASS__ );
		if ( $logged_in_user ) {
			return true;
		}

		return false;
	}
}
