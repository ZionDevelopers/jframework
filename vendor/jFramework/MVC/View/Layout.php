<?php
/**
 * jFramework
 *
 * @link https://github.com/ZionDevelopers/jframework/ The jFramework GitHub Project
 * @copyright 2010-2023, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */

namespace jFramework\MVC\View;

use jFramework\MVC\View\AbstractView;
use jFramework\Core\Registry;

/**
 * Generate view
 * 
 * Created: 2010-06-08 08:33 PM
 * Updated: 2014-06-08 08:33 AM
 * @version 0.0.1 
 * @package jFramework
 * @subpackage MVC
 * @copyright Copyright (c) 2010-2018, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
class Layout extends AbstractView
{    
    /**
     * Get view contents
     * @return string
     */
    public function getView()
    {
        // Return view contents
        return $this->view;
    }
     
    /**
     * Define Layout file
     * @param string $file
     */
    public function setFile($file)
    {
        $this->file = Registry::get('FOLDER.layout') . '/' . $file . $this->fileExt;
    }
    
    /**
     * To Parse Layout
     * @param string $view
     */
    public function render($view)
    {   
        // Define View
        $this->view = $view;
        
        $result = false;

        // Define baseDir
        $this->baseDir = dirname($_SERVER['REQUEST_URI']);

        // Check if there is a layout
        if ($this->layoutCheck($this->file)) {
            // Return rendered layout
            $result = parent::render($this->file);
        } 
        
        return $result;
    }
}
