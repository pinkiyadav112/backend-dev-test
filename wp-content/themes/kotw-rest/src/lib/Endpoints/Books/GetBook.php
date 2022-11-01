<?php
/**
 * @author     Kings Of The Web
 * @endpoint   wp-json/kotwrest/books/get-book/<book-id>
 **
 */

namespace KotwRest\Endpoints\Books;

use kotw\Authenticate;
use kotw\Logger;
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
		self::$allowed_user_roles = array( 'administrator', 'developer' );
		self::$allowed_domains    = array( 'localhost' );

		return array(
			'kotwrest',
			'books/get-book/(?P<book_id>[\d]+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'callback' ),
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


    $book_id = $request->get_param( 'book_id' );
    $result = array();
  
    
    $args = array('p' => $book_id, 'post_type' => 'books');
    $loop = get_posts($args);
        if (empty($loop)) {
           
       return self::handle_error( "Record not found", 404 );
   // return $response;
        
        }
    else{
        $attachment_id = get_post_meta( $book_id, 'image', true );
        $img_atts = wp_get_attachment_image_src( $attachment_id, "full" );
        $img_src = $img_atts[0];
        $result['ID'] = $loop[0]->ID;
        $result['title'] = get_post_meta( $book_id, 'title', true );
        $result['author'] = get_post_meta( $book_id, 'author', true );
        $result['description'] = get_post_meta( $book_id, 'description', true );
        $result['price'] = get_post_meta( $book_id, 'price', true );
        $result['image'] = $img_src;
        $result['link'] = get_permalink($book_id);
        
    
        return self::handle_success( $result );
        
    }


	/*	return self::handle_error(
			'Something wrong happened.',
			400
		);*/
		
		

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
