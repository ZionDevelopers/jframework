<?php
/**
 * jFramework
 *
 * @version 1.2
 * @copyright Copyright (c) 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
view::autoRender ( false );

if (defined ( 'STDIN' )) {
	echo "\r\nStarting PHP Session Cleaner for jFramework...\r\n";
	// Loading Class
	tools::Library ( 'jSessCleaner' );
	// Starts Session Cleaner
	$sc = new jSessCleaner ();
	// Get Old Session Files
	$files = $sc->getOldSessFiles ();
	// Clean and Show
	$sc->cleanupAndShow ( $files );
}
?>