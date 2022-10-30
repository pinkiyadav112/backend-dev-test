<?php
/**
 * This endpoint retrieves all the required information of a page using its slug.
 *
 *
 * @author     Kings Of The Web
 * @endpoint   /wp-json/kotwreset/get-page/{slug}
 *
 */

namespace KotwRest\Endpoints\Pages;

use kotw\Rest\Endpoint;
use WP_REST_Request as WP_REST_Request;
use WP_REST_Response;

class GetPage extends Endpoint {

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
		self::$allowed_user_roles = array( 'administrator', 'developer', 'subscriber' );
		self::$allowed_domains    = array();

		return array(
			'kotwrest',
			'get-page/(?P<slug>[a-zA-Z0-9-]+)',
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

		// get page slug.
		$slug = $request->get_param( 'slug' );

		if ( ! $slug ) {
			return self::handle_error(
				'No slug was provided',
				400
			);
		}

		// get page by slug.
		$page = get_page_by_path( $slug );
		if ( ! $page ) {
			return self::handle_error(
				'No page was found with this slug',
				404
			);
		}

		return self::handle_success(
			array(
				'id'        => $page->ID,
				'title'     => $page->post_title,
				'thumbnail' => get_the_post_thumbnail_url( $page->ID ),
				'blocks'    => self::parse_blocks_from_content( $page->post_content, $page->ID ),
				'meta'      => self::get_page_options( $page->ID ),
			)
		);

	}

	/**
	 *
	 * This parse the page's content, and returns an array of ACF blocks' data.
	 *
	 * @param $content
	 *
	 * @return array
	 */
	public static function parse_blocks_from_content( $content, $page_id ): array {
		if ( empty( $content ) ) {
			return array();
		}

		// parse the content, to get the values of all gutenburg blocks.
		$blocks        = parse_blocks( $content );
		$blocks_parsed = $blocks;

		foreach ( $blocks as $key => $block ) {
			$block_data = $block['attrs'] ? $block['attrs']['data'] : array();
			unset( $blocks_parsed[ $key ]['attrs'] );

			$formatted_block_data = array();
			foreach ( $block_data as $field_key => $field_value ) {
				if ( strpos( $field_key, '_' ) === 0 ) {
					continue;
				}
				$field_object                       = get_field_object( $block_data[ '_' . $field_key ] );
				$formatted_block_data[ $field_key ] = array(
					'parent'      => $field_object['parent'],
					'parent_name' => get_field_object( $field_object['parent'] ) ? get_field_object( $field_object['parent'] )['name'] : '',
					'value'       => $field_value,
					'key'         => $block_data[ '_' . $field_key ],
					'name'        => $field_object['name'],
				);
			}

			$blocks_parsed[ $key ]['data'] = $formatted_block_data;
		}

		return $blocks_parsed;

	}

	/**
	 * This returns an array of all the page's acf meta data, and can be overridden by the child theme.
	 *
	 * @param $page_id
	 *
	 * @return array
	 */
	public static function get_page_options( $page_id ): array {
		$meta = get_fields( $page_id );
		$meta = is_array($meta) ? $meta : array();

		return apply_filters( 'kotwrest_get_page_options', $meta, $page_id );
	}

	/**
	 * @param $grouped_fields
	 * @param $field_key
	 * @param $field
	 *
	 * @return array
	 */
	public static function recursive_acf_grouping( $formatted_block_data ): array {
		foreach ( $formatted_block_data as $field ) {
			//does it have a parent?
			if ( $field['parent'] ) {
				//does the parent exist in the array?
				if ( ! isset( $formatted_block_data_grouped[ $field['parent_name'] ] ) ) {
					$formatted_block_data_grouped[ $field['parent_name'] ] = array();
				}
				$formatted_block_data_grouped[ $field['parent_name'] ][ $field['name'] ] = $field['value'];
			} else {
				$field_key                                  = $field['name'] ?? 'parent';
				$formatted_block_data_grouped[ $field_key ] = $field['value'];
			}
		}

		return $formatted_block_data_grouped;
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
