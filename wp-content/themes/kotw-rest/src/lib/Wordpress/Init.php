<?php
/**
 * @author     Kings Of The Web
 * @year       2022
 * @package    mazencanada.com
 * @subpackage KotwRest\Wordpress
 */

namespace KotwRest\Wordpress;

class Init {

	public function __construct() {
		// Init Theme.
		new Theme();

		// Init Post Types.
		new PostTypes();

		// Init Taxonomies.
		new Taxonomies();

		// Init Custom Routes.
		new Routes();

		// Init Custom Redirects.
		new Redirects();

		// Init Custom REST functions.
		new Rest();

		// Init Custom Users functions.
	}
}
