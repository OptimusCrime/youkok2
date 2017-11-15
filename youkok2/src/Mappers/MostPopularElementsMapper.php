<?php
namespace Youkok\Mappers;

use Youkok\Models\Element;

class MostPopularElementsMapper implements Mapper
{
    public static function map($obj, $data = null)
    {
        $output = [];
        foreach ($obj as $v) {
            if ($v instanceof Element) {
                $output[] = MostPopularElementMapper::map($v, $data);
            }
        }

        return [
            'code' => 200,
            'data' => $output
        ];
    }
}
