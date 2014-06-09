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

namespace jFramework;

use jFramework\Core\Tools;
use jFramework\Core\Registry;
use jFramework\MVC\Router;

// Use

/**
 * jFramework Core Operations Handler
 * 
 * Created: 2014-06-08 04:38 PM (GMT -03:00)
 * Updated: 2014-06-08 07:10 PM (GMT -03:00)
 * @version 0.0.3 
 * @package jFramework
 * @copyright Copyright (c) 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
class Core
{
    /**
     * Root director for jFramework
     * @var string
     */
    public $rootDir = '';
    
    /**
     * Constructor
     * @param string $rootDir
     */
    public function __construct($rootDir)
    {
        $this->rootDir = $rootDir;
    }
    
    /**
     * Parse jFramework Settings
     */
    private function reloadData()
    {
        // Define .ini files directory
        Registry::setDir($this->rootDir . '/config/');  
        // Define rootDir variable
        Registry::set('rootDir', $this->rootDir);
        // Define serverName variable
        Registry::set('serverName', $_SERVER['SERVER_NAME']);
        // Parse the whole configs directory
        Registry::parseDir();        
    }
    
    /**
     * Update php.ini settings from app/configs/php.ini
     */
    private function ini_update()
    {
        // Loop all PHP.ini values
        foreach(Registry::get('PHP') as $key => $value){
            // Define new value
            ini_set($key, $value);
        }
    }
    
    /**
     * Define Bootstrap Headers
     */
    private function headers()
    {
        // Start Session on Dispatcher
        session_start ();

        // Signature
        header('X-Powered-By: jFramework ' . Registry::get('jFramework.version'), true);

        // Setting content Type and Charset
        header('Content-Type: text/html; charset=' . Registry::get('APP.charset'), true);
    }
    
    /** 
     * Initialize jFramework
     */
    public function initialize()
    {        
        // Parse all .ini files
        $this->reloadData();
        
        // Update php.ini settings from app/configs/php.ini
        $this->ini_update();

        // Define Bootstrap headers
        $this->headers();
        
        // Spawn Router
        $Router = new Router();
        
        // Get Custom Routes
        $Router->getCustomRoutes();
        
        // Bootstrap
        $Router->bootstrap();
    }
}
