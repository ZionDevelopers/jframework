<?php
/**
 * jFramework
 * 
 * @version 1.2.1
 * @copyright Copyright (c) 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */


// Define default Controller
$controller = 'home';

// Define get Controller
function getController($result) {
	// Access Global variable
	global $controller;
	
	// Check if not empty
	if (! empty ( $result [0] )) {
		// Define controller
		$controller = $result [0];
	}
}

$BASE_DIR = str_replace ( "\\", '/', dirname ( $_SERVER ['SCRIPT_NAME'] ) );
define ( 'APP_DIR', str_replace ( "\\", '/', dirname ( getcwd () ) . '/app' ) );
define ( 'WEBROOT_DIR', str_replace ( "\\", '/', getcwd () ) );

// Search for Controller
preg_replace_callback ( '/([^\/,?,&][A-Z,a-z,0-9,.,_,-]+[\/]?[A-Z,a-z,0-9]+)/i', 'getController', $_SERVER ['REQUEST_URI'], 1 );

// Define Controller
define ( 'CONTROLLER', $controller );
unset ( $controller );

require APP_DIR . '/dispatcher.php';
?>