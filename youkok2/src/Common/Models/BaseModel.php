<?php
namespace Youkok\Common\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected $dataStore;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->dataStore = [];
    }

    public function __set($name, $value)
    {
        if (substr($name, 0, 1) === '_') {
            $this->dataStore[substr($name, 1)] = $value;
        } else {
            parent::__set($name, $value);
        }
    }

    public function getDataStore()
    {
        return $this->dataStore;
    }

    private function hasDataStoreKey($name)
    {
        if (substr($name, 0, 1) !== '_') {
            return false;
        }

        $dataStoreKey = substr($name, 1);
        return isset($this->dataStore[$dataStoreKey]);
    }

    private function getDataStoreKey($name)
    {
        if (!$this->hasDataStoreKey($name)) {
            return null;
        }

        $dataStoreKey = substr($name, 1);
        if (!isset($this->dataStore[$dataStoreKey])) {
            return null;
        }

        return $this->dataStore[$dataStoreKey];
    }

    public function __isset($name)
    {
        return $this->hasDataStoreKey($name);
    }

    public function __get($key)
    {
        $value = parent::__get($key);
        if ($value !== null) {
            return $value;
        }

        if ($key === 'dataStore') {
            return $this->getDataStore();
        }

        if (!$this->hasDataStoreKey($key)) {
            return null;
        }

        return $this->getDataStoreKey($key);
    }
}
