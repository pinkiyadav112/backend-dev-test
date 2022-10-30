<?php
/**
 * @author     Kings Of The Web
 * @year       2022
 * @package    mazencanada.com
 * @subpackage KotwRest\Wordpress
 */

namespace KotwRest\Wordpress;

class Routes {


	public function __construct() {
		add_filter( 'post_type_link', array( $this, 'frontend_permalink' ), 99, 2 );

	}

	/**
	 * Updates the permalink for the frontend.
	 *
	 * @param string $post_link The post's permalink.
	 * @param object $post      The post in question.
	 *
	 * @return string
	 */
	public function frontend_permalink( string $post_link, object $post ): string {
		$frontend_permalink = get_field( 'frontend_url', 'option' ) . '/' . $post->post_name;

		if ( $post->post_type === 'page' ) {
			$post_link = $frontend_permalink;
		}

		return $post_link;
	}


}
