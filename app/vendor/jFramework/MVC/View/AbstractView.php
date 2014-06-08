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

namespace jFramework\MVC\View;

/**
 * To manage Views
 * 
 * Created: 2010-08-24 12:50 PM
 * Updated: 2014-06-08 08:24 AM
 * @version 2.0.0
 * @access public
 * @package jFramework
 * @subpackage MVC
 * @copyright Copyright (c) 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
abstract class AbstractView 
{

    /**
     * To Set a Flash Message
     *
     * @access public
     * @param string $message
     */
    public function setFlash($message)
    {
        $_SESSION ['SYSTEM'] ['MESSAGE'] = $message;
    }

    /**
     * To get a Flash Message
     *
     * @access public
     * @return string|null
     */
    public function getFlash()
    {
        return (isset($_SESSION ['SYSTEM'] ['MESSAGE']) ? $_SESSION ['SYSTEM'] ['MESSAGE'] : null);
    }

    /**
     * To delete a Flash Message
     * 
     * @access public
     */
    public function delFlash()
    {
        if (isset($_SESSION ['SYSTEM'] ['MESSAGE'])) {
            $_SESSION ['SYSTEM'] ['MESSAGE'] = null;
        }
    }
}
