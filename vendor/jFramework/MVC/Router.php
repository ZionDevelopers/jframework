<?php
/**
 * jFramework
 *
 * @version 2.1.0
 * @link https://github.com/ZionDevelopers/jframework/ The jFramework GitHub Project
 * @copyright 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */

namespace jFramework\MVC;

use jFramework\Core\Registry;
use jFramework\MVC\View\XHTML;

/**
 * jFramework Router
 * 
 * Created: 2014-06-08 08:53 PM (GMT -03:00)
 * Updated: 2015-08-10 10:15 AM (GMT -03:00)
 * @version 0.0.6 
 * @package jFramework
 * @subpackage MVC
 * @copyright Copyright (c) 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
class Router
{
    protected $regexRoutes = array();
    protected $redirRoutes = array();
    protected $basepath = '';
    public $core = null;

    /**
     * Get Custom Routes
     */
    public function __construct()
    {
        // Loop by regex routes
        foreach(Registry::get('REGEX_ROUTES') as $controllerAction => $pattern) {
            $pattern = '/^' . str_replace('/', '\/', $pattern) . '$/';
            $this->regexRoutes[$pattern] = explode('_', $controllerAction);
        }
        
        // Loop by redir routes
        $this->redirRoutes = Registry::get('REDIR_ROUTES');
    }
    
    /**
     * Start Bootstrap
     */
    public function bootstrap()
    {
        // Define base path
        $this->basepath = str_replace('\\', '/', dirname($this->core->server('SCRIPT_NAME')));       
         
        /// Detect request
        $request = $this->detectRequest();   
                        
        // Define Request data
        Registry::set('Request', $request);
        
        // Handle the Request
        $view = $this->handleRequest($request);
        
        // Format XHTML
        return XHTML::format($view, $this->basepath);
    }
    
    /**
     * Handle the Request
     * @param array $request
     */
    protected function handleRequest($request)
    {
        // Default view contents
        $contents = '';
        
        // Controller Class
        $class = ucfirst($request['controller']) . 'Controller';
        // Action Mathod
        $method = strtolower($request['action']) . 'Action';
        
        // Get controllers folder
        $file = Registry::get('FOLDER.controller');
        // Set Class
        $file .= '/' . $class . '.php';       

        // Check if file is Readable
        if (is_readable($file)) {
            // Require controller
            require $file;
            
            // Define class with namespace
            $class = 'App\Controller\\'.$class;

            // Check if Controller was found on the declared classes
            if (in_array($class, get_declared_classes())) {  
                // Spawn new Controller
                $controller = new $class;               
                
                // Pass Core OBJ
                $controller->core = $this->core;
                $controller->db = $this->core->db();  
                $controller->basepath = $this->basepath;
                 
                // Check if Action exists
                if (method_exists($controller, $method)) {
                    // Call Action
                    $contents = call_user_func_array(
                        array($controller, $method),
                        array($this->core->get(), $this->core->post(), $request ['data'])
                    );                   
                } else {
                    // Run 404 Error Page
                    $contents = $controller->notFoundAction();
                }
            }
        }
        
        // Check if controller was successfully spawned
        if (!isset($controller)) {     
            // Spawn new Error Controller
            $controller = new \jFramework\MVC\Controller\ErrorController();
            
            // Pass Core OBJ
            $controller->core = $this->core;
            
            // Run NotFound Action
            $contents = $controller->notFoundAction($request);            
        }
                        
        // Check if database driver was created
        if (!is_null($this->core->db)) {
            // Close connection
            $this->core->db->close();
        }
        
        // Check if controller was successfully spawned and PHP is running on a WebServer
        if (is_object($controller) && PHP_SAPI != 'cli') {
            // Check for layout function
            if(method_exists($controller, 'layout') && !empty($contents)) {
                // Render layout
                return $controller->layout($contents);            
            }
        } else {
            // Return view contents when php is running from Console
            return $contents;
        }
    }
    
    /**
     * Search for a match in the custom routes
     * @param string $route
     * @param string $method
     * @return type
     */
    protected function match($route, $method)
    {
        // Format failsafe route
        $match = array(
            'route' => $route,
            'controller' => null, 
            'action' => null, 
            'method' => $method,
            'data' => array()
        );
        
        // Loop by regex routes
        foreach ($this->regexRoutes as $pattern => $_) {       
            // Check if is there a match
            if (preg_match($pattern, $route, $params) === 1) {
                // Remove useless key
                array_shift($params);
                  
                // Format route array
                $match['route'] = $route;
                $match['controller'] = $params[0];
                $match['action'] = empty($params[1]) ? 'index' : $params[1];
                $match['data'] = $params;
            }
        }

        // Check if a custom route was found
        if (isset($this->redirRoutes[$route])) {
            // Split Controller Separator
            $result = explode(':', $this->redirRoutes[$route]);
        
            // Format route array
            $match['route'] = $route;
            $match['controller'] = $result[0];
            $match['action'] = $result[1];
        }
                
        // Return route
        return $match;
    }
    
    /**
     * Detect the current Request
     * @return array
     */
    protected function detectRequest()
    {
        // Get URI
        $uri = $this->core->server('REQUEST_URI');

        // Check if PHP is running on WebServer
        if (PHP_SAPI == 'cli') {
            // Format a Request URI for Console
            $uri = isset ($this->core->args[1]) ? '/' . $this->core->args[1] : '/';
        }
        
        // Remove first slash
        $uri = preg_replace('/^\//', '', $uri);
        
        // Set default request path
        $request = array();
        $request['path'] = '';
        
        // Parse URI Request
        $request = array_merge($request, parse_url($uri));

        // Detect a match for custom route
        $route = $this->match($request['path'], $this->core->server('REQUEST_METHOD'));

        // If not found a custom route
        if (is_null($route['controller'])) {
            // Search for action
            if (strstr($request['path'], '/') !== false) {
                // Split controller spearator
                $path = explode('/', $request['path']);
            } else {
                $path = array();
                $path[0] = $request['path'];
            }
            // Define Controller
            $route['controller'] = !empty($path [0]) ? $path[0] : 'Index';
            // Define Action
            $route['action'] = !empty($path[1]) ? $path[1] : 'index';
        }
        
        return $route;
    }
}
