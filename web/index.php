<?php
/**
 * jFramework
 *
 * @version 2.0.0
 * @link https://github.com/ZionDevelopers/jframework/ The jFramework GitHub Project
 * @copyright 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */

// Define root directory
$root = dirname(__DIR__);

// Require autoloader
require $root . '/app/vendor/autoload.php';
// Add path to autoloader
autoload::addPath ($root . '/app/vendor/');
// Register autoloader
autoload::register(true);

// Start jFramework Core
$jFramework = new \jFramework\Core($root);
// Bootstrap jFramework
$jFramework->bootstrap();