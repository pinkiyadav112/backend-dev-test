<?php
/**
 * This should have all functions that handle add/removing files.
 *
 * @author     Kings Of The Web
 * @year       2022
 * @package    kotw-base
 * @subpackage kotw\Admin\Crons
 */

namespace kotw\Admin\Crons;

use Exception;
use kotw\Logger;

class FilesHandler {

	/**
	 * This removes a file from the exports directory and its relevance in the db's table.
	 *
	 * @param $table_name
	 * @param $id
	 * @param $file_path
	 *
	 * @return bool
	 */
	public static function remove_file( $table_name, $id, $file_name, $file_path ): bool {
		// Remove the file.
		\exec( "cd $file_path && rm -rf $file_name" );

		// Check if file was actually deleted.
		if ( file_exists( $file_path . '/' . $file_name ) ) {
			new Logger(
				__CLASS__,
				"File: $file_path/$file_name was not deleted.",
				'remove_file'
			);

			return false;
		}

		global $wpdb;
		$delete = $wpdb->delete(
			$table_name,
			array(
				'id' => $id,
			)
		);
		if ( ! $delete ) {
			new Logger( __CLASS__, $wpdb->show_errors, 'export_db: $wpdb->show_errors' );

			return false;
		}

		return true;
	}


	/**
	 * This exports a file at the moment that this method is called.
	 * It saves the file name as a hashed string.
	 * It saves the file to specific path.
	 * It saves both information to the db in this table: $wpdb->prefix . 'kotw_hashed_exports';
	 *
	 * @param $table_name
	 * @param $type
	 *
	 * @return bool
	 */
	public static function export_file( $table_name, $type ): bool {
		$insert = self::generate_insert_array( $type );
		if ( ! is_array( $insert ) ) {
			return false;
		}
		$file_path = $insert['file_path'];
		$file_name = $insert['file_name'];

		$wp_content_path = dirname( KotwUtilitiesPluginPath, 2 );
		$wp_main_path    = dirname( KotwUtilitiesPluginPath, 3 );
		switch ( $type ) {
			case 'assets':
				// Export assets zipped.
				$file_name .= '.tar.gz';
				\exec( "cd $wp_content_path && tar -czvf $file_name uploads/ && mv $file_name $file_path/" );
				break;

			default:
				// Export the db.
				global $wpdb;
				$file_name     .= '.sql';
				$exclude_tables = implode(
					',',
					array(
						$wpdb->prefix . 'kotw_hashed_exports',
						$wpdb->prefix . 'kotw_hashed_crons_logs',
					)
				);
				\exec( "cd $file_path && wp db export --exclude_tables=$exclude_tables $file_name", $output );
				new Logger( __CLASS__, $output, 'export_db: $output' );

				$sql_export_file = $file_path . '/' . $file_name;

				// Run sanitization.
				$sanitize_path_plugin = KotwUtilitiesPluginPath . 'assets';

				$sanitize_file_site = $wp_main_path . '/.kotw/scripts/production/sanitize.sql';
				$sanitize_file      = file_exists( $sanitize_file_site ) ? $sanitize_file_site : $sanitize_path_plugin . '/sanitize.sql';
				$file_handle        = fopen( $sql_export_file, 'a' );
				$sanitize_handle    = fopen( $sanitize_file, 'r' );
				while ( ! feof( $sanitize_handle ) ) {
					fwrite( $file_handle, fread( $sanitize_handle, 8192 ) );
				}
				fclose( $sanitize_handle );
				fclose( $file_handle );

				break;
		}

		// Check if file was inserted.
		if ( ! file_exists( $file_path . '/' . $file_name ) ) {
			new Logger(
				__CLASS__,
				"File $file_path/$file_name was not created.",
				'export_file'
			);

			return false;
		}

		global $wpdb;
		$insert = $wpdb->insert( $table_name, $insert );
		if ( ! $insert ) {
			new Logger( __CLASS__, $wpdb->last_error, 'export_file: $wpdb->show_errors' );

			return false;

		}

		return true;
	}


	/**
	 * This generates the insert array that should be used for inserting a new row in the custom table.
	 *
	 * @return array
	 */
	public static function generate_insert_array( $type = 'db' ) {
		$exports_path = dirname( KotwUtilitiesPluginPath, 2 ) . '/exports/' . $type;
		if ( ! file_exists( $exports_path ) ) {
			try {
				//TODO change to php8 once the site is upgraded.
				mkdir( $exports_path, 0777, true );
			} catch ( Exception $e ) {
				new Logger( __CLASS__, $e, 'generate_insert_array' );
			}
		}
		$file_name = md5( time() );

		return array(
			'file_name' => $file_name,
			'file_path' => $exports_path,
			'time'      => current_time( 'mysql' ),
		);
	}

}
