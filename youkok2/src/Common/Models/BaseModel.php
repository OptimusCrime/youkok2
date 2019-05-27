<?php
namespace Youkok\Common\Models;

use Illuminate\Database\Eloquent\Model;
use Youkok\Biz\Exceptions\ColumnsDoesNotExistException;

class BaseModel extends Model
{
    public function __get($key)
    {
        if ($key === 'attributes') {
            return parent::__get($key);
        }

        $value = parent::__get($key);

        if ($value === null && $this->attributes !== null && !array_key_exists($key, $this->attributes)) {
            throw new ColumnsDoesNotExistException('Tried to fetch attribute "' . $key . '" which does not exist.');
        }

        return $value;
    }
}
