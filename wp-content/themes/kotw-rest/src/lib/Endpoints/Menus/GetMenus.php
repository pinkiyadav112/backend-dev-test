<?php
/**
 * This endpoint retrieves ALL menus available in the WordPress installation,
 *
 * Each menu should return its name, slug and items.
 *
 * Each item, should return its name, slug, url, and children (up to 2 levels).
 *
 * @author     Kings Of The Web
 * @endpoint   /wp-json/kotwreset/get-menus
 *
 */

namespace KotwRest\Endpoints\Menus;

use kotw\Rest\Endpoint;
use WP_REST_Request as WP_REST_Request;
use WP_REST_Response;

class GetMenus extends Endpoint {

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
	 * @var string|void
	 */
	public static $site_url;

	/**
	 * This initializes the endpoint's data.
	 * @return array
	 */
	public static function init(): array {
		parent::init();
		self::$public_access      = true;
		self::$same_domain_access = true;
		self::$allowed_user_roles = array( 'administrator', 'developer', 'subscriber' );
		self::$allowed_domains    = array();
		self::$site_url           = site_url();

		return array(
			'kotwrest',
			'get-menus',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'callback' ),
				'permission_callback' => array( __CLASS__, 'permission_callback' ),
			),
		);
	}


	/**
	 *  The main callback for this endpoint, that should return an array of menus
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response|array
	 */
	public static function callback( WP_REST_Request $request ) {

		// get all available menus by locations.
		$locations = get_nav_menu_locations();

		// get menus per each location.
		$menus = array();
		foreach ( $locations as $location => $menu_id ) {
			$menus[] = array(
				'location' => $location,
				'menu'     => wp_get_nav_menu_object( $menu_id ),
			);
		}

		$menus_array = array();

		// get menu items with thier children
		foreach ( $menus as $menu ) {
			$menus_array[] = self::get_menu_items( $menu['menu']->term_id );
		}

		if ( count( $menus_array ) > 0 ) {
			return self::handle_success( $menus_array );
		}

		return self::handle_error(
			'No menu was found',
			404
		);

	}


	/**
	 * Returns the menu items with their children
	 *
	 * @param $menu_id
	 *
	 * @return array
	 */
	public static function get_menu_items( $menu_id ) {
		// get menu items by keeping children structure
		$menu_items = wp_get_nav_menu_items( $menu_id );
		if ( ! is_array( $menu_items ) ) {
			return array();
		}
		$menu_items = array_map(
			function ( $item ) use ( $menu_items ) {
				return array(
					'name'     => $item->title,
					'id'       => $item->post_name,
					'url'      => str_replace( self::$site_url, '', $item->url ),
					'parent'   => $item->menu_item_parent,
					'children' => self::get_nav_menu_item_children( $item->ID, $menu_items, true ),
				);

			},
			$menu_items
		);

		// if a menu item was added as a child, it should be removed from the mai array
		$menu_items = array_filter(
			$menu_items,
			function ( $item ) {
				return $item['parent'] == 0;
			}
		);

		return array(
			'name'  => get_term( $menu_id )->name,
			'id'    => get_term( $menu_id )->slug,
			'items' => $menu_items,
		);
	}

	/**
	 *
	 * Returns all children of a menu item
	 *
	 * @param $menu_items
	 *
	 * @return array
	 */
	public static function get_nav_menu_item_children( $parent_id, $nav_menu_items, $depth = true ) {
		$nav_menu_item_list = array();
		foreach ( (array) $nav_menu_items as $nav_menu_item ) {
			if ( $nav_menu_item->menu_item_parent == $parent_id ) {
				$nav_menu_item_list[] = array(
					'title'  => $nav_menu_item->title,
					'url'    => str_replace( self::$site_url, '', $nav_menu_item->url ),
					'parent' => $nav_menu_item->menu_item_parent,
				);
				if ( $depth ) {
					if ( $children = self::get_nav_menu_item_children( $nav_menu_item->ID, $nav_menu_items ) ) {
						$nav_menu_item_list = array_merge( $nav_menu_item_list, $children );
					}
				}
			}
		}

		return $nav_menu_item_list;
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
