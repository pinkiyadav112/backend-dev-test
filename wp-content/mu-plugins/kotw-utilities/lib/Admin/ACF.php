<?php
/**
 * @author     Kings Of The Web
 * @year       2022
 * @package    english.codedegree.com
 * @subpackage kotw\Admin
 */

namespace kotw\Admin;

class ACF {

	public function __construct() {
		add_filter( 'acf/load_field/name=lottie_json_file', array( $this, 'lottie_json_file_label' ), 10, 1 );

	}

	/**
	 * @param $field
	 *
	 * @return mixed
	 */
	public function lottie_json_file_label( $field ) {
		// update the instructions.
		$field['instructions'] = "Go to: <a href='" . site_url( 'wp-content/mu-plugins/kotw-utilities/assets/lottie-files/available.php' ) . "' target='__blank'>Available Animations</a>, select the animation you want and copy the name of the file. Paste it here.";

		return $field;
	}

}
