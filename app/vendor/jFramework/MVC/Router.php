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

namespace jFramework\MVC;

use jFramework\Core\Registry;

/**
 * jFramework Router
 * 
 * Created: 2014-06-08 08:53 PM (GMT -03:00)
 * Updated: 2014-06-08 08:53 PM (GMT -03:00)
 * @version 0.0.1
 * @access public
 * @package jFramework
 * @subpackage MVC
 * @copyright Copyright (c) 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
class Router
{
    protected $customRoutes = array();
    protected $basepath = '';


    public function getCustomRoutes()
    {
        $this->customRoutes = Registry::get('CUSTOM_ROUTES');
    }
    
    public function bootstrap()
    {
        $this->basepath = dirname($_SERVER['SCRIPT_NAME']);
        $this->detectRequest();
    }
    
    protected function match($route, $method)
    {
        
    }
    
    protected function detectRequest()
    {
        $controller = 'Index';
        $uri = $_SERVER['REQUEST_URI'];
        $uri = preg_replace('#^'.$this->basepath.'(?:index\.php/)?#i', '/', $uri);
        $request = parse_url($_SERVER['REQUEST_URI']);
        
        exit($controller);
    }
}
