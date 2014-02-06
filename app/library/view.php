<?php
/**
 * To manage Views
 * Created 2010-08-24 12:50 PM
 * Updated 2010-08-04 13:40 PM
 * @version 1.1.0
 * @copyright Copyright (c) 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
class view {
	/**
	 * 
	 * @var boolean
	 */
	private static $renderView = true, $renderLayout = true;
	
	/**
	 * 
	 * @var array
	 */
	private static $vars = array ();
	
	/**
	 * 
	 * @var string
	 */
	public static $contents = '';
	
	/**
	 * To get some Var
	 * @param string $var
	 * @param string $controller
	 * @return mixed
	 */
	public static function getVar($var, $controller = CONTROLLER) {
		return self::$vars [$controller] [$var];
	}
	
	/**
	 * To Parse View
	 * @param string $file
	 */
	public static function renderView($file, $forceRender = false) {
		$contents = '';
		
		if (self::check ( $file ) && (self::$renderView || $forceRender)) {
			//Check if exists defined vars to this view
			if (isset ( self::$vars [$file] )) {
				//Setting all View Vars
				foreach ( self::$vars [$file] as $key => $var ) {
					$$key = $var;
				}
			}
			
			$file = VIEWS_DIR . '/' . $file . '.php';
			
			ob_start ();
			require ($file);
			$contents = ob_get_contents ();
			ob_end_clean ();
		}
		return $contents;
	}
	
	public static function element($file, array $vars = array()){
		$file = '_elements/' . $file;
		self::set($vars, $file);
		$contents = self::renderView($file);
		unset(self::$vars[$file]);
		return $contents;
	}
	
	/**
	 * To check if a view exists
	 * @param string $file
	 */
	public static function check($file) {
		return file_exists ( VIEWS_DIR . '/' . $file . '.php' );
	}
	
	/**
	 * To parse Layout
	 * @param string $contents
	 * @param string $file
	 */
	public static function renderLayout($contents, $file = LAYOUT_PAGE, $forceRender = false) {
		$layout = '';
		
		self::$contents = $contents;
		
		if (self::check ( '_layouts/' . $file ) && (self::$renderLayout || $forceRender)) {
			$layout = self::renderView ( '_layouts/' . $file, $forceRender );
		}
		
		return $layout;
	}
	
	/**
	 * To disable or enable auto view Renderization
	 * @param boolean $val
	 */
	public static function autoView($val = false) {
		self::$renderView = $val;
	}
	
	/**
	 * To disable or enable auto layout Renderization
	 * @param boolean $val
	 */
	public static function autoLayout($val = false) {
		self::$renderLayout = $val;
	}
	
	/**
	 * To disable or enable auto view and layout Renderization
	 * @param boolean $val
	 */
	public static function autoRender($val = false) {
		self::autoView ( $val );
		self::autoLayout ( $val );
	}
	
	/**
	 * To set Vars into Views
	 * @param array $vars
	 * @param string $view
	 */
	public static function set(array $vars, $view = CONTROLLER) {
		foreach ( $vars as $key => $val ) {
			self::$vars [$view] [$key] = $val;
		}
	}
	
	/**
	 * To Set a Flash Message
	 * @param $message
	 */
	public static function setFlash($message) {
		$_SESSION ['SYSTEM'] ['MESSAGE'] = $message;
	}
	
	/**
	 * To get a Flash Message
	 * @return string
	 */
	public static function getFlash() {
		return (isset ( $_SESSION ['SYSTEM'] ['MESSAGE'] ) ? $_SESSION ['SYSTEM'] ['MESSAGE'] : null);
	}
	
	/**
	 * To delete a Flash Message
	 */
	public static function delFlash() {
		if (isset ( $_SESSION ['SYSTEM'] ['MESSAGE'] )) {
			$_SESSION ['SYSTEM'] ['MESSAGE'] = null;
		}
	}
	
	/**
	 * To get an email template
	 * @param string $template
	 * @param array $vars
	 * @return string
	 */
	public static function email($template, array $vars = array()) {
		self::set ( $vars, 'emails/' . $template );
		$contents = self::renderView ( 'emails/' . $template, true );
		return self::renderLayout ( $contents, 'email', true );
	}
}