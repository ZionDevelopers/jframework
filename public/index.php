<?php
/**
 * jFramework *

 * @link https://github.com/ZionDevelopers/jframework/ The jFramework GitHub Project
 * @copyright 2010-2016, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */

// PHP 5.4 Emulator
if (version_compare(PHP_VERSION, '5.4.0', '<')) {
    // Emulate Request TIME FLOAT
    $_SERVER['REQUEST_TIME_FLOAT'] = microtime(true);
}

// CLI Compatibility mode
if (PHP_SAPI != 'cli') {
    $argv = array();
}

// Define root directory
$root = dirname(__DIR__);

// Require composer autoloader
require $root . '/vendor/autoload.php';

// Start jFramework Core
$jFramework = new \jFramework\Core(__DIR__, $argv);

// Initialize jFramework
$jFramework->initialize();
