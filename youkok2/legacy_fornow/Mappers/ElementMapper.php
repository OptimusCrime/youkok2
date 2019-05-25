<?php
namespace Youkok\Mappers;

class ElementMapper implements Mapper
{
    public static function map($obj, $data = null)
    {
        return $obj->toArray();
    }
}
