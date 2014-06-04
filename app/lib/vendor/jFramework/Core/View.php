<?php
/**
 * jFramework
 *
 * @version 1.3.0
 * @link https://github.com/ZionDevelopers/jframework/ The jFramework GitHub Project
 * @copyright 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */

namespace jFramework\Core;

/**
 * To manage Views
 * 
 * Created: 2010-08-24 12:50 PM
 * Updated: 2014-06-03 10:50 AM
 * @version 1.2.0
 * @access public
 * @package jFramework
 * @subpackage Core
 * @copyright Copyright (c) 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
class View 
{
    /**
     * @var boolean
     * @access private
     * @static
     */
    private static $renderView = true;
    private static $renderLayout = true;

    /**
     * @var array
     * @access private
     * @static
     */
    private static $vars = array();

    /**
     * @var string
     * @access public
     * @static
     */
    public static $contents = '';

    /**
     * To get some Var
     *
     * @access public
     * @param string $var        	
     * @param string $controller        	
     * @return mixed
     * @static
     */
    public static function getVar($var, $controller = CONTROLLER)
    {
        return self::$vars [$controller] [$var];
    }

    /**
     * To Parse View
     *
     * @access public
     * @param string $file 
     * @param boolean $forceRender 
     * @static      	
     */
    public static function renderView($file, $forceRender = false)
    {
        $contents = '';

        if (self::check($file) && (self::$renderView || $forceRender)) {
            // Check if exists defined vars to this view
            if (isset(self::$vars [$file])) {
                // Setting all View Vars
                foreach (self::$vars [$file] as $key => $var) {
                    $$key = $var;
                }
            }

            $file = VIEWS_DIR . '/' . $file . '.php';

            ob_start();
            require ($file);
            $contents = ob_get_contents();
            ob_end_clean();
        }
        
        return $contents;
    }

    /**
     * Render Element
     *
     * @access public
     * @param string $file        	
     * @param array $vars        	
     * @return string
     * @static
     */
    public static function element($file, array $vars = array())
    {
        $file = '_elements/' . $file;
        
        self::set($vars, $file);
        $contents = self::renderView($file);
        unset(self::$vars [$file]);
        
        return $contents;
    }

    /**
     * To check if a view exists
     *
     * @access public
     * @param string $file        
     * @return boolean
     * @static	
     */
    public static function check($file)
    {
        return file_exists(VIEWS_DIR . '/' . $file . '.php');
    }

    /**
     * To parse Layout
     *
     * @access public
     * @param string $contents        	
     * @param string $file
     * @param boolean $forceRender
     * @static
     */
    public static function renderLayout($contents, $file = LAYOUT_PAGE, $forceRender = false)
    {
        $layout = '';

        self::$contents = $contents;

        if (self::check('_layouts/' . $file) && (self::$renderLayout || $forceRender)) {
            $layout = self::renderView('_layouts/' . $file, $forceRender);
        }

        return $layout;
    }

    /**
     * To disable or enable auto view Renderization
     *
     * @access public
     * @param boolean $val        	
     * @static
     */
    public static function autoView($val = false)
    {
        self::$renderView = $val;
    }

    /**
     * To disable or enable auto layout Renderization
     *
     * @access public
     * @param boolean $val
     * @static
     */
    public static function autoLayout($val = false)
    {
        self::$renderLayout = $val;
    }

    /**
     * To disable or enable auto view and layout Renderization
     *
     * @access public
     * @param boolean $val        	
     * @static
     */
    public static function autoRender($val = false)
    {
        self::autoView($val);
        self::autoLayout($val);
    }

    /**
     * To set Vars into Views
     *
     * @access public
     * @param array $vars        	
     * @param string $view 
     * @static       	
     */
    public static function set(array $vars, $view = CONTROLLER)
    {
        foreach ($vars as $key => $val) {
            self::$vars [$view] [$key] = $val;
        }
    }

    /**
     * To Set a Flash Message
     *
     * @access public
     * @param string $message
     * @static
     */
    public static function setFlash($message)
    {
        $_SESSION ['SYSTEM'] ['MESSAGE'] = $message;
    }

    /**
     * To get a Flash Message
     *
     * @access public
     * @return string|null
     * @static
     */
    public static function getFlash()
    {
        return (isset($_SESSION ['SYSTEM'] ['MESSAGE']) ? $_SESSION ['SYSTEM'] ['MESSAGE'] : null);
    }

    /**
     * To delete a Flash Message
     * 
     * @access public
     * @static
     */
    public static function delFlash()
    {
        if (isset($_SESSION ['SYSTEM'] ['MESSAGE'])) {
            $_SESSION ['SYSTEM'] ['MESSAGE'] = null;
        }
    }

    /**
     * To get an email template
     *
     * @access public
     * @param string $template        	
     * @param array $vars        	
     * @return string
     * @static
     */
    public static function email($template, array $vars = array())
    {
        self::set($vars, 'emails/' . $template);
        $contents = self::renderView('emails/' . $template, true);
        return self::renderLayout($contents, 'email', true);
    }
}
