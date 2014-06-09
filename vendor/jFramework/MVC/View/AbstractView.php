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

use jFramework\Core\Registry;

/**
 * To manage Views
 * 
 * Created: 2010-08-24 12:50 PM (GMT -03:00)
 * Updated: 2014-06-09 10:16 AM (GMT -03:00)
 * @version 2.0.2 
 * @package jFramework
 * @subpackage MVC
 * @copyright Copyright (c) 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
abstract class AbstractView 
{
    protected $viewFile = '';
    protected $layoutFile = '';
    
    /**
     * Constructor
     * @param string $layout
     */
    public function __construct(){
        $request = Registry::get('Request');
        $this->viewFile = strtolower($request['controller']) . '/' . strtolower($request['action']);
    }
    /**
     * To Set a Flash Message
     *     
     * @param string $message
     */
    public function setFlash($message)
    {
        $_SESSION ['SYSTEM'] ['MESSAGE'] = $message;
    }

    /**
     * To get a Flash Message
     *     
     * @return string|null
     */
    public function getFlash()
    {
        return (isset($_SESSION ['SYSTEM'] ['MESSAGE']) ? $_SESSION ['SYSTEM'] ['MESSAGE'] : null);
    }

    /**
     * To delete a Flash Message
     *      
     */
    public function delFlash()
    {
        if (isset($_SESSION ['SYSTEM'] ['MESSAGE'])) {
            $_SESSION ['SYSTEM'] ['MESSAGE'] = null;
        }
    }
    
    public function setLayout($layout)
    {
        $this->layoutFile = $layout;
    }
}
