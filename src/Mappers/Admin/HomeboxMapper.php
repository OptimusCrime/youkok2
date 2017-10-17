<?php
namespace Youkok\Mappers\Admin;

use Youkok\Mappers\Mapper;

class HomeboxMapper implements Mapper
{
    public static function map($obj, $data = null)
    {
        return [
            'code' => 200,
            'msg' => 'OK',
            'data' => $obj
        ];
    }
}