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

namespace App\Controller;

use jFramework\MVC\Controller\AbstractActionController;
use jFramework\MVC\View;
use jFramework\Image\Captcha;
use jFramework\Core\Registry;

class CaptchaController extends AbstractActionController
{
    public function indexAction($get, $post, $data)
    {       
        $view = new View();
        
        // Set Page title
        Registry::set('APP.title', 'Captcha Example :: '.Registry::get('APP.title'));
        
        $message = '';
        
        if (!empty($get['captcha'])) {
            $pass = Captcha::verify($get['captcha'], 'captcha');
            
            if ($pass) {
                $message = '<span style="color: green"><b>Success:</b> You have confirmed to not be a robot!</span><br /><br />';
            } else {
                $message = '<span style="color: red"><b>Error:</b> You failed!</b><br /><br />';
            }
        }
        
        $view->setFlash($message);
        
        $view->message = '';
        $view->captcha = Captcha::generateHTML('captcha');
        return $view->render();
    }
    
    public function generateAction($get, $post, $data)
    {
        $from = isset($get['from']) ? $get['from'] : 'default';
        
        $captcha = new Captcha();
        $captcha->generate($from);
        
        return '';
    }
}
