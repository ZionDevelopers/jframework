<?php
/**
 * Session Cleaner for PHP
 * @!created 2013-07-29 11:00 AM
 * @!updated 2013-07-29 11:06 AM
 * @author Júlio César <talk@juliocesar.me>
 * @version 1.0
 * @package jFramework
 * @copyright Copyright (c) 2013, Júlio César
 */
class jSessCleaner {
	/**
	 * __construct
	 */
	public function __construct() {		
	}
	
	/**
	 * Clean up Old Session Files and Show
	 * @param array $files
	 * @access public
	 */
	public function cleanupAndShow(array $files = array()){
		// Get Old PHP Sesssion Files
		if(empty($files)){
			$files = $this->getOldSessFiles();
		}	
			
		// Check if are Sess files to delete
		if(count($files) >= 1){
			foreach($files as $file){
				print("\r\nDeleting ".$file."...");
				// Delete File
				unlink($file);
				
				// Check if was Deleted
				if(!file_exists($file)){
					echo 'Deleted!';
				}else{
					echo 'Not Deleted!';
				}
			}
		}else{
			// All files Already Cleaned!
			echo "\r\n Session files already Cleaned! \r\n";		
		}
	}
	
	/**
	 * Search for Old PHP Session Files
	 * @return array
	 * @access public
	 */
	public function getOldSessFiles() {
		// Mount List
		$toClean = array();
		// Scan file file on Session Folder
		$files = scandir ( SESSION_DIR );
		// Get All Files
		foreach($files as $file){			
			// Check if is not Scan parent Folders
			if ($file != '.' && $file != '..') {
				// Get Full File Patch	
				$fullFile = SESSION_DIR . '/' . $file ;	
				// Check if is really a File		
				if (is_file ( $fullFile)) {	
					// Check if is a Session File				
					if(strstr($file, "sess_") !== false){	
						// Get File Last Access					
						$sessTime = fileatime($fullFile);
						// Get a Kill Time
						$killTime = strtotime('-1 week');
						// Check if the file is ready to DIE
						if($sessTime <= $killTime){
							// Delete the Session File
							$toClean[$fullFile] = $fullFile;											
						}					
					}
				}
			}
		}
		
		return $toClean;
	}	
}
?>