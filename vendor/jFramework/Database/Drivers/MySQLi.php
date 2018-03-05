<?php
/**
 * jFramework
 *
 * @link https://github.com/ZionDevelopers/jframework/ The jFramework GitHub Project
 * @copyright 2010-2016, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */

namespace jFramework\Database\Drivers;

use jFramework\Core\Tools;
use jFramework\Core\Registry;
use jFramework\Database\AbstractDBManager;

/**
 * Class to manage MySQL database data
 * 
 * Created: 2010-07-26 07:58 PM
 * Updated: 2014-06-03 10:04 AM
 * @version 2.3.0 
 * @package jFramework
 * @subpackage Core
 * @copyright Copyright (c) 2010-2018, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
class MySQLi extends AbstractDBManager
{    
    
    /**
     * Spawn mysqli driver
     */
    public function __construct()
    {
        $this->driverLink = new \mysqli();
    }

    /**
     * Connect to Database Server
     */
    public function connect()
    {
        // Talk back to the Driver
        $this->driverLink->connect($this->settings ['host'], $this->settings ['user'], $this->settings ['password'], $this->settings ['base']);

        // Test Connecton
        if (!$this->testCon()) {
            // Throw Connection error
            $this->error($this->driverLink->connect_error);
        }        

        // Cache File
        $this->cacheFile = Registry::get('FOLDER.cache') . '/' . $this->settings ['host'] . '@' . $this->settings ['base'] . '.dat';

        // Set Connection Charset
        $this->driverLink->set_charset($this->settings ['charset']);

        // Refresh Cache
        $this->cacheRefresh();
    }

    /**
     * To test the connection
     *     
     * @return boolean
     */
    public function testCon()
    {
        return !($this->driverLink->connect_error);
    }

    /**
     * To list all the table colums
     *     
     * @param string $table        	
     */
    public function cache($table)
    {
        // Check if has table fields cache
        if (!isset($this->cacheTable [$table])) {
            $result = $this->query("SHOW COLUMNS FROM " . $table);
            if ($result) {
                foreach ($this->fetch($result) as $column) {
                    $this->cacheTable [$table] [$column ["Field"]] = $column ["Type"];
                }
            }
        }

        file_put_contents($this->cacheFile, serialize($this->cacheTable));
    }

    /**
     * Query SQL
     * @param string $sql
     * @param boolean $autoFetch
     * @return array|mysqli_result
     */
    public function query($sql, $autoFetch = true)
    {
        $result = false;
        
        // Test Connection
        if ($this->testCon()) {
            // Execute query
            $result = $this->driverLink->query($sql);
            
            // If no result
            if (!$result) {
                $this->error('Fail commiting: ' . $sql);
            }

            // Test if need fetch query result
            if ($result && stripos($sql, 'SELECT ') !== false && $autoFetch) {
                // Fetch query result
                $result = $this->fetch($result);
            }

            // Add sql to history
            if ($this->sqlArchive) {
                $this->sqlHistory [] = date('H:i:s') . ': ' . $sql;
            }
        } else {
            $this->error('Can\'t process query, no database connection available');
        }

        return $result;
    }

    /**
     * Fetch query result
     *     
     * @param resource $res        	
     * @return array
     */
    public function fetch($res)
    {
        $result = array();
        $data = null;

        // Test Connection
        if ($this->testCon()) {
            // Check if is a resource
            if (is_object($res)) {
                // Rescue fetch data
                while ($data = $res->fetch_assoc()) {
                    $result [] = $data;
                }

                // Free Result to free some bytes on memory
                $res->free();
            } else {
                $this->error('The query resource is invalid');
            }
        } else {
            $this->error('Can\'t process fetch, no database connection available');
        }

        return (array) $result;
    }

    /**
     * Escape string for query
     *     
     * @param string $string        	
     * @return string
     */
    public function escape($string)
    {
        // Check if need remove HTML tags and remove all HTML Tags
        if ($this->removeHtml) {
            $string = strip_tags($string);
        }

        // Test Connection
        if ($this->testCon()) {
            // Escape string with real escape string
            $string = $this->driverLink->real_escape_string($string);
        }

        $string = trim($string);
        

        // Fix New Lines
        $string = $this->normalizeNewLines($string);

        // Return
        return $string;
    }

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
    public function find($table, array $fields = array(), array $where = array(), array $order = array(), array $limit = array(), array $group = array(), $autoFetch = true)
    {
        // To Cache
        $this->cache($table);

        $sql = "SELECT " . (empty($fields) ? '*' : implode(', ', $fields)) . " FROM `" . $table . "`";

        // Generete Where SQL
        $this->where($table, $where, $sql);
        
        // Generate Group SQL
        $this->group($table, $group, $sql);
        
        // Generate Order SQL
        $this->order($table, $order, $sql);
        
        // Generate Limit SQL
        $this->limit($limit, $sql);

        return $this->query($sql, $autoFetch);
    }

    /**
     * Insert data in database
     *     
     * @param array $data        	
     * @param string $table        	
     * @return boolean|mysqli_result
     */
    public function insert(array $data, $table)
    {
        // To Cache
        $this->cache($table);

        // Check if already have the created field in data, and if table has
        // this field
        if (!isset($data ['created']) && isset($this->cacheTable [$table] ['created'])) {
            $data ['created'] = date("Y-m-d H:i:s");
        }

        $sql = "INSERT INTO `" . $table . "` (";

        foreach ($data as $key => $val) {
            // Check if field exists
            if (isset($this->cacheTable [$table] [$key])) {
                $sql .= "`" . $key . "`, ";
            }
        }

        $sql = substr($sql, 0, - 2);
        $sql .= ') VALUES (';

        foreach ($data as $key => $val) {
            // Check if field exists
            if (isset($this->cacheTable [$table] [$key])) {
                if (is_numeric($val)) {
                    $sql .= $val . ", ";
                } elseif (is_null($val)) {
                    $sql .= "null, ";
                } else {
                    $sql .= "'" . $this->escape((string) $val) . "', ";
                }
            }
        }

        $sql = substr($sql, 0, - 2);

        $sql .= ");";

        return $this->query($sql);
    }

    /**
     * Insert data in database
     *     
     * @param array $data        	
     * @param string $table        	
     * @param array $where        	
     * @return boolean|mysqli_result
     */
    public function update(array $data, $table, array $where)
    {
        // To Cache
        $this->cache($table);

        // Check if already have the updated field in data, and if table has
        // this field
        if (!isset($data ['updated']) && isset($this->cacheTable [$table] ['updated'])) {
            $data ['updated'] = date("Y-m-d H:i:s");
        }

        $sql = "UPDATE `" . $table . "` SET ";

        foreach ($data as $key => $val) {
            if (isset($this->cacheTable [$table] [$key])) {
                if (is_numeric($val)) {
                    $sql .= "`" . $key . "`=" . $val . ", ";
                } else {
                    $sql .= "`" . $key . "`='" . $this->escape((string) $val) . "', ";
                }
            }
        }

        $sql = substr($sql, 0, - 2);

        // Generete Where SQL
        $this->where($table, $where, $sql);

        $sql .= ";";

        return $this->query($sql);
    }

    /**
     * To save data into database
     *     
     * @param array $data        	
     * @param string $table        	
     * @param array $where        	
     * @return boolean|mysqli_result
     */
    public function save(array $data, $table, array $where = array())
    {
        // If WHERE is empty so...
        if (empty($where)) {
            // Is To Insert
            $result = $this->insert($data, $table);
        } else {
            // If Where is not Empty is an UPDATE
            $result = $this->update($data, $table, $where);
        }

        return $result;
    }

    /**
     * To delete records from table
     *     
     * @param string $table        	
     * @param array $where        	
     * @return boolean|mysqli_result
     */
    public function delete($table, array $where = array())
    {
        // To Cache
        $this->cache($table);

        $sql = "DELETE FROM `" . $table . "`";

        // Generete Where SQL
        $this->where($table, $where, $sql);

        return $this->query($sql);
    }

    /**
     * To lock tables
     *     
     * @param string $table        	
     * @param string $mod        	
     * @return boolean|mysqli_result
     */
    public function lockTable($table, $mod = "WRITE")
    {
        return $this->query("LOCK TABLES " . $table . " " . $mod);
    }

    /**
     * To unlock locked tables to this connection
     *
     
     * @return boolean|mysqli_result
     */
    public function unlockTables()
    {
        return $this->query("UNLOCK TABLES");
    }

    /**
     * Return number of rows
     *     
     * @param resource $res        	
     * @return number
     */
    public function numRows($res)
    {
        $result = 0;

        // Check if Res is avalid Result/Object
        if (is_object($res)) {
            $result = $res->num_rows;
        }

        return $result;
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
    public function paginator($table, array $where = array(), $nRecords = 20, array $order = array())
    {
        $page = ((isset($_GET ["pg"])) ? (int) $_GET ["pg"] : null);
        $this->nRecords = $nRecords;
        $this->currentPage = ((empty($page)) ? 1 : $page);
        $this->tRecords = $this->count($table, $where);
        $this->nPages = ($this->tRecords / $this->nRecords);

        return $this->find($table, array(), $where, $order, array(($this->currentPage - 1) * $this->nRecords, $this->nRecords));
    }

    /**
     * To Count all records from one table
     *      
     * @param string $table
     * @param array $where
     * @return integer
     */
    public function count($table, array $where = array())
    {
        $data = $this->find($table, array('COUNT(*) AS N'), $where);
        return (int) $data [0] ['N'];
    }

    /**
     * Generete Where SQL
     *     
     * @param $table string        	
     * @param $where array        	
     * @param $sql string        	
     * @param $recursive boolean        	
     */
    public function where($table, array $where, &$sql, $recursive = false)
    {
        // To Cache
        $this->cache($table);

        // Array Where To SQL
        if (!empty($where)) {
            // It's calling again??
            if (!$recursive) {
                $sql .= " WHERE ";
            }

            foreach ($where as $key => $val) {
                // If This Value is a String
                if (!is_array($val)) {
                    $origK = $key;

                    // DELIMITER SEARCH
                    if (preg_match('/(>=|<=|>|<|=|LIKE)/', $key, $matches)) {
                        $delimiter = $matches [0];
                        $key = trim(str_replace($delimiter, '', $key));
                    } else {
                        $delimiter = '=';
                    }

                    unset($matches);

                    // TODO: Make a better Operator Concatenator
                    // OPERATOR SEARCH
                    if (preg_match('/(OR|AND)/', $key, $matches)) {
                        $operator = $matches [0];
                        $key = trim(str_replace($operator, '', $key));
                    } else {
                        $operator = 'AND';
                    }

                    unset($matches);

                    // FUNCTION SEARCH
                    if (preg_match('/(sha1|md5)/', $key, $matches)) {
                        $func = $matches [0];
                        if (!empty($func)) { 
                            Tools::debug($func);
                            exit();
                        }

                        $key = trim(str_replace($func, '', $key));
                    }

                    unset($matches);

                    // Math SEARCH
                    if (preg_match('/^([(])?([)])?([a-z,A-Z,>=,=,<,>,\s]{1,})([)])?$/', $key, $matches)) {
                        // If Found 4 Itens
                        if ($matches [1] == '(' && empty($matches [2]) && !isset($matches [4])) {
                            $math = $matches [1];
                        } elseif (empty($matches [1]) && $matches [2] == ')' && !isset($matches [4])) {
                            $math = $matches [2];
                        } elseif ($matches [1] == '(' && empty($matches [2]) && isset($matches [4])) {
                            if ($matches [4] == ')') {
                                $math = '()';
                            } else {
                                $math = '';
                            }
                        } else {
                            $math = implode('', $matches);
                        }

                        if ($math == '()') { // If is a complete math
                            $mathStart = '(';
                            $mathEnd = ')';
                        } elseif ($math == '(') { // If is a start math
                            $mathStart = '(';
                            $mathEnd = '';
                        } elseif ($math == ')') { // If is an end math
                            $mathStart = '';
                            $mathEnd = ') ';
                        } else { // If is not a math
                            $mathStart = '';
                            $mathEnd = '';
                        }
                        // Clean Key
                        $key = trim(preg_replace("/\(|\)/", '', $key));
                    } else {
                        $mathStart = '';
                        $mathEnd = '';
                        $math = '';
                    }

                    unset($matches);
                    if (isset($this->cacheTable [$table] [$key])) {
                        if (is_numeric($val)) {
                            $sql .= $mathStart . "`" . $key . "`" . $delimiter . $val . " " . $mathEnd . $operator . " ";
                        } elseif (is_string($val)) {
                            $sql .= $mathStart . "`" . $key . "`" . $delimiter . "'" . $this->escape($val) . "' " . $mathEnd . $operator . " ";
                        }
                    }

                    // If Value is an array so make a recursive Function
                } elseif (is_array($val)) {
                    /**
                     * **** Analyze Array: START *****
                     */
                    $arrayAnalyze = '';
                    $fieldArrayIndexed = false;

                    // Analyzing Array
                    foreach ($val as $k => $v) {
                        $arrayAnalyze .= is_numeric($k);
                    }
                    /**
                     * **** Analyze Array: END *****
                     */
                    // Checking if array is like Multiple Select Field
                    $fieldArrayIndexed = (str_repeat('1', count($val)) == $arrayAnalyze);

                    /**
                     * **** Field Array Indexed Regeneration: START *****
                     */
                    if ($fieldArrayIndexed) {
                        $list = array();
                        foreach ($val as $k => $v) {
                            /**
                             * **** MATH SEARCH: START ****
                             */
                            // Look for Math () Keys
                            if (preg_match('/(\([a-z,A-Z,>=,=,<,>,\s]{1,}\))/', $key)) {
                                // Remove math delimiters and remove empty spaces
                                $field = trim(preg_replace("/\(|\)/", '', $key));
                                // Check witch postion math should be
                                if ($val [$k] == end($val) && $k == 0) { // If This key is first and last
                                    $field = '(' . $field . ')';
                                } elseif ($k == 0 && $val [$k] != end($val)) { // If This key is first
                                    $field = '(' . $field;
                                } elseif ($val [$k] == end($val) && $k != 0) { // If this key is last
                                    $field = ')' . $field;
                                }
                            } else {
                                $field = $key;
                            }
                            /**
                             * **** MATH SEARCH: END ****
                             */
                            $list [$k] [$field] = $v;
                        }
                    } else {
                        $list = $val;
                    }

                    /**
                     * **** Field Array Indexed Regeneration: END ****
                     */
                    // Recursiving array
                    foreach ($list as $k => $v) {
                        $this->{__FUNCTION__}($table, $v, $sql, true);
                    }
                }
            }

            if (!$recursive) {
                // Remove the last AND or OR from SQL STring
                if (substr($sql, - 4) == 'AND ') {
                    $sql = substr($sql, 0, - 4);
                } elseif (substr($sql, - 3) == 'OR ') {
                    $sql = substr($sql, 0, - 3);
                }
            }
        }
    }

    /**
     * Generate LIMIT SQL
     *     
     * @param $limit array        	
     * @param $sql string        	
     */
    public function limit(array $limit, &$sql)
    {
        if (!empty($limit)) {
            $sql .= ' LIMIT ' . implode(',', $limit);
        }
    }

    /**
     * Generete Order SQL
     *     
     * @param $table string        	
     * @param $order array        	
     * @param $sql string        	
     */
    public function order($table, array $order, &$sql)
    {
        // To Cache
        $this->cache($table);

        // Check if the fields are not empty
        if (!empty($order)) {
            $sql .= " ORDER BY ";

            foreach ($order as $key => $val) {
                if (isset($this->cacheTable [$table] [$key]) || $key == 'RAND()') {
                    if ($key == 'RAND()') {
                        $sql .= $key . ", ";
                    } else {
                        $sql .= "`" . $key . "` " . $this->escape($val) . ", ";
                    }
                }
            }

            $sql = substr($sql, 0, - 2);
        }
    }

    /**
     * Generete Group By SQL
     *     
     * @param $table string        	
     * @param $order array        	
     * @param $sql string        	
     */
    public function group($table, array $group, &$sql)
    {
        // To Cache
        $this->cache($table);

        // Check if the fields are not empty
        if (!empty($group)) {
            $sql .= " GROUP BY ";

            foreach ($group as $key => $val) {
                if (isset($this->cacheTable [$table] [$val])) {
                    $sql .= "`" . $val . "`, ";
                }
            }

            $sql = substr($sql, 0, - 2);
        }
    }

    /**
     * Show Pagination
     *     
     * @param $tipo string        	
     */
    public function showPagination($type = "select", $ajaxFunc = "")
    {
        $prev = ($this->currentPage - 1);
        $next = ($this->currentPage + 1);
        $result = "";
        $url = $_SERVER ['REQUEST_URI'];

        if (empty($_SERVER ['QUERY_STRING'])) {
            if (!isset($_GET ['pg'])) {
                $url .= "?pg={PAG}";
            } else {
                $url = preg_replace("/(pg=[0-9]{1,})/i", "pg={PAG}", $url);
            }
        } else {
            if (!isset($_GET ['pg'])) {
                $url .= "&pg={PAG}";
            } else {
                $url = preg_replace("/(pg=[0-9]{1,})/i", "pg={PAG}", $url);
            }
        }

        if (($this->tRecords % $this->nRecords != 0)) {
            while ($this->tRecords % $this->nRecords != 0) {
                $this->tRecords ++;
            }
        }
        
        if ($type == "select") {
            $result = "<br><br><br><center>";
            $result .= (($this->currentPage > 1) ? "&nbsp;<a href=\"" . str_replace("{PAG}", $prev, $url) . "\">&lt;&lt; Anterior</a>&nbsp;" : "<label disabled=\"disabled\">&lt;&lt; Anterior</label>");
            $result .= "&nbsp;&nbsp;<select onchange=\"window.location.href = this.options[this.selectedIndex].value; this.disabled = true;\">";
            for ($a = 1; $a <= $this->tRecords; $a ++) {
                if ($a % $this->nRecords == 0) {
                    $link = $a;
                    $link /= $this->nRecords;
                    if ($link != $this->currentPage) {
                        $result .= "<option value=\"" . str_replace("{PAG}", $link, $url) . "\">" . $link . "</option>";
                    } else {
                        $result .= "<option value=\"" . str_replace("{PAG}", $link, $url) . "\" selected=\"selected\">" . $link . "</option>";
                    }
                }
            }
            $result .= "</select>&nbsp;&nbsp;";
            $result .= (($this->currentPage < $this->nPages) ? "&nbsp;<a href=\"" . str_replace("{PAG}", $next, $url) . "\">Próxima &gt;&gt;</a>&nbsp;" : "<label disabled=\"disabled\">Próxima &gt;&gt;</label>");
            $result .= "</center>";
        } elseif ($type == "ajax" && !empty($ajaxFunc)) {
            $result = "<center>";
            $result .= (($this->currentPage > 1) ? "&nbsp;<a href=\"#\" onclick=\"" . $ajaxFunc . "(" . $prev . ");return false;\">&lt;&lt; Anterior</a>&nbsp;" : "<label disabled=\"disabled\">&lt;&lt; Anterior</label>");
            $result .= "&nbsp;&nbsp;<select onchange=\"" . $ajaxFunc . "(this.options[this.selectedIndex].value); this.disabled = true;\">";
            for ($a = 1; $a <= $this->tRecords; $a ++) {
                if ($a % $this->nRecords == 0) {
                    $link = $a;
                    $link /= $this->nRecords;
                    if ($link != $this->currentPage) {
                        $result .= "<option value=\"" . $link . "\">" . $link . "</option>";
                    } else {
                        $result .= "<option value=\"" . $link . "\" selected=\"selected\">" . $link . "</option>";
                    }
                }
            }
            $result .= "</select>&nbsp;&nbsp;";
            $result .= (($this->currentPage < $this->nPages) ? "&nbsp;<a href=\"#\" onclick=\"" . $ajaxFunc . "(" . $next . ");return false;\">Próxima &gt;&gt;</a>&nbsp;" : "<label disabled=\"disabled\">Próxima &gt;&gt;</label>");
            $result .= "</center>";
        } elseif ($type == "text") {
            $result = "<br><br><br><center>";
            $result .= (($this->currentPage > 1) ? "&nbsp;<a href=\"" . $url . $prev . "\">&lt;&lt; Anterior</a>&nbsp;" : "<label disabled=\"disabled\">&lt;&lt; Anterior</label>");
            for ($a = 1; $a <= $this->tRecords; $a ++) {
                if ($a % $this->nRecords == 0) {
                    $link = $a;
                    $link /= $this->nRecords;
                    if ($link != $this->currentPage) {
                        $result .= "&nbsp;<a href=\"" . $url . $link . "\">" . $link . "</a>&nbsp;";
                    } else {
                        $result .= "&nbsp;<strong><big>" . $link . "</big></strong>&nbsp;";
                    }
                }
            }
            $result .= (($this->currentPage < $this->nPages) ? "&nbsp;<a href=\"" . $url . $next . "\">Próxima &gt;&gt;</a>&nbsp;" : "<label disabled=\"disabled\">Próxima &gt;&gt;</label>");
            $result .= "</center>";
        } else {
            trigger_error("Type of paginator do not exists", E_USER_WARNING);
        }

        // Show nothing if no need
        if ($this->nPages <= 1) {
            $result = '';
        }

        return $result;
    }
    
    /**
     * Close Connection
     * @return boolean
     */
    public function close()
    {
        return $this->driverLink->close();
    }

    /**
     * To Realize reConnect
     *      
     */
    public function reConnect()
    {
        $this->close();
        $this->connect();
    }

    /**
     * To get Last ID
     *      
     */
    public function getLastID()
    {
        return $this->driverLink->insert_id;
    }
}
