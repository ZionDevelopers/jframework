<?php
/**
 * jFramework
 *
 * @link https://github.com/ZionDevelopers/jframework/ The jFramework GitHub Project
 * @copyright 2010-2016, Júlio César de Oliveira
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
 * @copyright Copyright (c) 2010-2016, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
class XHTML
{    
    /**
     * Format XHTML
     * @param string $xhtml
     * @param string $basepath
     * @return string
     */
    public static function format($xhtml, $basepath)
    {
        // Define result failsafe
        $result = $xhtml;
        
        // Get App settings
        $settings = Registry::get('APP');
        
        // Detect if php is running on a WebServer
        if (PHP_SAPI != 'cli') {
        
            // XHTML Optimization Check
            if ((bool)$settings['xhtml-optimization']) {        
                // Detect if Tidy exists
                if (class_exists('tidy')) {
                    // Spawn Tidy
                    $tidy = new \tidy();

                    // Tidy Config
                    $config = array(
                        'indent' => true,
                        'alt-text' => '',
                        'clean' => true,
                        'output-xhtml' => true,
                        'wrap' => 20000000,
                        'indent-spaces' => 0
                    );

                    // Parse xhtml
                    $tidy->parseString($result, $config, $settings['xhtml-charset']);

                    // Clear and Repair XHTML
                    $tidy->cleanRepair();

                    // Convert Tidy OBJ to string
                    $result = (string)$tidy;      
                } elseif(PHP_SAPI == 'cli') {
                    // Strip XHTML
                    $result = strip_tags($result);
                }            
            }

            // BaseRef Fixer
            if ((bool)$settings['baseref-rewrite']) {
                // Match Rewrite all targets
                preg_match_all('/(href=")([^"]+)|(src=")([^"]+)|(this.src=\')([^\']+)/i', $result, $rewrites, PREG_SET_ORDER);
                
                // Remove empty rows
                array_walk($rewrites, function (&$val, $key) {
                    $val = array_filter($val);                    
                });
                
                // Loop by all rewrites
                foreach ($rewrites as $row) {
                    // Get match
                    $match = array_shift($row);
                    // Get attr
                    $attr = array_shift($row);
                    // Get url
                    $url = array_shift($row);
                    
                    // Check if url is not external
                    if (!preg_match('/:/', $url)) { 
                        // Rewrite target
                        $result = str_replace($attr . $url, $attr . $basepath . $url, $result);
                    }
                }
            }

            // SEO Optimizations with Tidy
            if (class_exists('tidy') && (bool)$settings['seo-optimization']) {
               // SEO Optimizations
               $result = preg_replace("/\n|\r\n|\r|\t/", '', $result);
            }
        }
        
        return $result;
    }
}
