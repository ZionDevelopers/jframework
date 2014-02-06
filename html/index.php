<?php
$BASE_DIR = str_replace ( "\\", '/', dirname ( $_SERVER ['SCRIPT_NAME'] ) );
define ( 'APP_DIR', str_replace ( "\\", '/', dirname ( getcwd () ) . '/app' ) );
define ( 'WEBROOT_DIR', str_replace ( "\\", '/', getcwd () ) );
$controller = 'home';

if (isset ( $_GET ['controller'] )) {
	$controller = $_GET ['controller'];
	unset ( $_GET ['controller'] );
}

$_SERVER ['QUERY_STRING'] = preg_replace ( '/^controller=' . preg_quote ( $controller, '/') . '(&)?/', '', $_SERVER ['QUERY_STRING'] );

// Define Controller
define ( 'CONTROLLER', $controller );
unset ( $controller );

require (APP_DIR . '/dispatcher.php');
?>