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

/**
 * Manage AutoLoader of classes
 * 
 * Created: 2014-06-08 04:38 PM (GMT -03:00)
 * Updated: 2014-06-08 07:55 PM (GMT -03:00)
 * @version 0.2.1
 * @see https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md 
 * @package PSR
 * @copyright Copyright (c) 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
class autoload
{
    
    /**
     * List of path to search
     * @var array
     */
    private static $paths = array();
    
    /**
     * Add path to list
     * @param string $path
     */
    public static function addPath($path){
        self::$paths[] = $path;
    }
    
    /**
     * PSR-0 Autoloader
     * @param string $className
     */
    public static function load($className) {
        $className = ltrim($className, '\\');
        $fileName = '';
        $namespace = '';

        if ($lastNsPos = strrpos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }

        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

        // Check paths
        foreach (self::$paths as $path) {
            // Check if file exists on this path
            if (file_exists($path . $fileName)) {
                // Require file
                require $path . $fileName;
            }
        }
    }
    
    /**
     * Register AutoLoader
     * 
     * @param boolean $prepend
     */
    public static function register($prepend = false){
        spl_autoload_register('self::load', true, $prepend);
    }
    
    /**
     * Unregister AutoLoader
     * 
     * @param boolean $prepend
     */
    public static function unregister(){
        spl_autoload_unregister('self::load');
    }
}
