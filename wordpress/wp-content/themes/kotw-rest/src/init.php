<?php
/**
 * @author     Kings Of The Web
 * @year       2022
 * @package    KotwRest
 */

use KotwRest\Wordpress\Init as WordpressInit;
use KotwRest\Blocks\Init as BlocksInit;
use KotwRest\ACF\Init as ACFInit;
use KotwRest\Endpoints\Init as EndpointsInit;
use KotwRest\Wordpress\Menus as MenusInit;

// Init WordPress.
new WordpressInit();

// Init Blocks.
new BlocksInit();

// Init ACF.
new ACFInit();

// Init endpoints.
new EndpointsInit();

// Init menus.
new MenusInit();
