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

namespace jFramework\MVC;

use jFramework\MVC\View\AbstractView;

/**
 * Generate view
 * 
 * Created: 2010-06-08 08:33 PM
 * Updated: 2014-06-08 08:33 AM
 * @version 0.0.1 
 * @package jFramework
 * @subpackage MVC
 * @copyright Copyright (c) 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
class View extends AbstractView
{        
    /**
     * Set View file
     * @param string $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }
    
    /**
     * To Parse View
     * @return string	
     */
    public function render($file = '')
    {    
        // Return view rendered
        return parent::render($this->file);
    }
}
