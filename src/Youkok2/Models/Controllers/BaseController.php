<?php
namespace Youkok2\Models\Controllers;

use Youkok2\Utilities\CacheManager;
use Youkok2\Utilities\Database;

class BaseController
{

    protected $model;
    private $class;
    private $cacheKey;
    private $errors;

    public function __construct($class, $model) {
        if ($class !== null) {
            $this->class = $class;

            $this->cacheKey = get_class_vars(get_class($this->class))['cacheKey'];
        }

        $this->model = $model;
        $this->schema = $model->getSchema();
        $this->errors = [];
    }

    public function createById($id) {
        if ($this->cacheKey !== null and $this->schema['meta']['cacheable'] and
            CacheManager::isCached($id, $this->cacheKey)) {
            $cache_data = CacheManager::getCache($id, $this->cacheKey);

            foreach ($this->schema['fields'] as $k => $v) {
                if (isset($cache_data[$k])) {
                    $method = 'set' . ucfirst($k);
                    if (isset($v['method'])) {
                        $method = 'set' . ucfirst($v['method']);
                    }

                    call_user_func_array([$this->model, $method], [$cache_data[$k]]);
                }
            }
        }
        else {
            if (!isset($this->schema['meta']['queryable']) or (isset($this->schema['meta']['queryable']) and
                    $this->schema['meta']['queryable'])) {
                $query_arr = [];
                foreach ($this->schema['fields'] as $k => $v) {
                    if (isset($v['db']) and $v['db']) {
                        $query_arr[] = '`' . $this->schema['meta']['table'] . '`.`' . $k . '`';
                    }
                }

                $query_string = "SELECT " . implode(', ', $query_arr) . PHP_EOL;
                $query_string .= "FROM `" . $this->schema['meta']['table'] . '`' . PHP_EOL;
                $query_string .= "WHERE `id` = :id" . PHP_EOL;

                $result = Database::$db->prepare($query_string);
                $result->execute([
                    ':id' => $id
                ]);
                $row = $result->fetch(\PDO::FETCH_ASSOC);

                if (isset($row['id'])) {
                    foreach ($this->schema['fields'] as $k => $v) {
                        if (isset($v['db']) and isset($row[$k])) {
                            $method = 'set' . ucfirst($k);
                            if (isset($v['method'])) {
                                $method = 'set' . ucfirst($v['method']);
                            }

                            call_user_func_array([$this->model, $method], [$row[$k]]);
                        }
                    }

                    if ($this->cacheKey !== null and $this->schema['meta']['cacheable']) {
                        $this->cache();
                    }
                }
            }
        }
    }

    public function createByArray($arr) {
        foreach ($this->schema['fields'] as $k => $v) {
            if (isset($arr[$k])) {
                $method = 'set' . ucfirst($k);
                if (isset($v['method'])) {
                    $method = 'set' . ucfirst($v['method']);
                }

                call_user_func_array([$this->model, $method], [$arr[$k]]);
            }
        }
    }
    
    public function toArray() {
        return $this->model->toArrayInitial();
    }

    public function cache($force = false) {
        $cache_arr = [];

        foreach ($this->schema['fields'] as $k => $v) {
            if (!isset($v['cache']) or (isset($v['cache']) and $v['cache'])) {
                $method_prefix = 'get';
                if (isset($v['is'])) {
                    $method_prefix = 'is';
                }
                $method = $method_prefix . ucfirst($k);
                if (isset($v['method'])) {
                    $method = $method_prefix . ucfirst($v['method']);
                }

                $cache_arr[$k] = call_user_func_array([$this->model, $method], []);
            }
        }

        CacheManager::setCache($this->model->getId(), $this->cacheKey, $cache_arr, $force);
    }

    public function deleteCache() {
        CacheManager::deleteCache($this->model->getId(), $this->cacheKey);
    }

    public function save() {
        $attributes_arr = [];
        $bindings_arr = [];
        $values_arr = [];

        foreach ($this->schema['fields'] as $k => $v) {
            if (isset($v['db']) and $v['db'] and !isset($v['ignore_insert'])) {
                $attributes_arr[] = '`'  . $k . '`';

                $binding = ':'. $k;

                $method_prefix = 'get';
                if (isset($v['is'])) {
                    $method_prefix = 'is';
                }
                $method = $method_prefix . ucfirst($k);
                if (isset($v['method'])) {
                    $method = $method_prefix . ucfirst($v['method']);
                }

                if (isset($v['default']) and $v['default'] === 'CURRENT_TIMESTAMP') {
                    $value = call_user_func_array([$this->model, $method], []);
                    if ($value === null or strlen($value) == 0) {
                        $bindings_arr[] = 'CURRENT_TIMESTAMP';
                    }
                    else {
                        $bindings_arr[] = $binding;
                        $values_arr[$binding] = $value;
                    }
                }
                else {
                    $value = call_user_func_array([$this->model, $method], []);
                    
                    $bindings_arr[] = $binding;

                    if ($v['type'] === 'integer' and is_bool($value)) {
                        if ($value) {
                            $value = 1;
                        }
                        else {
                            $value = 0;
                        }
                    }

                    $values_arr[$binding] = $value;
                }
            }
        }

        try {
            $query_string  = "INSERT INTO `" . $this->schema['meta']['table'];
            $query_string .= "` (" . implode(', ', $attributes_arr) . ")" . PHP_EOL;
            $query_string .= "VALUES (" . implode(', ', $bindings_arr) . ")";

            $result = Database::$db->prepare($query_string);
            $result->execute($values_arr);

            call_user_func_array([$this->model, 'setId'], [Database::$db->lastInsertId()]);

            return true;
        }
        catch (\PDOException $e) {
            $this->errors[] = $e->getMessage();

            return false;
        }

    }

    public function update() {
        $attributes_arr = [];
        $update_arr = [];
        $values_arr = [];

        foreach ($this->schema['fields'] as $k => $v) {
            if (isset($v['db']) and $v['db'] and !isset($v['ignore_update'])) {
                $attributes_arr[] = '`'  . $k . '`';
                
                $binding = ':'. $k;

                $method_prefix = 'get';
                if (isset($v['is'])) {
                    $method_prefix = 'is';
                }
                $method = $method_prefix . ucfirst($k);
                if (isset($v['method'])) {
                    $method = $method_prefix . ucfirst($v['method']);
                }
 
                $value = call_user_func_array([$this->model, $method], []);
                
                $update_arr[] = '`' . $k . '` = ' . $binding;

                $values_arr[$binding] = $value;
            }
        }
        
        $values_arr[':id'] = call_user_func_array([$this->model, 'getId'], []);

        $query_string  = "UPDATE `" . $this->schema['meta']['table'] . "`" . PHP_EOL;
        $query_string .= "SET " . implode(', ', $update_arr) . PHP_EOL;
        $query_string .= "WHERE `id` = :id";
        
        $result = Database::$db->prepare($query_string);
        $result->execute($values_arr);
    }
    
    public function delete() {
        $values_arr = [];
        
        $values_arr[':id'] = call_user_func_array([$this->model, 'getId'], []);
        
        $query_string  = "DELETE FROM `" . $this->schema['meta']['table'] . "`" . PHP_EOL;
        $query_string .= "WHERE `id` = :id" . PHP_EOL;
        $query_string .= "LIMIT 1";
        
        $result = Database::$db->prepare($query_string);
        $result->execute($values_arr);
    }

    public function getLastError() {
        if (count($this->errors) == 0) {
            return null;
        }
        return $this->errors[count($this->errors) - 1];
    }

    public function getErrors() {
        return $this->errors;
    }
}
