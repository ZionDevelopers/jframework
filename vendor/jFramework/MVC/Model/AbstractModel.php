<?php
/**
 * jFramework
 *
 * @link https://github.com/ZionDevelopers/jframework/ The jFramework GitHub Project
 * @copyright 2010-2016, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */

namespace jFramework\MVC\Model;

/**
 * To manage models
 *
 * Created: 2018-03-04 09:10 PM (GMT -03:00)
 * Created: 2018-03-04 09:10 PM (GMT -03:00)
 * @version 2.2.0
 * @package jFramework
 * @subpackage MVC
 * @copyright Copyright (c) 2010-2018, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
abstract class AbstractModel
{
    protected $table = 'none';
    private $where = [];
    private $data = [];
    private $db = null;

    /**
     * Initialize
     * @global \mysqli $dbUniqueLink;
     */
    public function __construct()
    {
        global $dbUniqueLink;
        $this->db =& $dbUniqueLink;
    }

    /**
     * Set data
     * @param array $post
    */
    public function set(array $post)
    {
        $this->data = array_merge($post, $this->data);
    }

    /**
     * Set where
     * @param array $where
     */
    public function where(array $where = [])
    {
        $this->where = array_merge($where, $this->where);
    }

		/**
		 * Save data in database
		 * @return array|boolean|\mysqli_result
		 */
    public function save()
    {
        // Check if where was set
        if (empty($this->where)) {
            // Insert data array into table
            return $this->db->insert($this->data, $this->table);
        } else {
					return $this->db->update($this->data, $this->table, $this->where);
				}
    }

    /**
		 * Get data from table
		 * @param array $select
		 * @param array $where
		 * @param array $order
		 * @param array $limit
		 * @param array $group
		 * @param boolean $authFetch
		 * @return array|boolean|\mysqli_result 
		 */
		public function get(array $select = [], array $where = [], array $order = [], array $limit = [], array $group = [], $autoFetch = true)
		{
        return $this->db->find($this->table, $select, $where, $order, $limit, $group, $autoFetch);
		}
}
