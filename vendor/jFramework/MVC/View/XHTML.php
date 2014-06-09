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

namespace jFramework\MVC\View;

use jFramework\Core\Registry;

/**
 * Generante and Process XHTML
 * 
 * Created: 2010-06-09 12:21 PM (GMT -03:00)
 * Updated: 2014-06-09 12:21 PM (GMT -03:00)
 * @version 0.0.1 
 * @package jFramework
 * @subpackage MVC
 * @copyright Copyright (c) 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
class XHTML
{    
    public static function format($xhtml)
    {
        // Define result failsafe
        $result = $xhtml;
        
        // Detect if Tidy exists
        if(class_exists('tidy')){
            // Spawn Tidy
            $tidy = new \tidy();
            // Parse xhtml
            $tidy->parseString($xhtml, array('indent' => true, 'output-xhtml' => true, 'wrap' => 200), Registry::get('APP.xhtml-charset'));
            // Clear and Repair XHTML
            $tidy->cleanRepair();
            
            $result = $tidy;
        }
        
        return $result;
    }
}
