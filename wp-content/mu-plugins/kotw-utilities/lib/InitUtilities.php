<?php

namespace kotw;

use kotw\Admin\Crons;
use kotw\Admin\UserRoles;
use kotw\Admin\AdminBar;
use kotw\Admin\ACF;
use kotw\Custom\UserTaxonomy;
use kotw\Rest\Endpoints\Export;
use kotw\Rest\Endpoints\SiteStatus;
use kotw\Rest\Endpoints\BoilerPlate;


class InitUtilities {
	public function __construct() {
		$this->register_commands();
		$this->register_admin();
		$this->register_endpoints();
		$this->register_cron();
		$this->register_timber();

	}

	/**
	 * This registers all commands needed for kotw-utilities.
	 *
	 * @return void
	 */
	public function register_commands(): void {
		/**
		 * Register here WP_CLI Commands.
		 * Only Classes need to be added here as the main command,
		 * and their sub-commands need to be registered inside the corresponding class.
		 */
		if ( defined( 'WP_CLI' ) ) {
			add_action(
				'cli_init',
				function () {
					\WP_CLI::add_command( 'kotw', 'kotw\Commands\KotwTables' );
					\WP_CLI::add_command( 'kotw', 'kotw\Commands\Blocks' );

				}
			);
		}

	}

	/**
	 * This function registers all Admin related functionalities.
	 */
	public function register_admin() {
		// User roles.
		$user_roles = new UserRoles();
		$user_roles->run();

		// User Taxonomy.
		new UserTaxonomy();

		// Admin Bar.
		new AdminBar();

		// ACF customizations.
		new ACF();

	}

	/**
	 * This function registers all rest endpoints to be used by the plugin.
	 * @return void
	 */
	public function register_endpoints() {
		new Rest\Register(
			array(
				Export\Assets::init(),
				Export\Database::init(),
				Export\Access::init(),
				Export\Logs::init(),
				SiteStatus::init(),
				BoilerPlate::init(),
			)
		);

	}

	/**
	 * Registers all cron jobs needed for kotw-utilities.
	 *
	 * @return void
	 */
	public function register_cron() {
		// Custom Cron Jobs.
		new Crons();
	}

	/**
	 * Registers all timber related functionalities.
	 *
	 * @return void
	 */
	public function register_timber() {
		new Timber\Init();
	}
}
