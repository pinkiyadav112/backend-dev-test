<?php

namespace kotw\Custom;

use kotw\Logger;
use Exception as Exception;

class DBTable {

	/**
	 * @var string
	 */
	public string $prefix;
	/**
	 * @var string
	 */
	public string $table_name;

	/**
	 * @var string
	 */
	public string $create_sql;

	/**
	 * @var array|mixed
	 */
	public array $columns;

	public function __construct(
		$prefix = 'kotw',
		$table_name = '',
		$columns = array()
	) {
		$this->prefix     = $prefix;
		$this->table_name = $table_name;
		$this->columns    = $columns;

		$this->create_sql = $this->prepare_create_sql();
		if ( $this->create_sql ) {
			$this->create_table();
		}
	}

	/**
	 * This prepares the sql statement that will create the table.
	 *
	 * Example:
	 *          array(
	 *                'id'      => 'mediumint(9) NOT NULL AUTO_INCREMENT',
	 *                'user_id' => 'mediumint(9) NOT NULL',
	 *                'session' => 'text NOT NULL',
	 *                'time'    => 'DATETIME NOT NULL',
	 *          )
	 *
	 * @return bool|string
	 */
	public function prepare_create_sql() {

		global $wpdb;
		$table_name = $wpdb->prefix . $this->prefix . '_' . $this->table_name;

		// Check if this table actually exists or no, before going through the whole logic.
		if ( self::table_exists( $table_name, $wpdb ) ) {
			return false;
		}

		$charset_collate = $wpdb->get_charset_collate();

		$array_keys  = array_keys( $this->columns );
		$primary_key = $array_keys ? $array_keys[0] : null;
		$sql         = "CREATE TABLE $table_name (";
		foreach ( $this->columns as $key => $column ) {
			$sql .= "$key $column,";
		}

		$sql .= "PRIMARY KEY ($primary_key)";
		$sql .= ") $charset_collate;";

		return $sql;
	}

	/**
	 * This creates the table ONLY if it does not exit.
	 * @return false|array
	 */
	public function create_table() {

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		try {
			global $wpdb;
			$table = dbDelta( $this->create_sql );

			if ( $wpdb->show_errors ) {
				new Logger( __CLASS__, $wpdb->last_error, '$wpdb->show_errors' );

				return false;
			}

			return $table;

		} catch ( Exception $e ) {
			new Logger( __CLASS__, $e, 'create_table' );

			return false;
		}

	}

	/**
	 * This checks if the table exists, before running the rest of logic.
	 *
	 * @param $table_name
	 * @param $wpdb
	 *
	 * @return bool
	 */
	public static function table_exists( $table_name, $wpdb ): bool {
		$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );

		if ( $wpdb->get_var( $query ) === $table_name ) {
			return true;
		}

		return false;

	}

}

