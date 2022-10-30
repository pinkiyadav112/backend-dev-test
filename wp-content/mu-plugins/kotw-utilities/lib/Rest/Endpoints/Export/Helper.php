<?php
/**
 * @author     Kings Of The Web
 * @year       2022
 * @package    kotw-base
 * @subpackage kotw\Rest\Endpoints\Export
 */

namespace kotw\Rest\Endpoints\Export;

class Helper {

	/**
	 * This should generate the latest assets's url, to download.
	 *
	 * @return string|false
	 */
	public static function get_latest_export_file( $type ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'kotw_hashed_exports';
		$file       = $wpdb->get_results( "select * from $table_name where file_path like '%/$type%' order by id desc limit 1" );
		if ( count( $file ) < 1 ) {
			return false;
		}
		$ext = $type === 'assets' ? '.tar.gz' : '.sql';
		return $file[0]->file_name . $ext;
	}

}
