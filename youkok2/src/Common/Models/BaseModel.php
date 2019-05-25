<?php
namespace Youkok\Common\Models;

use Illuminate\Database\Eloquent\Model;
use Youkok\Biz\Exceptions\ColumnsDoesNotExistException;

class BaseModel extends Model
{
    public function __get($key)
    {
        if ($key === 'columns') {
            return parent::__get($key);
        }

        $value = parent::__get($key);

        if ($value === null && $this->columns !== null && !in_array($key, $this->columns)) {
            throw new ColumnsDoesNotExistException('Tried to fetch column ' + $key + ' which does not exist.');
        }

        return $value;
    }
}
