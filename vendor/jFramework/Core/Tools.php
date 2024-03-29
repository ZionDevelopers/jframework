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

namespace jFramework\Core;

use jFramework\Core\Registry;
use App\Model\IPBlock;

/**
 * Tools to do almost all things
 * 
 * Created: 2010-07-24 10:25 AM
 * Updated: 2023-06-13 12:35 PM
 * @version 1.2.2
 * @package jFramework
 * @subpackage Core
 * @copyright Copyright (c) 2010-2018, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
class Tools
{    
    /**
     * To Debug
     *     
     * @param mixed $any,...  
     * @static   	
     */
    public static function debug()
    {
        if (Registry::get('APP.runtime_mode') == 'DEV') {
            echo '<pre>';
            
            foreach (func_get_args() as $arg) {
                print_r($arg);
            }
            
            echo '</pre>';
        }
    }

    /**
     * To report error
     *     
     * @param int $error  
     * @static      	
     */
    public static function error($error = 404)
    {
        self::redir(BASE_DIR . '/errors/' . $error . '?referer=' . CONTROLLER);
    }

    /**
     * To format a value into Reais
     *     
     * @param string $price        	
     * @param boolean $noCents       	
     * @return string
     * @static
     */
    public static function formatPrice($price, $noCents = false)
    {
        $newPrice = 'R$ ' . number_format(self::price2Float($price), 2, ",", ".");
        if ($noCents) {
            $newPrice = explode(',', $newPrice);
            $newPrice = $newPrice [0];
        }
        return $newPrice;
    }

    /**
     * To clean a formated Price into Float
     *     
     * @param string $value        	
     * @return float
     * @static
     */
    public static function price2Float($value)
    {
        $string = (string) $value;

        if (strpos($string, ".") !== false && strpos($string, ",") !== false) {
            $string = str_replace(".", "", $string);
            $string = str_replace(",", ".", $string);
        } elseif (strpos($string, ".") === false && strpos($string, ",") !== false) {
            $string = str_replace(",", ".", $string);
        }
        return (float) $string;
    }

    /**
     * To get a file extension
     *     
     * @param string $file        	
     * @return string
     * @static
     */
    public static function fileType($file)
    {
        $ext = explode(".", $file);
        $ext = $ext [count($ext) - 1];
        return strtolower($ext);
    }

    /**
     * To check if this file request has maded by AJAX
     *     
     * @param boolean $quit       	
     * @param boolean $blockIP
     * @return boolean
     * @static
     */
    public static function checkAjax($quit = true, $blockIP = true)
    {
        $result = false;

        if (isset($_SERVER ["HTTP_X_REQUESTED_WITH"])) {
            if ($_SERVER ["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
                if ($quit) {
                    // Block IP enabled?
                    if ($blockIP) {
                        // Block IP
                        self::blockIP();
                    }
                    exit('Access denied');
                }
            } else {
                $result = true;
            }
        } else {
            if ($quit) {
                // Block IP enabled?
                if ($blockIP) {
                    // Block IP
                    self::blockIP();
                }
                exit('Access denied');
            }
        }

        return $result;
    }

    /**
     * To check if an url exists
     *     
     * @param $url string        	
     * @return boolean
     * @static
     */
    public static function urlExists($url)
    {
        $headers = get_headers($url, true);
        $response = $headers [0];

        return strpos($response, "200 OK") !== false;
    }

    /**
     * TO Redir
     *     
     * @param string $url 
     * @static      	
     */
    public static function redir($url)
    {
        header('Location: ' . $url, true);
        exit();
    }

    /**
     * Test if a file exists and if are not empty
     *     
     * @param string $file        	
     * @return boolean
     * @static
     */
    public static function testFile($file)
    {
        $result = false;
        
        if (file_exists($file)) {
            if (is_readable($file)) {
                if (filesize($file) > 0) {
                    $result = true;
                }
            }
        }

        return $result;
    }

    /**
     * Delete a list of files
     *     
     * @param array $list   
     * @static     	
     */
    public static function unlinkArray(array $list)
    {
        foreach ($list as $file) {
            if (!empty($file)) {
                if (file_exists($file)) {
                    @unlink($file);
                }
            }
        }
    }

    /**
     * To remove a complete folder
     *     
     * @param string $dir 
     * @static    	
     */
    public static function removeDir($dir)
    {
        $files = glob($dir . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (substr($file, - 1) == '/') {
                self::removeDir($file);
            } else {
                unlink($file);
            }
        }

        if (is_dir($dir)) {
            rmdir($dir);
        }
    }

    /**
     * Check if folder exists and if is writable
     *     
     * @param string $path        	
     * @return string
     * @static
     */
    public static function fixFolder($path)
    {
        if (!file_exists($path)) {
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }
        }elseif(!is_writeable($path) && file_exists($path)){
            chmod($path, 0777);
        }        
    }

    /**
     * To Truncate an Text
     *     
     * @param string $txt        	
     * @param integer $max   
     * @param string $end
     * @return string
     * @static   	
     */
    public static function truncateText($txt, $max, $end = '...')
    {
        $txtSize = strlen($txt);
        
        if ($txtSize >= $max) {
            $txt = substr($txt, 0, $max - strlen($end));
            $txt .= $end;
        }
        
        return $txt;
    }

    /**
     * Add Space Between Text
     *     
     * @param string $text       	
     * @return string
     * @static
     */
    public static function addSpaceText($text)
    {
        $newText = '';
        $n = strlen($text);
        
        for ($i = 0; $i < $n; $i ++) {
            $newText .= $text [$i] . ' ';
        }
        
        return $newText;
    }

    /**
     * Slug function
     *     
     * @param string $str       	
     * @return string
     * @static
     */
    public static function slug($str)
    {
        // Convert UTF-8 to ASCII-TRANSLIT
        $str = iconv(CHARSET, 'ASCII//TRANSLIT', $str);
        // Convert str to lowerCase
        $str = strtolower($str);
        // Replace spaces with -
        $str = str_replace(" ", "-", $str);
        // Remove all characters that is not a-z and -
        $str = preg_replace('/[^a-z,0-9,-]/', '', $str);

        return $str;
    }

    /**
     * Format hours
     *     
     * @param integer $hours        	
     * @return string
     * @static
     */
    public static function formatHours($hours)
    {
        $result = '';

        if ($hours == 1) {
            $result = '1 hora';
        } elseif ($hours < 24 && $hours > 1) {
            $result .= $hours . ' horas';
        }

        if ($hours >= 24 && $hours < 48) {
            $result = '1 dia';
        } elseif ($hours >= 24 && $hours < 168) {
            $result .= round($hours / 24) . ' dias';
        }

        if ($hours >= 168 && $hours < 336) {
            $result = '1 semana';
        } elseif ($hours > 168 && $hours < 672) {
            $result .= round($hours / 168) . ' semanas';
        }

        if ($hours >= 672 && $hours < 1344) {
            $result = '1 mês';
        } elseif ($hours >= 672 && $hours < 8064) {
            $result .= round($hours / 672) . ' meses';
        }

        if ($hours >= 8064 && $hours < 16128) {
            $result = '1 ano';
        } elseif ($hours >= 16128) {
            $result .= round($hours / 8064) . ' anos';
        }

        return $result;
    }

    /**
     * Phone Encode
     * Safe to Lammers!
     *     
     * @param string $phone        	
     * @return string
     * @static
     */
    public static function phoneEncode($phone)
    {
        // Compress and mess the encrypted text
        $result = gzcompress($phone, 9);
        // Format to a readable text
        $result = convert_uuencode($result);
        // Format to hex from Binary
        $result = bin2hex($result);

        return $result;
    }

    /**
     * Phone Decode
     * Safe to Lammers!
     *     
     * @param string $phone        	
     * @return string
     * @static
     */
    public static function phoneDecode($phone)
    {
        $result = preg_replace('/[^a-z,0-9]/', '', $phone);
        // Format to hex from Binary
        $result = @hex2bin($result);
        // Format to a readable text
        $result = @convert_uudecode($result);
        // Compress and mess the encrypted text
        $result = @gzuncompress($result);

        return $result;
    }

    /**
     * Encrypt a text (Encrypting 3 times with 2 keys by RIJNDAEL 256 bit
     * Cryptographic, messing the text a loot! and coding the text a loot too!
     * and converting and reconverting): With that you protect the text and
     * leaving the text 470% larger than the original
     *     
     * @param string $str
     * @param string $key
     * @return string
     * @static
     */
    public static function encrypt($str, $key1, $key2)
    {
        // Get MD5 SHA1 Keys HASH
        $key1 = md5(sha1($key1));
        $key2 = md5(sha1($key2));

        // Encrypt String
        $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key1, $str, MCRYPT_MODE_CFB, $key2);
        // Compress and mess the encrypted text
        $encrypted = gzcompress($encrypted, 9);
        // Format to a readable text
        $encrypted = convert_uuencode($encrypted);
        // Encrypt again by interting the keys position
        $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key2, $encrypted, MCRYPT_MODE_CFB, $key1);
        // Format to a readable text
        $encrypted = base64_encode($encrypted);
        // Encrypt one more time with the 2 keys concatenaed by md5 hash
        $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key1 . $key2), $encrypted, MCRYPT_MODE_CFB, md5($key2 . $key1));
        // Convert the Binary Encryted code to Hexa code (Readable text)
        $encrypted = bin2hex($encrypted);
        // Revert string
        $encrypted = strrev($encrypted);
        
        return $encrypted;
    }

    /**
     * Decrypt a text encrypted by tools::encrypt function Decrypting 3 times
     * with 2 keys by RIJNDAEL 256 bit Cryptographic, unmessing the text a loot!
     * and uncoding the text a loot too! and unconverting): With that you
     * unprotect the text and leaving the text 470% smaller than the "original"
     * (Encrypted text) but EXACLY THE SAME as the ORIGINAL TEXT
     *     
     * @param string $str
     * @param string $key
     * @return string
     * @static
     */
    public static function decrypt($str, $key1, $key2)
    {
        // Get MD5 Sha1 Keys HASH
        $key1 = md5(sha1($key1));
        $key2 = md5(sha1($key2));

        // UnRevert string
        $decrypted = strrev($str);
        // Convert hexa code to original binary code
        $decrypted = hex2bin($decrypted);
        // Uncrypt by 2 keys concatenaed by md5 hash
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key1 . $key2), $decrypted, MCRYPT_MODE_CFB, md5($key2 . $key1));
        // Unformat from readable text
        $decrypted = base64_decode($decrypted);
        // Uncrypt by interting the keys position
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key2, $decrypted, MCRYPT_MODE_CFB, $key1);
        // UnFormat from a readable text
        $decrypted = convert_uudecode($decrypted);
        // Uncompress and unmess the text
        $decrypted = gzuncompress($decrypted);
        // Uncrypt the text
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key1, $decrypted, MCRYPT_MODE_CFB, $key2);
        // Remove the ODD spaces and make it clean!
        $decrypted = trim($decrypted);
        
        return $decrypted;
    }
    
    /**
     * Get client IP
     * @return string
     */
    public static function getClientIP()
    {
        if (!empty($_SERVER['CF-Connecting-IP'])) {
            $ip = $_SERVER['CF-Connecting-IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];  
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) { 
            $ip = $_SERVER['REMOTE_ADDR']; 
        } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP']; 
        } 
        
        return preg_replace('/[^a-z0-9:,.]/', '', $ip);
    }
    
    /**
     * Block IP from server
     * @param string $ip
     */
    public static function blockIP($ip = null)
    {
        // Check if ip was sent
        if (empty($ip)) {
            // Get IP
            $ip = self::getClientIP();
        }
        
        // Get WebRoot folder
        $folderPath = Registry::get('webroot');
        // Define file
        $file = $folderPath . '/.htaccess';
        // Get .htaccess contents
        $htaccessContent = file_get_contents($file);
        
        // Check if ip is already blocked
        if (strpos($htaccessContent, $ip) === false) {
            // Update ip block line
            $newHtaccessContent = str_replace('#IPBLOCK:END', "\n    #" . date('Y-m-d H:i:s') . "\n    Deny from " . $ip . "\n    #IPBLOCK:END", $htaccessContent);

            // Update .htaccess
            file_put_contents($file, $newHtaccessContent);
            
            // Create IP Block Object
            $IPBlock = new IPBlock();

            // Setup IP Block Data
            $data = [];
            $data['ip'] = $ip;
            $data['date'] = date('Y-m-d H:i:s');
            $data['post'] = json_encode($_POST);
            $data['get'] = json_encode($_GET);
            $data['server'] = json_encode($_SERVER); 
            $data['hits'] = 1;
            $data['url'] = $_SERVER['REQUEST_URI'];
            $data['enabled'] = 1;

            // Save Array to Database
            $IPBlock->set($data);
            $IPBlock->save();
        }
    }
    
    /**
     * Get Hostname by name (IPv6)
     * @link https://www.php.net/manual/en/function.gethostbyname.php#70936 PHP.NET
     * @param string $host
     * @param boolean $try_a
     * @return boolean
     */
    public static function gethostbynamev6($host, $try_a = false)
    {
        // get AAAA record for $host
        // if $try_a is true, if AAAA fails, it tries for A
        // the first match found is returned
        // otherwise returns false

        $dns = self::gethostbynamel6($host, $try_a);
        if ($dns == false) { 
            return false;             
        } else { 
            return $dns[0];
        }
    }

    /**
     * Get host by name helper for v6
     * @link https://www.php.net/manual/en/function.gethostbyname.php#70936 PHP.NET
     * @param string $host
     * @param boolean $try_a
     * @return boolean
     */
    public static function gethostbynamel6($host, $try_a = false)
    {
        // get AAAA records for $host,
        // if $try_a is true, if AAAA fails, it tries for A
        // results are returned in an array of ips found matching type
        // otherwise returns false

        $dns6 = dns_get_record($host, DNS_AAAA);
        if ($try_a == true) {
            $dns4 = dns_get_record($host, DNS_A);
            $dns = array_merge($dns4, $dns6);
        } else { 
            $dns = $dns6;            
        }
        $ip6 = array();
        $ip4 = array();
        
        foreach ($dns as $record) {
            if ($record["type"] == "A") {
                $ip4[] = $record["ip"];
            }
            if ($record["type"] == "AAAA") {
                $ip6[] = $record["ipv6"];
            }
        }
        if (count($ip6) < 1) {
            if ($try_a == true) {
                if (count($ip4) < 1) {
                    return false;
                } else {
                    return $ip4;
                }
            } else {
                return false;
            }
        } else {
            return $ip6;
        }
    }
}
