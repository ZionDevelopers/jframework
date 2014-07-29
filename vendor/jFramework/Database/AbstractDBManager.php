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

namespace jFramework\Database;

/**
 * Class to manage MySQL database data
 * 
 * Created: 2010-07-26 07:58 PM
 * Updated: 2014-06-03 10:04 AM
 * @version 2.3.0 
 * @package jFramework
 * @subpackage Core
 * @copyright Copyright (c) 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
abstract class AbstractDBManager
{
    /**
     *
     * @var resource
     */
    protected $driverLink = null;
    /**
     *
     * @var integer     
     */
    public $nRecords = 0;
    public $currentPage = 0;
    public $tRecords = 0;
    public $nPages = 0;

    /**
     *
     * @var array
     * @access private
     */
    private $settings = array(
        'host' => 'localhost',
        'user' => 'root',
        'password' => '',
        'base' => 'test',
        'charset' => 'utf8'
    );
    
    private $cacheTable = array();

    /**
     *
     * @var string
     * @access private
     */
    private $cacheFile = '';

    /**
     *
     * @var boolean     
     */
    public $removeHtml = true;
    
    /**
     * Set Settings
     *
     * @param array $data             	
     */
    public function setSettings(array $data)
    {
        $this->settings = array_merge($this->settings, $data);
    }

    /**
     * Connect to Database Server
     */
    public function connect() { }

    /**
     * To test the connection
     *     
     * @return boolean
     */
    public function testCon() { }

    /**
     * Get Cache
     *     
     * @return array
     */
    public function cacheGet()
    {
        return $this->cacheTable;
    }

    /**
     * Refresh Session Table Cache to var inside object
     *      
     */
    public function cacheRefresh()
    {
        // Check if cache folder is not writeable
        if (!is_writable(Registry::get('FOLDER.cache'))) {
            chmod($this->cacheFile, 0755);
        }

        // Check if Cache file not exists
        if (!file_exists($this->cacheFile)) {
            // Create an empty cache file
            file_put_contents($this->cacheFile, serialize(array()));
        }

        // Set Initial Cache Table
        $this->cacheTable = unserialize(file_get_contents($this->cacheFile));
    }

    /**
     * To list all the table colums
     *     
     * @param string $table        	
     */
    public function cache() { }
    
    /**
     * Report error
     *     
     * @param string $text        	
     * @throws Exception
     */
    public function error($text)
    {
        throw new \Exception('Database Manager Exception: ' . $text . "\r\nDetails: " . $this->driver->error);
    }

    /**
     * (non-PHPdoc)
     *
     * @see mysqli::query()
     */
    public function query() { }
    
    /**
     * Fetch query result
     *     
     * @param mysqli_result $res        	
     * @return array
     */
    public function fetch() { }

    /**
     * Escape string for query
     *     
     * @param string $string        	
     * @return string
     */
    public function escape() { }

    /**
     * Find data in database
     *     
     * @param string $table        	
     * @param array $fields        	
     * @param array $where        	
     * @param array $order        	
     * @param array $limit        	
     * @param array $group        	
     * @param string $autoFetch        	
     * @return array|boolean|mysqli_result
     */
    public function find() { }

    /**
     * Insert data in database
     *     
     * @param array $data        	
     * @param string $table        	
     * @return boolean|mysqli_result
     */
    public function insert() { }

    /**
     * Insert data in database
     *     
     * @param array $data        	
     * @param string $table        	
     * @param array $where        	
     * @return boolean|mysqli_result
     */
    public function update() { }

    /**
     * To save data into database
     *     
     * @param array $data        	
     * @param string $table        	
     * @param array $where        	
     * @return boolean|mysqli_result
     */
    public function save() { }

    /**
     * To delete records from table
     *     
     * @param string $table        	
     * @param array $where        	
     * @return boolean|mysqli_result
     */
    public function delete() { }

    /**
     * To lock tables
     *     
     * @param string $table        	
     * @param string $mod        	
     * @return boolean|mysqli_result
     */
    public function lockTable() { }

    /**
     * To unlock locked tables to this connection
     *     
     * @return boolean|mysqli_result
     */
    public function unlockTables() { }

    /**
     * Return number of rows
     *     
     * @param mysqli_result $res        	
     * @return number
     */
    public function numRows() { }

    /**
     * To Normalize New Lines from Windows, Mac and Linux Plataforms to Uniform
     *     
     * @param string $string        	
     * @return string
     */
    public function normalizeNewLines($string)
    {
        // Replace all know new lines with Unix default new line
        $string = str_replace(array("\r\n", "\r", "\n"), "\n", $string);

        // Fix Duplicated Backslash of New Lines
        return str_replace(array("\\r\\n", "\\r", "\\n"), "\n", $string);
    }

    /**
     * Make all pagination process
     *      
     * @param string $table
     * @param array $where
     * @param number $nRecords
     * @param array $order
     * @return array
     */
    public function paginator( ) { }

    /**
     * To Count all records from one table
     *      
     * @param string $table
     * @param array $where
     * @return integer
     */
    public function count() { }

    /**
     * Generete Where SQL
     *     
     * @param $table string        	
     * @param $where array        	
     * @param $sql string        	
     * @param $recursive boolean        	
     */
    public function where() { }

    /**
     * Generate LIMIT SQL
     *     
     * @param $limit array        	
     * @param $sql string        	
     */
    public function limit() { }

    /**
     * Generete Order SQL
     *     
     * @param $table string        	
     * @param $order array        	
     * @param $sql string        	
     */
    public function order() { }

    /**
     * Generete Group By SQL
     *     
     * @param $table string        	
     * @param $order array        	
     * @param $sql string        	
     */
    public function group() { }

    /**
     * Show Pagination
     *     
     * @param $tipo string        	
     */
    public function showPagination() { }

    /**
     * To Realize reConnect
     *      
     */
    public function reConnect() { }

    /**
     * To get Last ID
     *      
     */
    public function getLastID() { }
}
