<?php
/**
 * @author     Kings Of The Web
 * @year       2022
 * @package    mazencanada.com
 * @subpackage KotwRest\Wordpress
 */

namespace KotwRest\Wordpress;

use kotw\Custom\PostType;

class PostTypes {

	public function __construct() {
		self::register();
		self::books();
	}

	/**
	 * This registers all post types requireed.
	 *
	 * @return void
	 */
	public static function register() {
		// example
		$example_post_type = new PostType(
			'project',
			'Project',
			'Projects',
			array( 'project-category' ),
			array(
				'title',
				'thumbnail',
				'editor',
				'excerpt',
				'revisions',
			),
			'dashicons-welcome-learn-more',
			true,
			array(),
			'Projects'
		);

	}
	
	
	public static function books() {
		// example
		$example_post_type = new PostType(
			'books',
			'books',
			'books',
			array( 'books-category' ),
			array(
				'title',
				'thumbnail',
				'editor',
				'excerpt',
				'revisions',
			),
			'dashicons-welcome-learn-more',
			true,
			array(),
			'books'
		);

	}
	
	
	
}
