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

namespace App\Controller;

use jFramework\MVC\Controller\AbstractActionController;
use jFramework\MVC\View;
use jFramework\Image\Captcha;

class CaptchaController extends AbstractActionController
{
    public function indexAction($get, $post, $data)
    {       
        $view = new View();
        $message = '';
        
        if(!empty($get['captcha'])){
            $pass = Captcha::verify($get['captcha'], 'captcha');
            
            if($pass){
                $message = '<span style="color: green"><b>Success:</b> You have confirmed to not be a robot!</span><br /><br />';
            }else{
                $message = '<span style="color: red"><b>Error:</b> You failed!</b><br /><br />';
            }
        }
        
        $view->message = $message;
        $view->captcha = Captcha::generateHTML('captcha');
        return $view->render();
    }
    
    public function generateAction($get)
    {
        $from = isset($get['from']) ? $get['from'] : 'default';
        
        $captcha = new Captcha();
        $captcha->generate($from);
        
        return '';
    }
}
