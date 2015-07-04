<?php
/*
 * File: BaseController.php
 * Holds: Interface for the controllers
 * Created: 06.11.2014
 * Project: Youkok2
*/

namespace Youkok2\Models\Controllers;

/*
 * Define what classes to use
 */

use \Youkok2\Utilities\CacheManager as CacheManager;
use \Youkok2\Utilities\Database as Database;

/*
 * The abstract class
 */

abstract class BaseController {

    /*
     * Variables
     */

    protected $model;
    private $class;
    private $cacheKey;

    // Construct
    public function __construct($class, $model) {
        // Set reference to class
        $this->class = $class;

        // Set reference to model
        $this->model = $model;

        // Get schema
        $this->schema = $model->getSchema();

        // Get cachekey
        $this->cacheKey = get_class_vars(get_class($this->class))['cacheKey'];
    }

    /*
     * Creating objects by id
     */

    public function createById($id) {
        // Check if already cached
        if (CacheManager::isCached($id, $this->cacheKey)) {
            // Get cache data
            $cache_data = CacheManager::getCache($id, $this->cacheKey);

            // Loop the schema
            foreach ($this->schema['fields'] as $k => $v) {
                // Check if fields exists in the cache
                if (isset($cache_data[$k])) {
                    // Fields exists, check what method to call
                    $method = 'set' . ucfirst($k);
                    if (isset($v['method'])) {
                        $method = 'set' . ucfirst($v['method']);
                    }

                    // Set the data
                    call_user_func_array([$this->model, $method], [$cache_data[$k]]);
                }
            }
        }
        else {
            // Not cached, find out what to fetch
            $query_arr = [];
            foreach ($this->schema['fields'] as $k => $v) {
                if (isset($v['db'])) {
                    $query_arr[] = '`' . $this->schema['meta']['table'] . '`.`'  . $k . '`';
                }
            }

            // Build query string
            $query_string  = "SELECT " . implode(', ', $query_arr) . PHP_EOL;
            $query_string .= "FROM `" . $this->schema['meta']['table'] . '`' . PHP_EOL;
            $query_string .= "WHERE `id` = :id" . PHP_EOL;

            // Get db record
            $result = Database::$db->prepare($query_string);
            $result->execute([
                ':id' => $id
            ]);
            $row = $result->fetch(\PDO::FETCH_ASSOC);

            // Check if anything was returned
            if (isset($row['id'])) {
                // Loop the fields in the schema
                foreach ($this->schema['fields'] as $k => $v) {
                    // Check if this field is a database field
                    if (isset($v['db']) and isset($row[$k])) {
                        // Find out what method to call
                        $method = 'set' . ucfirst($k);
                        if (isset($v['method'])) {
                            $method = 'set' . ucfirst($v['method']);
                        }

                        // Set the data
                        call_user_func_array([$this->model, $method], [$row[$k]]);
                    }
                }

                // Cache element
                $this->cache();
            }
        }
    }

    /*
     * Create objects by array
     */

    public function createByArray($arr) {
        // TODO
    }
    
    /*
     * Set cache
     */

    public function cache() {
        $cache_arr = [];

        // Loop all the fields in the schema
        foreach ($this->schema['fields'] as $k => $v) {
            // Find out what method to call
            $method_prefix = 'get';
            if (isset($v['is'])) {
                $method_prefix = 'is';
            }

            $method = $method_prefix . ucfirst($k);
            if (isset($v['method'])) {
                $method = $method_prefix . ucfirst($v['method']);
            }

            $cache_arr[$k] = call_user_func_array(array($this->model, $method), []);
        }

        // Set cache here
        CacheManager::setCache($this->model->getId(), $this->cacheKey, $cache_arr);
    }

    /*
     * Delete cache
     */

    public function deleteCache() {
        CacheManager::deleteCache($this->model->getId(), $this->cacheKey);
    }
    
    // Method for saving
    public function save() {

    }
    
    // Method for updating
    public function update() {

    }
}