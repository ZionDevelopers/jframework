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

use jFramework\MVC\View;
use jFramework\MVC\Controller\AbstractActionController;
use jFramework\Core\Registry;
use jFramework\Core\Tools;

class DBController extends AbstractActionController
{
    /**
     * Index Action
     * @param array $get
     * @param array $post
     * @param array $data
     * @return string
     */
    public function indexAction($get, $post, $data) 
    {
        $view = new View();  
        
        // Check for success or fail query        
        if (isset($get['success'])) {
            $view->setFlash('<b style="color:green">Success: </b>Test table Wiped!');
            Tools::redir($this->basepath . 'DB');
        } elseif (isset($get['fail'])) {
            $view->setFlash('<b style="color:red">Error: </b>Test table not Wiped!');
            Tools::redir($this->basepath . 'DB');    
        }

        // Set Page title
        Registry::set('APP.title', 'Database Example :: '.Registry::get('APP.title'));
        
        // Save Array to Database         
        $this->db->save(array('name' => 'Mr. #' . mt_rand(1, 99999)), 'test');
        
        // List saved test records
        $view->dbResult = $this->db->find('test');   
        
        return $view->render();
    }
    
    /**
     * Wipe Action
     * @param array $get
     * @param array $post
     * @param array $data
     */
    public function wipeAction($get, $post, $data)
    {   
        // Wipe test table and check if was successful
        if ($this->db->query('TRUNCATE TABLE test')) {
            Tools::redir($this->basepath . 'DB?success');
        } else {
            Tools::redir($this->basepath . 'DB?fail');
        }
    } 

}
