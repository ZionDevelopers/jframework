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

use jFramework\MVC\View;

/**
 * Base Action Controller who will Controllers will extend to
 * 
 * Created: 2014-06-08 08:06 PM (GMT -03:00)
 * Updated: 2014-06-08 08:06 PM (GMT -03:00)
 * @version 0.0.1 
 * @package jFramework
 * @subpackage MVC
 * @copyright Copyright (c) 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
abstract class AbstractActionController extends AbstractController
{
    public function indexAction ()
    {
        
    }
    
    /**
     * Render Layout
     * @param string $viewContents
     * @return string
     */
    public function layout($viewContents)
    {
        $view = new View();
        $view->setLayout('default');
        $view->title = 'Welcome to jFramework!';
        
        return $view->renderLayout($viewContents);
    }
    
    /**
     * Page not found Action
     * @return string
     */
    public function notFoundAction()
    {
        return '<h1>Page Not found</h1>';
    }
}
