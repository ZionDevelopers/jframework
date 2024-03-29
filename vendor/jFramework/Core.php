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

namespace jFramework;

use jFramework\Core\Registry;
use jFramework\MVC\Router;

/**
 * jFramework Core Operations Handler
 *
 * Created: 2014-06-08 04:38 PM (GMT -03:00)
 * Updated: 2023-06-08 08:10 PM (GMT -03:00)
 * @version 0.0.4
 * @package jFramework
 * @copyright Copyright (c) 2010-2018, Júlio César de Oliveira
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
    public $args = array();
    public $db = null;
    public $basepath = '';

    /**
     * Constructor
     * @param string $rootDir
     * @param boolean|string $changeBaseDir
     */
    public function __construct($rootDir, $args, $changeBaseDir = false)
    {
        if (PHP_SAPI == 'cli') {
            unset($args[0]);
        }

        // Define WebRoot
        Registry::set('webroot', $rootDir);
        
        // Check for baseDir change
        if (empty($changeBaseDir)) {
            // Set normal rootDir
            $this->rootDir = dirname($rootDir);
        } else {
            // Set custom rootDir
            $this->rootDir = $changeBaseDir;
        }
        
        $this->args = $args;
    }

    /**
     * Pass registry to Core
     * @param string $var
     * @param mixed $value
     * @return mixed
     */
    public function registry($var, $value = null)
    {
        if (is_null($value)) {
            return Registry::get($var);
        } else {
            Registry::set($var, $value);
        }
    }

    /**
     * Get Database Driver OBJ
     * @return \jFramework\Database\Drivers\MySQLi
     */
    public function db()
    {
        // Get
        $settings = $this->registry('DATABASE');

        if (!empty($settings)) {
            if (is_null($this->db)) {
                $driver = '\jFramework\Database\Drivers\\'.$settings['driver'];
                $this->db = new $driver();
                $this->db->setSettings($settings);
                $this->db->connect();
           }
        }

        return $this->db;
    }

    /**
     * Get variable from $_GET
     * @param string $var
     * @return mixed
     */
    public function get($var = null)
    {
        $result = null;

        if (is_null($var)) {
            $result = $_GET;
        } elseif(isset($_GET[$var])) {
            $result = $_GET[$var];
        }

        return $result;
    }

    /**
     * Get variable from $_POST
     * @param string $var
     * @return mixed
     */
    public function post($var = null)
    {
        $result = null;

        if (is_null($var)) {
            $result = $_POST;
        } elseif(isset($_POST[$var])) {
            $result = $_POST[$var];
        }

        return $result;
    }

    /**
     * Get variable from $_SERVER
     * @param string $var
     * @return mixed
     */
    public function server($var = null)
    {
        $result = null;

        if (is_null($var)) {
            $result = $_SERVER;
        } elseif(isset($_SERVER[$var])) {
            $result = $_SERVER[$var];
        }

        return $result;
    }

    /**
     * Get variable from $_COOKIE
     * @param string $var
     * @return mixed
     */
    public function cookie($var = null)
    {
        $result = null;

        if (is_null($var)) {
            $result = $_COOKIE;
        } elseif(isset($_COOKIE[$var])) {
            $result = $_COOKIE[$var];
        }

        return $result;
    }
    
    /**
     * Get variable from $_SESSION
     * @param string $var
     * @param string $newValue
     * @return mixed
     */
    public function session($var = null, $newValue = null)
    {
        $result = null;

        if (is_null($var)) {
            $result = $_SESSION;
        } elseif (isset($_SESSION[$var]) && empty($newValue)) {
            $result = $_COOKIE[$var];
        } elseif (isset($_SESSION[$var]) && !empty($newValue)) {
            $result = $_SESSION[$var] = $newValue;
        }

        return $result;
    }


    /**
     * Parse jFramework Settings
     */
    private function reloadData()
    {
        // Define .ini files directory
        Registry::setDir($this->rootDir . '/Config/');
        
        // Define rootDir variable
        Registry::set('rootDir', $this->rootDir);

        // Define serverName variable
        Registry::set('serverName', $this->server('SERVER_NAME'));

        // Parse the whole configs directory
        Registry::parseDir();
    }

    /**
     * Update php.ini settings from app/configs/php.ini
     */
    private function iniUpdate()
    {
        // Loop all PHP.ini values
        foreach (Registry::get('PHP') as $key => $value) {
            // Define new value
            ini_set($key, $value);
        }
    }

    /**
     * Define Bootstrap Headers
     */
    private function headers()
    {
        // Check if PHP is not running on Console
        if (PHP_SAPI != 'cli') {
            // Start Session on Dispatcher
            session_name('jFramework-general');
            session_start();

            // Signature
            header('X-Powered-By: jFramework ' . Registry::get('jFramework.version'), true);

            // Setting content Type and Charset
            header('Content-Type: text/html; charset=' . Registry::get('APP.charset'), true);
        }
    }

    /**
     * Initialize jFramework
     */
    public function initialize()
    {
        // Registry baseDir
        Registry::set('baseDir', str_replace("\\", '/', dirname('/')));

        // Parse all .ini files
        $this->reloadData();

        // Update php.ini settings from app/configs/php.ini
        $this->iniUpdate();

        // Development environment
        if (Registry::get('APP.runtime_mode') == 'DEV') {
            // Report all errors
            error_reporting(E_ALL);
            ini_set('display_errors', 'On');
            ini_set('log_error', 'Off');
        } elseif (Registry::get('APP.runtime_mode') == 'LIVE') {
            error_reporting(E_ALL);
            ini_set('display_errors', 'Off');
            ini_set('log_error', 'On');
        }

        // Log errors
        ini_set('error_log', Registry::get('FOLDER.logs'). '/errors.log');

        // Define Bootstrap headers
        $this->headers();

        // Spawn Router
        $Router = new Router();

        // Define core OBJ
        $Router->core = $this;

        // Bootstrap
        echo $Router->bootstrap();
    }
}
