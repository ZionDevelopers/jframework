<?php
/**
 * jFramework
 *
 * @version 1.2
 * @copyright Copyright (c) 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
// Use View
use \jFramework\Core\View;

$pageTitle = 'Home';

// jFramework MySQL data saving Example
$db->save ( array (	'name' => 'Mr. #' . mt_rand ( 1, 9999 ) ), 'tests' );

// jFramework MySQL data finding Example
$data = $db->find ( 'tests' );

View::set ( compact ( 'data' ) );
