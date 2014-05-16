<?php
/**
 * jFramework
 *
 * @version 1.2
 * @copyright Copyright (c) 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */

/**
 * To Find
 *
 * @param string $what        	
 * @param string $context        	
 * @return boolean
 */
function find($what, $context) {
	return (stripos ( $context, $what ) !== false);
}

/**
 * Decode URL to UTF8
 *
 * @param string $value        	
 */
function urldec(&$value) {
	if (is_string ( $value )) {
		$value = urldecode ( $value );
		$value = rawurldecode ( $value );
		$value = utf8_encode ( $value );
	}
}

/**
 * Encode string for URL
 *
 * @param string $value        	
 * @return string
 */
function urlenc($value) {
	if (is_string ( $value )) {
		$value = utf8_decode ( $value );
		$value = rawurlencode ( $value );
		$value = urlencode ( $value );
		$value = str_replace ( '%2520', '+', $value );
	}
	return $value;
}

/**
 * Convert Hex to Bin (reverse bin2hex)
 *
 * @param string $h        	
 * @return string
 */
if (! function_exists ( "hex2bin" )) {
	function hex2bin($h) {
		if (! is_string ( $h )) {
			return null;
		}
		$r = '';
		for($a = 0; $a < strlen ( $h ); $a += 2) {
			$r .= @chr ( @hexdec ( $h {$a} . $h {($a + 1)} ) );
		}
		return $r;
	}
}

/**
 * Return the First (ZERO) element from array
 *
 * @param array $array        	
 * @return string
 */
function z(array $array) {
	return $array [0];
}

/**
 * Check if server is running SSL
 *
 * @return boolean
 */
function isSSL() {
	$result = false;
	if ($_SERVER ['SERVER_PORT'] == PRODUCTION_HTTPS_PORT || $_SERVER ['SERVER_PORT'] == DEBUG_HTTPS_PORT) {
		$result = true;
	}
	
	return $result;
}

/**
 * Check if the current HTTP/S port is Valid
 *
 * @return boolean
 */
function isValidWebPort() {
	$result = false;
	$sPort = $_SERVER ['SERVER_PORT'];
	
	if ($sPort == PRODUCTION_HTTPS_PORT || $sPort == DEBUG_HTTPS_PORT || $sPort == PRODUCTION_HTTP_PORT || $sPort == DEBUG_HTTPS_PORT) {
		$result = true;
	}
	
	return $result;
}

/**
 * Get Web Port
 *
 * @param string $secure        	
 * @return Ambigous <NULL, string>
 */
function getWebPort($secure = false) {
	$result = null;
	
	if ($secure) {
		if (DEBUG) {
			$result = DEBUG_HTTPS_PORT;
		} else {
			$result = DEBUG_HTTPS_PORT;
		}
	} else {
		if (DEBUG) {
			$result = DEBUG_HTTP_PORT;
		} else {
			$result = DEBUG_HTTP_PORT;
		}
	}
	
	return $result;
}

/**
 * Function to convert IP address (xxx.xxx.xxx.xxx) to IP number (0 to 256^4-1)
 *
 * @param string $IPaddr        	
 * @return integer
 */
function dot2LongIP($IPaddr) {
	if ($IPaddr == "") {
		return 0;
	} else {
		$ips = explode ( ".", $IPaddr );
		return ($ips [3] + $ips [2] * 256 + $ips [1] * 256 * 256 + $ips [0] * 256 * 256 * 256);
	}
}
?>