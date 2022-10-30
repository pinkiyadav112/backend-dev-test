<?php
/**
 * This handles all custom cron jobs for kotw-utilities
 *
 * @author     Kings Of The Web
 * @year       2022
 * @package    kotw-base
 * @subpackage kotw\Crons
 */

namespace kotw\Admin;

use kotw\Admin\Crons\FilesHandler;


class Crons {
	/**
	 * @var array|string[]
	 */
	private static array $tables;


	public function __construct() {
		// Register names of tables.
		global $wpdb;
		self::$tables = array(
			'hashed_exports' => $wpdb->prefix . 'kotw_hashed_exports',
			'exports_logs'   => $wpdb->prefix . 'kotw_hashed_crons_logs',
		);

		add_action( 'init', array( $this, 'register_cron_jobs' ) );

		// List of cronjobs
		add_action( 'kotw_export_db', array( $this, 'export_db' ) );
		add_action( 'kotw_export_assets', array( $this, 'export_assets' ) );
		add_action( 'kotw_clean_exports', array( $this, 'clean_exports' ) );
	}


	/**
	 * This registers all cron jobs.
	 *
	 * @return void
	 */
	public function register_cron_jobs(): void {
		date_default_timezone_set( 'America/Toronto' );
		$next_5pm  = new \DateTime( '5pm' );
		$next_10pm = new \DateTime( '10pm' );

		// Export DB Cron Job.
		// Runs hourly.
		// Calls kotw_export_db to export the DB.
		if ( ! wp_next_scheduled( 'kotw_export_db' ) ) {

			wp_schedule_event( $next_5pm->getTimestamp(), 'hourly', 'kotw_export_db' );
		}

		// Export Assets Cron Job.
		// Runs once daily.
		// Calls kotw_export_assets to export the zipped assets file.
		if ( ! wp_next_scheduled( 'kotw_export_assets' ) ) {
			wp_schedule_event( $next_5pm->getTimestamp(), 'daily', 'kotw_export_assets' );
		}
	}

	/**
	 * This export the db at the moment that this method is called.
	 * It saves the file name as a hashed string.
	 * It saves the file to specific path.
	 * It saves both information to the db in this table: $wpdb->prefix . 'kotw_hashed_exports';
	 *
	 * @return bool
	 */
	public static function export_db(): bool {
		// Export DB.
		$export_file = FilesHandler::export_file( self::$tables['hashed_exports'], 'db' );
		self::log_hashed_export_cron_jon( 'export-db' );

		return $export_file;
	}


	/**
	 * This export a zipped assets file at the moment that this method is called.
	 * It saves the file name as a hashed string.
	 * It saves the file to specific path.
	 * It saves both information to the db in this table: $wpdb->prefix . 'kotw_hashed_exports';
	 *
	 * @return bool
	 */
	public static function export_assets(): bool {
		// Export zipped assets.
		$export_file = FilesHandler::export_file( self::$tables['hashed_exports'], 'assets' );
		self::log_hashed_export_cron_jon( 'export-assets' );

		return $export_file;

	}

	/**
	 * This removes old backups from the exports directory.
	 * It keeps the last version of the DB, and the assets files only.
	 * It removes any references to the asset and the db files from the DB table : $wpdb->prefix . 'kotw_hashed_exports';
	 *
	 * @return bool|null
	 */
	public static function clean_exports(): ?bool {

		global $wpdb;
		$table_name = self::$tables['hashed_exports'];

		// Get list of db files.
		$db_files = $wpdb->get_results( "select * from $table_name WHERE file_path like '%/db%' order by id desc; " );
		$db_files = array_slice( $db_files, 1 );

		// Get list of assets files.
		$assets_files = $wpdb->get_results( "select * from $table_name WHERE file_path like '%/assets%' order by id desc; " );
		$assets_files = array_slice( $assets_files, 1 );

		$remove_file = null;
		foreach ( $db_files as $file ) {
			$remove_file = FilesHandler::remove_file(
				$table_name,
				$file->id,
				$file->file_name . '.sql',
				$file->file_path
			);
		}
		foreach ( $assets_files as $file ) {
			$remove_file = FilesHandler::remove_file(
				$table_name,
				$file->id,
				$file->file_name . '.tar.gz',
				$file->file_path
			);
		}

		self::log_hashed_export_cron_jon( 'clean-exports' );

		return $remove_file;
	}

	/**
	 * @return void
	 */
	public static function log_hashed_export_cron_jon( $operation ): void {
		global $wpdb;
		$table_name = self::$tables['exports_logs'];
		$wpdb->insert(
			$table_name,
			array(
				'operation' => $operation,
				'time'      => current_time( 'mysql' ),
			)
		);
	}
}
