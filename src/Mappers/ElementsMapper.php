<?php
namespace Youkok\Mappers;

class ElementsMapper implements Mapper
{
    public static function map($obj)
    {
        if (count($obj) === 0) {
            return [];
        }

        $output = [];
        foreach ($obj as $v) {
            $output[] = ElementMapper::map($v);
        }

        return $output;
    }
}
