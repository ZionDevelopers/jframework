<?php
/**
 * Class to manage Captchas
 * @!created 2013-09-17 01:58 PM
 * @!updated 2013-09-18 01:23 PM
 * @version 1.0.1
 * @copyright Copyright (c) 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
class captcha {
	/**
	 * Auto Detect File
	 *
	 * @return void
	 */
	public static function check() {
		if (! file_exists ( CAPTCHA_ENFORCER_FILE )) {
			chmod ( TMP_DIR, '0755' );
			file_put_contents ( CAPTCHA_ENFORCER_FILE, "" );
		}
	}
	
	/**
	 * Check if IP is Captcha Enforced
	 *
	 * @return boolean
	 */
	public static function isEnforced() {
		self::check ();
		return find ( CLIENT_IP, file_get_contents ( CAPTCHA_ENFORCER_FILE ) );
	}
	
	/**
	 * Generate Captcha IMAGE
	 *
	 * @param string $referer        	
	 * @return void
	 */
	public static function generate($referer) {
		self::check ();
		// Generate Text
		$text = str_shuffle ( 'CDFHJKNPRTUVXY49' );
		$text = substr ( $text, - 4 );
		$showText = tools::addSpaceText ( $text );
		$text = strtolower ( $text );
		
		if (! empty ( $referer )) {
			// Add Captcha to Session
			$_SESSION ['SYSTEM'] ['CAPTCHA'] [$referer] = $text;
			
			// Generate Captcha Image
			$captcha = new Image ( WEBROOT_DIR . '/img/captcha.jpg' );
			$captcha->newSize ( 125, 40 );
			$captcha->text ( $showText, 20, 10, 30, array ( 160, 160, 160 ), 'ITCKRIST' );
			// Show Captcha and Destroy memory Resources
			$captcha->show ();
			$captcha->destroy ();
			unset ( $captcha );
		}
	}
	
	/**
	 * Lock/UnLock IP Address on the CAPTCHA Enforce
	 *
	 * @param boolean $add        	
	 * @return void
	 */
	public static function enforce($add = true) {
		$enforced = file_get_contents ( CAPTCHA_ENFORCER_FILE );
		if (! $add) {
			if (self::isEnforced ()) {
				$enforced = str_replace ( "\r\n" . CLIENT_IP, "", $enforced );
			}
		} else {
			if (! self::isEnforced ()) {
				$enforced = $enforced . "\r\n" . CLIENT_IP;
			}
		}
		
		file_put_contents ( CAPTCHA_ENFORCER_FILE, $enforced );
	}
	
	/**
	 * Generate HTML Captcha <img>
	 *
	 * @param string $from        	
	 * @return string
	 */
	public static function generateHTML($from) {
		return '<img src="' . BASE_DIR . '/captcha?from=' . $from . '&_=' . mt_rand ( 111, 999 ) . '" rel="' . $from . '" title="Trocar Imagem" style="cursor:pointer" onclick="this.src=\'' . BASE_DIR . '/captcha?from=' . $from . '&_=\'+Math.random()" />';
	}
}

?>