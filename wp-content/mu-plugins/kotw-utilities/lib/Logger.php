<?php

/**
 * This is the main Logger class used by KOTW sites.
 *
 * This will log in a pre-defined file in the wp-content/.logs directory,
 * and will also log in the Query Monitor's console if the plugin is installed and activated.
 *
 ******************************************************************************************************************
 **************************************** Examples of how to log data *********************************************
 * new Logger(__FILE__, $context, 'context');
 * >>> __FILE__ is the file path of the file you are logging from, and will be used as the log file name.
 * >>> $context is the context you are logging ( This can be string, array, object, etc. ).
 * >>> 'context' is the header of the logging message ( This can only be a string ).
 *
 * new Logger(__CLASS__, $context['post'], 'context[post]');
 * >>> __CLASS__ is the class name of the file you are logging from, and will be used as the log file name.
 * >>> $context['post'] is the context you are logging ( This can be string, array, object, etc. ).
 * >>> 'context[post]' is the header of the logging message ( This can only be a string ).
 *
 * new Logger('custom-log-file-name', $context, 'context');
 * >>> 'custom-log-file-name' is the custom log file name you want to use.
 * >>> $context is the context you are logging ( This can be string, array, object, etc. ).
 * >>> 'context' is the header of the logging message ( This can only be a string ).
 *******************************************************************************************************************
 *
 * @author     Kings Of The Web
 * @year       2022
 * @package    codedegree
 * @subpackage codedegree\Timber
 */
namespace kotw;

class Logger {
	/**
	 * @var string
	 */
	protected static $type = 'error';

	/**
	 * @var string
	 */
	protected static string $destination;
	/**
	 * @var array
	 */
	protected static array $logs;

	public function __construct( $type = 'error', $message = null, $header = null ) {
		self::$type        = self::getType( $type );
		self::$destination = dirname( __FILE__, 4 ) . '/.logs/';
		self::$logs        = array();

		if ( ! file_exists( self::$destination ) ) {
			mkdir( self::$destination );
			chmod( self::$destination, 0700 );
		}

		if ( $message && $header ) {
			$this->log( $message, $header );
		}
	}

	/**
	 * Gets the type of the error, which will be used to determine the file name.
	 *
	 * @param $type
	 *
	 * @return string
	 */
	public static function getType( $type ) {
		return preg_replace( '/[\W]/', '_', $type );
	}

	/**
	 * This logs the file in the .logs directory, with the pre-defined file name.
	 *
	 * @param $message
	 * @param $header
	 *
	 * @return void
	 */
	public static function log( $message, $header = '' ) {

		$file_destination = self::$destination . self::$type . '.log';
		if ( ! file_exists( $file_destination ) ) {
			fopen( $file_destination, 'w' ) || die( "Can't create file" );
		}

		$time = gmdate( 'd/m/Y H:i:s' );

		if ( is_array( $message ) || is_object( $message ) ) {
			$message = print_r( $message, true );
		}
		// Log to Query Monitor if it's installed and activated.
		self::logToQM( $header, $message );

		// Get the IP address of the user, to attach it to the message.
		$ip = self::getIP();

		// Log to the chosen file.
		error_log( "[$time][$ip][$header]: $message\n", 3, $file_destination );
	}

	/**
	 * Returns the IP address of the client.
	 *
	 * @return mixed
	 */
	public static function getIP() {
		// get IP of the current visitors.
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}

	/**
	 * If Query Monitor is active, then this should log to the Query Monitor's console.
	 *
	 * The type is used is always 'debug' for Query Monitor. Read: https://querymonitor.com/docs/logging-variables/
	 *
	 * @link    https://querymonitor.com/docs/logging-variables/
	 * @return  void
	 */
	public static function logToQM( $header, $message ) {
		// check if Query monitor plugin is one of the active plugins.
		if ( ! in_array( 'query-monitor/query-monitor.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			return false;
		}

		do_action(
			'qm/debug',
			$message,
			array(
				'caller' => $header,
			)
		);
	}
}
