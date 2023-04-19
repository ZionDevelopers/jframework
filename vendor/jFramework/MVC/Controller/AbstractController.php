<?php
/**
 * jFramework
 *
 * @link https://github.com/ZionDevelopers/jframework/ The jFramework GitHub Project
 * @copyright 2010-2023, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */

namespace jFramework\MVC\Controller;

/**
 * Base Controller who Controllers will extend to
 * 
 * Created: 2014-06-08 08:06 PM (GMT -03:00)
 * Updated: 2014-06-09 04:50 PM (GMT -03:00)
 * @version 0.0.2
 
 * @package jFramework
 * @subpackage MVC
 * @copyright Copyright (c) 2010-2018, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
abstract class AbstractController
{
    public $core = null;
    public $db = null;
    public $basepath = '';
}
