<?php
/**
 * jFramework
 *
 * @link https://github.com/ZionDevelopers/jframework/ The jFramework GitHub Project
 * @copyright 2010-2023, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */

namespace App\Controller;

use jFramework\MVC\Controller\AbstractActionController;
use jFramework\MVC\View;
use jFramework\Core\Registry;
use jFramework\Core\Tools;

class DebugController extends AbstractActionController
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
        $view = new View();
        
        // Set Page title
        Registry::set('APP.title', 'Home :: ' . Registry::get('APP.title'));
        
        // Set cache
        $view->cache = $this->db->cacheGet();   
        $view->get = $get;
        $view->post = $post;
        $view->data = $data;
        $view->request = Registry::get('Request');        
        
        return $view->render();
    } 
    
    /**
     * PHPInfo() Action
     * @param array $get
     * @param array $post
     * @param array $data
     */
    public function piAction($get, $post, $data)
    {
        // Define DynDNS
        $dynDNS = Registry::get('APP.dyndns');        
        // Define Visitor IP
        $visitorIp = $this->core->server('REMOTE_ADDR');
        // Define host IP
        $hostIp = Tools::gethostbynamev6($dynDNS);
        
        // Check if REMOTE api is dyndns
        if ($hostIp === $visitorIp) {
            // Run PHPINFO
            phpinfo();
        } else {
            // Dump unauthorized warning
            echo "You're not allowed to see this phpinfo(). <br />" ,
                 "Authorization by IP strictly to host <b>", $dynDNS, '</b><br />',
                 "Your ip <b>", $visitorIp, "</b>,  Expected ip <b>", $hostIp, '</b>';
        }
    }
}
