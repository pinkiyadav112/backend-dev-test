<?php

// global vendor autoloader.
require_once dirname( __DIR__, 1 ) . '/vendor/autoload.php';


// Add the custom spl_autoload_register function.
spl_autoload_register(
	function ( $classname ) {

		// It has to be part of codedegree namespace.
		$class     = preg_replace( array( '/KotwRest\\\\/i', '/\\\\/i' ), array( '', DIRECTORY_SEPARATOR ), $classname );
		$classpath = dirname( __FILE__ ) . '/lib/' . $class . '.php';

		if ( file_exists( $classpath ) ) {
			include_once $classpath;
		}

	}
);


