<?php
if (defined ( 'STDIN' )) {
	$BASE_DIR = str_replace ( "\\", '/', dirname ( $_SERVER ['SCRIPT_NAME'] ) );
	define ( 'APP_DIR', str_replace ( "\\", '/', dirname ( getcwd () ) . '/app' ) );
	define ( 'WEBROOT_DIR', str_replace ( "\\", '/', getcwd () ) );
	
	if (isset ( $argv [0] )) {
		//Define Controller
		define ( 'CONTROLLER', $argv [1] );
		//Requesting Configs and Library Classes
		//Basics Functions
		require (APP_DIR . '/library/basic.php');
		//Core Config
		require (APP_DIR . '/configs/cron.php');
		
		// Tools Library
		require (LIBRARY_DIR . '/tools.php');

		//Start Timer
		$timer = tools::timer ( 1 );
		
		//View Library
		tools::Library ( 'view' );
	
		//Require Controller
		if (file_exists ( CONTROLLERS_DIR . '/_cron/' . CONTROLLER . '.php' )) {
			//Require Controller
			require (CONTROLLERS_DIR . '/_cron/' . CONTROLLER . '.php');
		} else {
			//Generate a custom error page
			echo 'Cron page not founded';
		}
	}
}
?>