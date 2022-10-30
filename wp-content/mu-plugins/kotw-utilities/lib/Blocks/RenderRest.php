<?php
/**
 * @author     Kings Of The Web
 * @year       2022
 * @package    english.codedegree.com
 * @subpackage codedegree\Blocks
 */

namespace kotw\Blocks;

class RenderRest {

	/**
	 * This should render nothing more than a message that states the headless nature of the block.
	 *
	 * @param $args
	 *
	 * @return bool|string
	 */
	public static function template( $args ) {

		if ( ! is_array( $args ) ) {
			return false;
		}
		if ( ! isset( $args['name'] ) ) {
			return false;
		}

		return 'Headless Block can only be viewed on the front-end.';
	}

}
