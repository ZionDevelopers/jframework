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

namespace jFramework\Image;

use jFramework\Image\Core;
use jFramework\Core\Registry;
use jFramework\Core\Tools;

/**
 * Class to manage Captchas
 * 
 * Created: 2013-09-17 01:58 PM
 * Updated: 2014-06-03 09:47 AM 
 * @version 1.2.0 
 * @package jFramework
 * @subpackage Core
 * @copyright Copyright (c) 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
class Captcha
{

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
        if (!file_exists($this->enforcer_file)) {
            file_put_contents($this->enforcer_file, "");
        }
    }

    /**
     * Check if IP is Captcha Enforced
     *     
     * @return boolean
     */
    public function isEnforced()
    {
        self::check();
        return find(CLIENT_IP, file_get_contents($this->enforcer_file));
    }

    /**
     * Generate Captcha IMAGE
     *     
     * @param string $referer        	
     * @return void
     */
    public function generate($referer)
    {
        self::check();
        // Generate Text
        $text = str_shuffle('CDFHJKNPRTUVXY49');
        $text = substr($text, - 4);
        $showText = Tools::addSpaceText($text);
        $text = strtolower($text);

        if (!empty($referer)) {
            // Add Captcha to Session
            $_SESSION ['SYSTEM'] ['CAPTCHA'] [$referer] = $text;
 
            // Generate Captcha Image
            $captcha = new Core(Registry::get('webroot') . '/img/captcha.jpg');
            $captcha->newSize(125, 40);
            $captcha->text($showText, 20, 10, 30, array(160, 160, 160), 'ITCKRIST');
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
    public function generateHTML($from)
    {
        return '<img src="' . BASE_DIR . '/captcha?from=' . $from . '&_=' . mt_rand(111, 999) . '" rel="' . $from . '" title="Trocar Imagem" style="cursor:pointer" onclick="this.src=\'' . BASE_DIR . '/captcha?from=' . $from . '&_=\'+Math.random()" />';
    }
}
