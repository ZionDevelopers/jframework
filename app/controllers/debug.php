<?php
/**
 * jFramework
 * @copyright Copyright (c) 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */

$pageTitle = 'Debug';

// Check if Debug is Enabled
if (DEBUG) {
	// Check if DB is a Objct
	if (! empty ( $db )) {
		// Get Tables/Fields Names from Cache
		$cache = $db->cacheGet ();
	} else {
		// Nullify db
		$db = null;
		$cache = array ();
	}
		
	view::set ( compact ( 'cache' ) );
} else {
	// Disable View Rendering
	view::autoRender ( false );
}
?>
