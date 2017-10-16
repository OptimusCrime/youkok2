<?php
namespace Youkok\Mappers;

class MostPopularElementsMapper implements Mapper
{
    public static function map($obj, $data = null)
    {
        $output = [];
        foreach ($obj as $v) {
            $output[] = MostPopularElementMapper::map($v, $data);
        }

        return [
            'code' => 200,
            'data' => $output
        ];
    }
}