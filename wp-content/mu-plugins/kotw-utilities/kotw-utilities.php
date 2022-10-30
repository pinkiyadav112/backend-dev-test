<?php
/*
 * Copyright (c) 2022. Property of Kings Of The Web
 */

/*
Plugin Name: KOTW Utilities
Plugin URI: https://kingsoftheweb.net
Description: This adds all utilities needed for KOTW WordPress sites.
Version: 1.4.7
Author: Kings Of The Web
Author URI: https://kingsoftheweb.net
License: GPL2
*/

use kotw\InitUtilities;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once 'globals.php';
require_once 'autoload.php';


// Init the plugin.
new InitUtilities();
