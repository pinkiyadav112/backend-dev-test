<?php
/*
 * Copyright (c) 2022. Property of Kings Of The Web
 */

/**
 * @author     Kings Of The Web
 * @year       2022
 * @package    CodeDegree.com
 * @subpackage kotw\Commands
 */

namespace kotw\commands;

use kotw\Custom\DBTable;
use kotw\Logger;

class KotwTables {
	/**
	 * @param $args
	 * @param $assoc_args
	 *
	 * @return void
	 *
	 * @subcommand create-utilities-tables
	 *
	 * ## Usage
	 *
	 *      wp kotw create-utilities-tables
	 */
	public function create_utilities_tables( $args, $assoc_args ): void {
		// Create hashed exports table.
		new DBTable(
			'kotw',
			'hashed_exports',
			array(
				'id'        => 'mediumint(9) NOT NULL AUTO_INCREMENT',
				'file_name' => 'text NOT NULL',
				'file_path' => 'text NOT NULL',
				'time'      => 'DATETIME NOT NULL',
			)
		);

		// Create hashed crons logs table.
		new DBTable(
			'kotw',
			'hashed_crons_logs',
			array(
				'id'        => 'mediumint(9) NOT NULL AUTO_INCREMENT',
				'operation' => 'text NOT NULL',
				'time'      => 'DATETIME NOT NULL',
			)
		);

		\WP_CLI::success( 'The mysql queries have been executed. Check the logs if there were any error!' );
	}
}
