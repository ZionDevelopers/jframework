<?php
/**
 * jFramework
 * 
 * @version 1.2.1
 * @copyright Copyright (c) 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
// Date
date_default_timezone_set ( 'America/Sao_Paulo' );

// jFramework version
define ( 'VERSION', '1.2.1' );

// Folders Defination
define ( 'LIBRARY_DIR', APP_DIR . '/library' );
define ( 'LIB_DIR', LIBRARY_DIR);
define ( 'TMP_DIR', APP_DIR . '/tmp' );
define ( 'LOGS_DIR', TMP_DIR . '/logs' );
define ( 'SESSION_DIR', TMP_DIR . '/session' );
define ( 'CONTROLLERS_DIR', APP_DIR . '/controllers' );
define ( 'VIEWS_DIR', APP_DIR . '/views' );
define ( 'LAYOUT_DIR', VIEWS_DIR . '/layout' );
define ( 'CONFIGS_DIR', APP_DIR . '/configs' );
define ( 'ERRORS_PAGES_DIR', VIEWS_DIR . '/errors' );
define ( 'CACHE_DIR', TMP_DIR . '/cache' );

// Configs
define ( 'SITE_NAME', 'jFramework' );
define ( 'SITE_EMAIL_COPIES', 'example2@domain.com' );
define ( 'SITE_EMAIL', 'example2@domain.com' );
define ( 'DEBUG', true );
define ( 'CHARSET', 'utf-8' );
define ( 'BASE_DIR', ($BASE_DIR == '/' ? '' : $BASE_DIR) );
define ( 'BASE_URL', 'http://' . $_SERVER ['SERVER_NAME'] . BASE_DIR );
define ( 'LAYOUT_DEFAULT', 'default' );

static $CONFIGS = array ();

/**
 * Debugging
 *
 * false = PRODUCTION (No show any error on screen, but log all errors)
 * true = DEVELOPING (Only show errors on screen)
 */

if ( DEBUG ) {
	error_reporting ( E_ALL );
	ini_set ( "display_errors", "On" ); // To Show Errors
	ini_set ( "error_log", LOGS_DIR . "/phpErrors_development.log" );
	ini_set ( "log_errors", "On" ); // To log errors on files
} else {
	error_reporting ( E_ALL );
	ini_set ( "display_errors", "Off" ); // To not show errors
	ini_set ( "error_log", LOGS_DIR . "/phpErrors_production.log" );
	ini_set ( "log_errors", "On" ); // To log errors on files
}
