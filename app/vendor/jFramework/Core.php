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

// Use Database Manager
use jFramework\Database\Drivers\MySQL as Database;

// Use Tools
use jFramework\Core\Tools;

// Use View
use jFramework\Core\View;

// Use Registry
use jFramework\Core\Registry;

/**
 * jFramework Core Operations Handler
 * 
 * Created: 2014-06-08 04:38 PM (GMT -03:00)
 * Updated: 2014-06-08 07:10 PM (GMT -03:00)
 * @version 0.0.3
 * @access public
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
        Registry::setDir($this->rootDir . '/app/configs/');  
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
        // Fix folders
        Tools::folder ( Registry::get('FOLDER.session') );

        // Start Session on Dispatcher
        session_start ();

        // Signature
        header('X-Powered-By: jFramework ' . Registry::get('jFramework.version'), true);

        // Setting content Type and Charset
        header('Content-Type: text/html; charset=' . Registry::get('APP.charset'), true);
    }
    
    /** 
     * Bootstrap App
     */
    public function bootstrap()
    {        
        // Parse all .ini files
        $this->reloadData();
        
        // Update php.ini settings from app/configs/php.ini
        $this->ini_update();

        // Define Bootstrap headers
        $this->headers();

        // Check if the controller is private
        if (substr(CONTROLLER, - 1) == '_') {
            Tools::error(406);
        }

        // Creating new database manager OBJ
        $db = new Database;

        // Setting database settings
        $db->setSettings(Registry::get('database'));

        // Connecting to mysql database
        $db->connect();

        // Remove empty keys from get data
        $_GET = array_filter($_GET);

        // Require Controller
        if (file_exists(CONTROLLERS_DIR . '/' . CONTROLLER . '.php')) {
            // Require Controller
            require CONTROLLERS_DIR . '/' . CONTROLLER . '.php';

            // Defining Render Options
            define('LAYOUT_PAGE', isset($layout) ? $layout : LAYOUT_DEFAULT );
            define('VIEW_PAGE', isset($view) ? $view : CONTROLLER );

            // Check out View
            $contents = View::renderView(VIEW_PAGE);
        } else {
            // Generate a custom error page
            Tools::error(404);
        }

        // Start Tidy Cache Getter
        if (class_exists('tidy') && $contents != '' && LAYOUT_PAGE == LAYOUT_DEFAULT) {
            ob_start();
        }

        // Request Layout controller
        require CONTROLLERS_DIR . '/_layouts/' . LAYOUT_PAGE . '.php';

        // Closing MySQL database connectiong
        $db->close();

        // Format XHTML with Tidy (If available)
        if (class_exists('tidy') && $contents != '' && LAYOUT_PAGE == LAYOUT_DEFAULT) {
            // Start Tidy
            $tidy = new tidy ();
            // Parse contents
            $tidy->parseString(ob_get_contents(), array('indent' => true, 'output-xhtml' => true, 'wrap' => 200), 'utf8');
            // Clear and Repair xHTML
            $tidy->cleanRepair();

            // End Buffering and Flush output
            ob_end_clean();    

            // Output Fixed, Formated xHTML
            echo $tidy;
        }
    }
}