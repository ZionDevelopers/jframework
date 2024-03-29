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

namespace jFramework\Core;

use jFramework\Core\Registry;

/**
 * Session Cleaner for PHP
 * 
 * Created: 2013-07-29 11:00 AM
 * Updated: 2014-06-03 10:06 AM
 * @version 1.1.0
 
 * @package jFramework
 * @subpackage Core
 * @copyright Copyright (c) 2010-2018, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
class SessCleaner
{

    /**
     * Constructor     
     */
    public function __construct()
    {
        //TODO: Something...
    }

    /**
     * Clean up Old Session Files and Show
     *     
     * @param array $files
     */
    public function cleanupAndShow(array $files = array())
    {
        // Get Old PHP Sesssion Files
        if (empty($files)) {
            $files = $this->getOldSessFiles();
        }

        // Check if are Sess files to delete
        if (count($files) >= 1) {
            foreach ($files as $file) {
                print ("\r\nDeleting " . $file . "...");
                // Delete File
                @unlink($file);

                // Check if was Deleted
                if (!file_exists($file)) {
                    echo 'OK!';
                } else {
                    echo 'ERR!';
                }
            }
        } else {
            // All files Already Cleaned!
            echo "\r\nSession files already Cleaned!\r\n";
        }
    }

    /**
     * Search for Old PHP Session Files
     *     
     * @return array
     */
    public function getOldSessFiles()
    {
        // Mount List
        $toClean = array();
        // Scan file file on Session Folder
        $files = scandir(Registry::get('FOLDER.session'));
        // Get All Files
        foreach ($files as $file) {
            // Check if is not Scan parent Folders
            if ($file != '.' && $file != '..') {
                // Get Full File Patch
                $fullFile = Registry::get('FOLDER.session') . '/' . $file;
                // Check if is really a File
                if (is_file($fullFile)) {
                    // Check if is a Session File
                    if (strstr($file, "sess_") !== false) {
                        // Get File Last Access
                        $sessTime = fileatime($fullFile);
                        // Get a Kill Time
                        $killTime = strtotime('-1 week');
                        // Check if the file is ready to DIE
                        if ($sessTime <= $killTime) {
                            // Delete the Session File
                            $toClean [$fullFile] = $fullFile;
                        }
                    }
                }
            }
        }

        return $toClean;
    }
}
