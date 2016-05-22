<?php
/*
 * File: BaseModel.php
 * Holds: Base class for all models
 * Created: 25.05.2015
 * Project: Youkok2
 *
 */

namespace Youkok2\Models;

class BaseModel 
{
    
    /*
     * Set default values for the model
     */
    
    protected function setDefaults() {
        // Loop all the defaults
        foreach ($this->getSchema()['fields'] as $k => $v) {
            // Only run for fields in the database
            if (!isset($v['db']) or (isset($v['db']) and $v['db'])) {
                // Find what method to check
                $method_name = 'set' . ucfirst($k);
                
                if (isset($v['method'])) {
                    $method_name = 'set' . ucfirst($v['method']);
                }
                
                // Check if property exists
                if (method_exists($this, $method_name)) {
                    // Check if default value is defined
                    if (isset($v['default'])) {
                        call_user_func_array([
                            $this, $method_name
                        ], [
                            $v['default']
                        ]);
                    }
                }
            }
        }
    }

    /*
     * toArray initial fields
     */

    public function toArrayInitial() {
        // Array for storing the default fields from the schema
        $arr = [];

        // Loop all the defaults
        foreach ($this->getSchema()['fields'] as $k => $v) {
            if (isset($v['arr']) and $v['arr']) {
                // Find what method to check
                $method_name = 'get' . ucfirst($k);

                if (isset($v['method'])) {
                    $method_name = 'get' . ucfirst($v['method']);
                }

                // Add value to array
                $arr[$k] = call_user_func_array([
                    $this, $method_name
                ], []);
            }
        }

        // Return the array
        return $arr;
    }

    /*
     * Return schema
     */

    public function getSchema() {
        return $this->schema;
    }

    /*
     * Functions overload
     */

    public function __call($name, $arguments) {
        // Check if method exists
        if (method_exists($this->controller, $name)) {
            // Call method and return response
            return call_user_func_array([$this->controller,
                $name], $arguments);
        }
    }
}
