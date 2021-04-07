<?php
/**
 * jFramework
 *
 * @link https://github.com/ZionDevelopers/jframework/ The jFramework GitHub Project
 * @copyright 2010-2016, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */

namespace jFramework\Image;

use jFramework\Image\Core;
use jFramework\Core\Registry;

/**
 * Class to manage Captchas
 * 
 * Created: 2013-09-17 01:58 PM
 * Updated: 2014-06-03 09:47 AM 
 * @version 1.2.0 
 * @package jFramework
 * @subpackage Core
 * @copyright Copyright (c) 2010-2018, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
class Captcha
{

    public $width = 195;
    public $height = 50;
    
    private $enforcer_file = null;
    
    public function __construct()
    {
        $this->enforcer_file = Registry::get('FOLDER.logs') . '/enforcer.log';
    }
    
    /**
     * Auto Detect File
     *
     * @access private
     * @return void
     */
    private function check()
    {
        // Check if folder is writeable
        if (!is_writeable(dirname($this->enforcer_file))) {
            // Try to fix permissions
            chmod(dirname($this->enforcer_file), 0755);
        }
        
        // Check if captcha enforcer file exists
        if (!file_exists($this->enforcer_file)) {
            // Create a captcha enforcer empty file
            file_put_contents($this->enforcer_file, '');
        }
    }

    /**
     * Check if IP is Captcha Enforced
     *     
     * @return boolean
     */
    public function isEnforced()
    {
        // Enforcer file check
        self::check();
        return find(CLIENT_IP, file_get_contents($this->enforcer_file));
    }
    
        /**
     * Add Space Between Text
     *     
     * @param string $text       	
     * @return string
     * @static
     */
    public function addSpaceText($text)
    {
        $newText = '';
        $n = strlen($text);
        
        for ($i = 0; $i < $n; $i ++) {
            $newText .= $text [$i] . ' ';
        }
        
        return $newText;
    }

    /**
     * Generate Captcha IMAGE
     *     
     * @param string $referer        	
     * @return void
     */
    public function generate($referer)
    {
        // Enforcer file check
        self::check();
        
        // Generate Text
        $text = str_shuffle('CDFHJKNPRTUVXY49');
        $text = substr($text, - 6);
        $showText = $this->addSpaceText($text);
        $text = strtolower($text);

        if (!empty($referer)) {
            // Add Captcha to Session
            $_SESSION ['SYSTEM'] ['CAPTCHA'] [$referer] = $text;
 
            // Generate Captcha Image
            $captcha = new Core(Registry::get('webroot') . '/img/captcha.jpg');
            $captcha->newSize($this->width, $this->height, true);
            $captcha->text($showText, 25, 10, 35, array(255, 0, 0), 'AnkeCall');
            
            // Show Captcha and Destroy memory Resources
            $captcha->show();
            $captcha->destroy();
            unset($captcha);
        }
    }

    /**
     * Lock/UnLock IP Address on the CAPTCHA Enforce
     *     
     * @param boolean $add        	
     * @return void
     */
    public function enforce($add = true)
    {
        $enforced = file_get_contents($this->enforcer_file);
        
        if (!$add) {
            if (self::isEnforced()) {
                $enforced = str_replace("\r\n" . CLIENT_IP, "", $enforced);
            }
        } else {
            if (!self::isEnforced()) {
                $enforced = $enforced . "\r\n" . CLIENT_IP;
            }
        }

        file_put_contents($this->enforcer_file, $enforced);
    }

    /**
     * Generate HTML Captcha <img>
     *     
     * @param string $from        	
     * @return string
     */
    public static function generateHTML($from)
    {
        return '<img src="captcha/generate?from=' . $from . '&' . mt_rand(1000, 9999) . '" rel="' . $from . '" title="Reload captcha" style="cursor:pointer" onclick="this.src=\'captcha/generate?from=' . $from . '&\'+Math.random()" />';
    }
    
    /**
     * Verify captcha
     * @param string $text
     * @param string $from
     * @return boolean
     */
    public static function verify($text, $from)
    {         
        return strtoupper($_SESSION ['SYSTEM'] ['CAPTCHA'] [$from]) === strtoupper($text);
    }
}
