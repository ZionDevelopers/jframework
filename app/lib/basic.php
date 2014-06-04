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

/**
 * Return the First (ZERO) element from array
 *
 * @param array $array        	
 * @return string
 */
function z(array $array)
{
    return $array [0];
}

/**
 * Function to convert IP address (xxx.xxx.xxx.xxx) to IP number (0 to 256^4-1)
 *
 * @param string $IPaddr        	
 * @return integer
 */
function dot2LongIP($IPaddr)
{
    if ($IPaddr == "") {
        return 0;
    } else {
        $ips = explode(".", $IPaddr);
        return ($ips [3] + $ips [2] * 256 + $ips [1] * 256 * 256 + $ips [0] * 256 * 256 * 256);
    }
}

// Register Autoload to Automatic load of Classes
spl_autoload_register(function ($className)
{
    $className = ltrim($className, '\\');
    $fileName = '';
    $namespace = '';

    if ($lastNsPos = strrpos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }

    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
    
    $fileName = LIB_DIR . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . $fileName;

    if (file_exists($fileName)) {
        require $fileName;
    }
});
