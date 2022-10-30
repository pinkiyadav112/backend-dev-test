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
		// book
		$book_post_type = new PostType( 
			'book',
			'Book',
			'Books',
			array( 'book-category' ),
			array(
				'title',
				'thumbnail',
				'editor',
				'excerpt',
				'revisions',
			),
			'dashicons-book',
			true,
			array(),
			'Books'
		);
	}
}
