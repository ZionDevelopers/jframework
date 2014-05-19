<?php
/**
 * jFramework
 *
 * @version 1.2
 * @copyright Copyright (c) 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
$pageTitle = 'Home';

$db->save ( array (	'name' => 'Mr. #' . mt_rand ( 1, 9999 ) ), 'tests' );

$data = $db->find ( 'tests' );

view::set ( compact ( 'data' ) );
