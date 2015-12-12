<?php
/*
 * File: BaseController.php
 * Holds: Interface for the controllers
 * Created: 06.11.2014
 * Project: Youkok2
 *
 */

namespace Youkok2\Models\Controllers;

use Youkok2\Utilities\CacheManager;
use Youkok2\Utilities\Database;

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
        if ($this->schema['meta']['cacheable'] and CacheManager::isCached($id, $this->cacheKey)) {
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
                if (isset($v['db']) and $v['db']) {
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

                // Check if we should cache the element
                if ($this->schema['meta']['cacheable']) {
                    // Add to cache queue
                    $this->cache();
                }
            }
        }
    }

    /*
     * Create objects by array
     */

    public function createByArray($arr) {
        // Loop the fields in the schema
        foreach ($this->schema['fields'] as $k => $v) {
            // Check if this field is a database field
            if (isset($arr[$k])) {
                // Find out what method to call
                $method = 'set' . ucfirst($k);
                if (isset($v['method'])) {
                    $method = 'set' . ucfirst($v['method']);
                }

                // Set the data
                call_user_func_array([$this->model, $method], [$arr[$k]]);
            }
        }
    }
    
    /*
     * The default toArray method
     */
    
    public function toArray() {
        // Get the initial fields from the array
        return $this->model->toArrayInitial();
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

            // Get value
            $cache_arr[$k] = call_user_func_array([$this->model, $method], []);
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
    
    /*
     * Save
     */

    public function save() {
        // Arrays for building the query
        $attributes_arr = [];
        $bindings_arr = [];
        $values_arr = [];

        foreach ($this->schema['fields'] as $k => $v) {
            if (isset($v['db']) and $v['db'] and !isset($v['ignore_insert'])) {
                // Set attribute
                $attributes_arr[] = '`'  . $k . '`';

                // Get binding
                $binding = ':'. $k;

                // Find out what method to call
                $method_prefix = 'get';
                if (isset($v['is'])) {
                    $method_prefix = 'is';
                }
                $method = $method_prefix . ucfirst($k);
                if (isset($v['method'])) {
                    $method = $method_prefix . ucfirst($v['method']);
                }

                // Get bindings and the actual value
                if (isset($v['default']) and $v['default'] === 'NOW()') {
                    // Handle edge case for NOW() inserts
                    $bindings_arr[] = 'NOW()';
                }
                else {
                    // Get value
                    $value = call_user_func_array([$this->model, $method], []);
                    
                    // Set to bindings arr
                    $bindings_arr[] = $binding;

                    // Set to values
                    $values_arr[$binding] = $value;
                }
            }
        }

        // Build query string
        $query_string  = "INSERT INTO `" . $this->schema['meta']['table'] . "` (" . implode(', ', $attributes_arr) . ")" . PHP_EOL;
        $query_string .= "VALUES (" . implode(', ', $bindings_arr) . ")";
        
        $result = Database::$db->prepare($query_string);
        $result->execute($values_arr);
        
        // Set the ID
        call_user_func_array([$this->model, 'setId'], [Database::$db->lastInsertId()]);
    }
    
    /*
     * Update
     */
    
    public function update() {
        // Arrays for building the query
        $attributes_arr = [];
        $update_arr = [];
        $values_arr = [];

        foreach ($this->schema['fields'] as $k => $v) {
            if (isset($v['db']) and $v['db'] and !isset($v['ignore_update'])) {
                // Set attribute
                $attributes_arr[] = '`'  . $k . '`';

                // Get binding
                $binding = ':'. $k;

                // Find out what method to call
                $method_prefix = 'get';
                if (isset($v['is'])) {
                    $method_prefix = 'is';
                }
                $method = $method_prefix . ucfirst($k);
                if (isset($v['method'])) {
                    $method = $method_prefix . ucfirst($v['method']);
                }

                // Get value
                $value = call_user_func_array([$this->model, $method], []);
                
                // Set to bindings arr
                $update_arr[] = '`' . $k . '` = ' . $binding;

                // Set to values
                $values_arr[$binding] = $value;
            }
        }
        
        // Add id to value
        $values_arr[':id'] = call_user_func_array([$this->model, 'getId'], []);

        // Build query string
        $query_string  = "UPDATE `" . $this->schema['meta']['table'] . "`" . PHP_EOL;
        $query_string .= "SET " . implode(', ', $update_arr) . PHP_EOL;
        $query_string .= "WHERE `id` = :id" . PHP_EOL;
        $query_string .= "LIMIT 1";
        
        $result = Database::$db->prepare($query_string);
        $result->execute($values_arr);
    }
    
    /*
     * Delete
     */
    
    public function delete() {
        $values_arr = [];
        
        // Add id to value
        $values_arr[':id'] = call_user_func_array([$this->model, 'getId'], []);
        
        // Build query string
        $query_string  = "DELETE FROM `" . $this->schema['meta']['table'] . "`" . PHP_EOL;
        $query_string .= "WHERE `id` = :id" . PHP_EOL;
        $query_string .= "LIMIT 1";
        
        $result = Database::$db->prepare($query_string);
        $result->execute($values_arr);
    }
}