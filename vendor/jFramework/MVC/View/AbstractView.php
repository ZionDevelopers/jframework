<?php
/**
 * jFramework
 *
 * @version 2.3
 * @link https://github.com/ZionDevelopers/jframework/ The jFramework GitHub Project
 * @copyright 2010-2023, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */

namespace jFramework\MVC\View;

use jFramework\Core\Registry;

/**
 * To manage Views
 * 
 * Created: 2010-08-24 12:50 PM (GMT -03:00)
 * Updated: 2023-07-01 3:08 PM (GMT -03:00)
 * @version 2.0.5 
 * @package jFramework
 * @subpackage MVC
 * @copyright Copyright (c) 2010-2023, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
abstract class AbstractView 
{
    protected $file = '';
    protected $view = '';
    public $fileExt = '.phtml';
    public $viewRequest = 'none';
    public $action = 'none';
    public $db = null;
    public $requestURL = '';
    
    /**
     * Constructor
     */
    public function __construct()
    {
        // Get Request
        $request = Registry::get('Request');
        
        // Get View folder
        $this->file = Registry::get('FOLDER.view');
        $this->file .= '/' . ucfirst($request['controller']);
        $this->file .= '/' . ucfirst($request['action']) . $this->fileExt;        
        $this->viewRequest = '/' . ucfirst($request['controller']) . '/' . ucfirst($request['action']) . $this->fileExt;
        $this->action = ucfirst($request['controller']) . '/' . ucfirst($request['action']);
        $this->requestURL = $request['route'];
    }
    
    /**
     * Get registry data
     * @param string $var
     * @return mixed
     */
    public function registry($var)
    {
        // Return data from registry
        return Registry::get($var);
    }
    
    /**
     * Set a property
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {                
        // Set variable on object
        $this->$name = $value;
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
    
    /**
     * Check layout
     * @param string $file
     * @return boolean
     */
    public function layoutCheck($file)
    {
        return is_readable($file);
    }
    
    /**
     * Render a view/layout
     * @param string $file
     * @return string
     */
    protected function render($file)
    {
        // Define result failsafe
        $result = '';

        // Check if file is readable
        if (is_readable($file)) {
            // Start Buff obtainer
            ob_start();
            
            // Require file
            require $file;
            
            // Get Buff contents
            $result = ob_get_contents();
                        
            // Stop the buffering and clean
            ob_end_clean();  
        } else {
            // Throw 404 not found view
            throw new \Exception(sprintf('View: %s was not found.', $file), 404);
        }
        
        // Result buff
        return $result;
    }
}
