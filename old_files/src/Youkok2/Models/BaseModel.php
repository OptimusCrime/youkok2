<?php
namespace Youkok2\Models;

class BaseModel
{
    protected $controller;
    protected $schema;
    
    protected function setDefaults() {
        foreach ($this->getSchema()['fields'] as $k => $v) {
            if (!isset($v['db']) or (isset($v['db']) and $v['db'])) {
                $method_name = 'set' . ucfirst($k);
                
                if (isset($v['method'])) {
                    $method_name = 'set' . ucfirst($v['method']);
                }
                
                if (method_exists($this, $method_name)) {
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

    public function toArrayInitial() {
        $arr = [];

        foreach ($this->getSchema()['fields'] as $k => $v) {
            if (isset($v['arr']) and $v['arr']) {
                $method_name = 'get' . ucfirst($k);

                if (isset($v['method'])) {
                    $method_name = 'get' . ucfirst($v['method']);
                }

                $arr[$k] = call_user_func_array([
                    $this, $method_name
                ], []);
            }
        }

        return $arr;
    }

    public function getSchema() {
        return $this->schema;
    }

    public function __call($name, $arguments) {
        if (method_exists($this->controller, $name)) {
            return call_user_func_array([$this->controller,
                $name], $arguments);
        }
    }
}
