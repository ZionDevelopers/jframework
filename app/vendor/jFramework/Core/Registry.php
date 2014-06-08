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

namespace jFramework\Core;

/**
 * Registry is to manage jFramework Settings
 * 
 * Created: 2014-06-08 04:38 PM (GMT -03:00)
 * Updated: 2014-06-08 07:51 PM (GMT -03:00)
 * @version 0.0.5
 * @access public
 * @package jFramework
 * @subpackage Core
 * @copyright Copyright (c) 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
class Registry
{
    private static $registry = array();
    private static $ext = '.ini';
    private static $dir;

    /**
     * Parse .ini file
     * @param string $file
     */
    public static function parse($file)
    {
        // Check if file can be readed
        if (is_readable($file )) {
            // Parse .ini file
            $configs = parse_ini_file($file, true);
            // Merge brand new parsed array with the whole registry
            self::$registry = array_merge(self::$registry, $configs);

            // Loop registry to parse register variables
            foreach (self::$registry as $i => $config) {
                // Replace all ini vars with the vars from the registry
                self::$registry [$i] = preg_replace_callback(
                        '/\$([A-Z,a-z,0-9,.,_,-]+)/',
                        'self::parseIniVars', 
                        $config
                );
            }
        }
    }

    /**
     * Parse INI Variables
     * @param array $var
     * @return string
     */
    public static function parseIniVars($var)
    {
        // Replace $ with nothing from key 0 from the array
        $var = str_replace('$', '', $var[0]);

        // Check if variable exists
        if(self::get($var) !== null){
            // Return parsed variable
            return self::get($var);
        }

        // Return non-replaced variable
        return '$' . $var;
    }

    /**
     * Define directory where the settings files will be placed / parsed
     * @param type $dir
     */
    public static function setDir($dir)
    {
        // Define .ini directory
        self::$dir = $dir;
    }

    /**
     * Get a variable from Registry
     * @param string $var
     * @return mixed
     */
    public static function get($var)
    {
        // Define failsafe result
        $result = null;
        
        // Check if array key separator exists
        if (strpos($var, '.') !== false) {
            // Explode Array key separator
            $var = explode('.', $var);
            
            // Check if was found a registry with this parsed variable
            if (isset(self::$registry[ $var[0] ][ $var[1] ])) {
                // Return parsed variable
                $result = self::$registry[ $var[0] ][ $var[1] ];
            }
        // Check if variable exists
        }elseif (isset(self::$registry[$var])) {
            $result = self::$registry [$var];
        }

        return $result;
    }

    /**
     * Set a variable to Registry
     * @param string $var
     * @param string $contents
     */
    public static function set($var, $contents) {
        self::$registry [$var] = $contents;
    }

    /**
     * Return all the registry
     * @return array
     */
    public static function dump() {
        return self::$registry;
    }

    /**
     * Parse all .ini files from self::$dir
     */
    public static function parseDir(){
        // List all files .ini from the configs dir
        foreach(glob(self::$dir . '*' . self::$ext) as $file){
            // Parse .ini file
            self::parse($file);
        }
    }
}
