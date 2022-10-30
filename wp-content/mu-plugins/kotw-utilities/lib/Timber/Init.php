<?php
/**
 * @author     Kings Of The Web
 * @year       2022
 * @package    kotw
 * @subpackage kotw\Timber
 */

namespace kotw\Timber;

use \Timber\Timber as Timber;


class Init {
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Init timber after WP has been loaded.
	 *
	 * @return void
	 */
	public function init() {

		// Initialize Timber.
		if ( class_exists( '\Timber\Timber' ) ) {
			$timber = new Timber();
		}

		// Timber functions and filters.
		$this->functions_and_filters();
	}

	/**
	 * Registers the Timber custom functions and filters.
	 *
	 * @return void
	 */
	public function functions_and_filters(): void {
		new Functions();
	}
}
