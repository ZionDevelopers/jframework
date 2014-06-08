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
 * To Find
 *
 * @param string $what        	
 * @param string $context        	
 * @return boolean
 */
function find($what, $context)
{
    return (stripos($context, $what) !== false);
}

/**
 * Decode URL to UTF8
 *
 * @param string $value        	
 */
function urldec(&$value)
{
    if (is_string($value)) {
        $value = urldecode($value);
        $value = rawurldecode($value);
        $value = utf8_encode($value);
    }
}

/**
 * Encode string for URL
 *
 * @param string $value        	
 * @return string
 */
function urlenc($value)
{
    if (is_string($value)) {
        $value = utf8_decode($value);
        $value = rawurlencode($value);
        $value = urlencode($value);
        $value = str_replace('%2520', '+', $value);
    }
    return $value;
}

/**
 * Convert Hex to Bin (reverse bin2hex)
 *
 * @param string $h        	
 * @return string
 */
if (!function_exists("hex2bin")){

    function hex2bin($h)
    {
        if (!is_string($h)) {
            return null;
        }
        
        $r = '';
        
        for ($a = 0; $a < strlen($h); $a += 2) {
            $r .= @chr(@hexdec($h {$a} . $h {($a + 1)}));
        }
        
        return $r;
    }
}
