<?php
/**
 * jFramework
 *
 * @version 1.3.0
 * @link https://github.com/ZionDevelopers/jframework/ The jFramework GitHub Project
 * @copyright 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */

// Use View
use \jFramework\Core\View;
// jSessCleaner
use jFramework\Core\JSessCleaner as jSessCleaner;

View::autoRender(false);

if (defined('STDIN')) {
    echo "\r\nStarting PHP Session Cleaner for jFramework...\r\n";

    // Starts Session Cleaner
    $sc = new jSessCleaner ();
    // Get Old Session Files
    $files = $sc->getOldSessFiles();
    // Clean and Show
    $sc->cleanupAndShow($files);
}
