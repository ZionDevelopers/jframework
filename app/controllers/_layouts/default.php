<?php
/**
 * jFramework
 *
 * @version 1.3.0
 * @copyright Copyright (c) 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */

// Use View
use \jFramework\Core\View;

// Setting layout view vars
View::set ( array ( 'pageTitle' => SITE_NAME . (isset ( $pageTitle ) ? ' :: ' . $pageTitle : ' :: jFramework')), '_layouts/' . LAYOUT_PAGE );

// Put view contents inside Layout
echo View::renderLayout ( $contents );
