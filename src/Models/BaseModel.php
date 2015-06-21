<?php
/*
 * File: BaseModel.php
 * Holds: Base element for all models
 * Created: 25.05.2015
 * Project: Youkok2
*/

namespace Youkok2\Models;

/*
 * The class
 */

class BaseModel {
    
    /*
     * Set default values for the model
     */
    
    protected function setDefaults($class) {
        // Get class name
        $class_name = get_class($class);

        // Loop all the defaults
        foreach ($class->getSchema()['fields'] as $k => $v) {
            // Find what method to check
            $method_name = 'set' . ucfirst($k);
            
            if (isset($v['method'])) {
                $method_name = 'set' . ucfirst($v['method']);
            }
            
            // Check if property exists
            if (method_exists($class_name, $method_name)) {
                // Check if default value is defined
                if (isset($v['default'])) {
                    call_user_func_array(array($class, $method_name), array($v['default']));
                }
            }
        }
    }
}