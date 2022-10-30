<?php
/**
 * @author     Kings Of The Web
 * @year       2022
 * @package    mazencanada.com
 * @subpackage KotwRest\ACF
 */

namespace KotwRest\ACF;

class Init {

	public function __construct() {
		// ACF Custom Hooks.
		new Hooks();

		// Options Pages.
		new OptionsPages();
	}

}
