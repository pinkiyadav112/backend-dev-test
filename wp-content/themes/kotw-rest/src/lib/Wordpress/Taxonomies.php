<?php
/**
 * @author     Kings Of The Web
 * @year       2022
 * @package    mazencanada.com
 * @subpackage KotwRest\Wordpress
 */

namespace KotwRest\Wordpress;

use kotw\Custom\Taxonomy;

class Taxonomies {
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
		$example_taxonomy = new Taxonomy(
			'project-title',
			'Project Group',
			'Projects Groups',
			array( 'project', 'lesson' ),
			true,
			true,
			array(),
			'Projects Groups'
		);
		// book
		$book_taxonomy = new Taxonomy(
			'book-category',
			'Book Category',
			'Book Categories',
			array( 'book' ),
			true,
			true,
			array(),
			'Book Categories'
		);
	}
}
