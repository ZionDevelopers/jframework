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

use jFramework\Core\SessCleaner;
use jFramework\MVC\Controller\AbstractActionController;

class SessCleanerController extends AbstractActionController
{
    public function indexAction(){ 
        echo "\r\nStarting PHP Session Cleaner for jFramework...\r\n";

        // Starts Session Cleaner
        $sc = new SessCleaner ();
        // Get Old Session Files
        $files = $sc->getOldSessFiles();
        // Clean and Show
        $sc->cleanupAndShow($files);
    }
}
