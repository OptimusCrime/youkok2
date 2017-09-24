<?php
namespace Youkok\Mappers;

class ElementMapper implements Mapper
{
    public static function map($obj)
    {
        return $obj->toArray();
    }
}
