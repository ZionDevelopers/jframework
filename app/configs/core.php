<?php 
/**
 * jFramework
 * @copyright Copyright (c) 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
// Date
date_default_timezone_set ( 'America/Sao_Paulo' );

// Folders Defination
define ( 'LIBRARY_DIR', APP_DIR . '/library' );
define ( 'TMP_DIR', APP_DIR . '/tmp' );
define ( 'LOGS_DIR', TMP_DIR . '/log' );
define ( 'SESSION_DIR', TMP_DIR . '/session' );
define ( 'CONTROLLERS_DIR', APP_DIR . '/controllers' );
define ( 'VIEWS_DIR', APP_DIR . '/views' );
define ( 'LAYOUT_DIR', VIEWS_DIR . '/layout' );
define ( 'CONFIGS_DIR', APP_DIR . '/configs' );
define ( 'ERRORS_PAGES_DIR', VIEWS_DIR . '/errors' );
define ( 'CACHE_DIR', TMP_DIR . '/cache' );
define ( 'RESOURCE_DIR', APP_DIR . '/resource' );
define ( 'TOUR360_DIR', WEBROOT_DIR . '/tours360' );

// Configs
define ( 'SITE_NAME', 'jFramework' );
define ( 'SITE_EMAIL_COPIES', 'talk@juliocesar.me' );
define ( 'SITE_EMAIL', 'talk@juliocesar.me' );
define ( 'DEBUG', true);
define ( 'DEBUG_HTTP_PORT', 88 );
define ( 'DEBUG_HTTPS_PORT', 4443 );
define ( 'PRODUCTION_HTTP_PORT', 80 );
define ( 'PRODUCTION_HTTPS_PORT', 443 );
define ( 'CHARSET', 'utf-8' );
define ( "BASE_DIR", ($BASE_DIR == '/' ? '' : $BASE_DIR) );
define ( 'BASE_URL_SECURE', 'https://' . $_SERVER ['SERVER_NAME'] . (DEBUG ? ':' . DEBUG_HTTPS_PORT : '') . '/' . BASE_DIR );
define ( 'BASE_URL_NON_SECURE', 'http://' . $_SERVER ['SERVER_NAME'] . (DEBUG ? ':' . DEBUG_HTTP_PORT : '') . '/' . BASE_DIR );
define ( 'BASE_URL', 'http' . (isSSL () ? 's' : '') . '://' . $_SERVER ['SERVER_NAME'] . (DEBUG ? ':' . $_SERVER ['SERVER_PORT'] : '') . BASE_DIR );
define ( 'LAYOUT_DEFAULT', 'default' );

static $CONFIGS = array ();

// ------------------------- DEBUG MODE : START -------------------------//
/*
 * false = PRODUCTION (No show any error on screen, but log all errors) true = DEVELOPING (Only show errors on screen)
 */

if (DEBUG === true) {
	error_reporting ( E_ALL | E_STRICT );
	ini_set ( "display_errors", "On" ); // To Show Errors
	ini_set ( "error_log", LOGS_DIR . "/phpErrors_development.log" );
	ini_set ( "log_errors", "On" ); // To log errors on files
} elseif (DEBUG === false) {
	error_reporting ( E_ALL & ~ E_NOTICE );
	ini_set ( "display_errors", "Off" ); // To not show errors
	ini_set ( "error_log", LOGS_DIR . "/phpErrors_production.log" );
	ini_set ( "log_errors", "On" ); // To log errors on files
}
// ------------------------- DEBUG MODE : END -------------------------//

/**
 * **** INI SET ADAPTATIONS: START *****
 */
// Limite de memoria de 64 megabytes
ini_set ( 'memory_limit', '10M' );
/**
 * **** INIT SET ADAPTATIONS: END *****
 */
?>