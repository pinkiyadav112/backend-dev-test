<?php
/**
 * This endpoint retrieves all the required information of a book using its id.
 *
 *
 * @author     Kings Of The Web
 * @endpoint   /wp-json/kotwrest/books/get-book/{id}
 *
 */

namespace KotwRest\Endpoints\Books;

use kotw\Rest\Endpoint;
use WP_REST_Request as WP_REST_Request;
use WP_REST_Response;

class GetBook extends Endpoint {

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
			'books/get-book/(?P<book_id>[\d]+)',
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

		// get book id.
		$book_id = $request->get_param( 'book_id' );

		if ( ! $book_id ) {
			return self::handle_error(
				'No book id was provided',
				400
			);
		}

		// get book by book id.
		$book = get_post( $book_id );
		if( ! get_post_status( $book_id ) ) {
			return self::handle_error(
				'No book id was found',
				404
			);
		}

		return self::handle_success(
			array(
				'id'        => $book->ID,
				'title'     => $book->post_title,
				'permalink' => $book->permalink,
				'meta'      => get_fields( $book_id ),
			)
		);
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
