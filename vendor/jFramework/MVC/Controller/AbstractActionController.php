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

namespace jFramework\MVC\Controller;

use jFramework\MVC\View\Layout;
use jFramework\MVC\View;
use jFramework\Core\Registry;

/**
 * Base Action Controller who will Controllers will extend to
 * 
 * Created: 2014-06-08 08:06 PM (GMT -03:00)
 * Updated: 2014-06-09 04:50 PM (GMT -03:00)
 * @version 0.0.5 
 * @package jFramework
 * @subpackage MVC
 * @copyright Copyright (c) 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
abstract class AbstractActionController extends AbstractController
{
    /**
     * Default Action
     * 
     * @param array $get
     * @param array $post
     * @param array $data
     */
    public function indexAction ($get, $post, $data)
    {
        
    }
    
    /**
     * Render Layout
     * @param string $viewContents
     * @return string
     */
    public function layout($viewContents)
    {
        // Spawn new Layout
        $layout = new Layout();
        
        // Set layout
        $layout->setFile('default');
        
        // Set Page title
        $layout->title = Registry::get('APP.title');
        
        // Render Layout with view contents
        return $layout->render($viewContents);
    }
    
    /**
     * Page not found Action
     * @return string
     */
    public function notFoundAction($etc = '')
    {      
        $view = new View();
        
        // Get view file
        $file = Registry::get('FOLDER.error-view') . '/notFound' . $view->fileExt;
        
        // Set view file
        $view->setFile($file);
        
        // Set App title
        Registry::set('APP.title', Registry::get('APP.title') . ' :: Page not found!');
        
        $view->title = 'Error 404 :: Page not found';
        $view->message = 'The page you requested was not found!';  
        
        // Render view
        return $view->render();
    }
}
