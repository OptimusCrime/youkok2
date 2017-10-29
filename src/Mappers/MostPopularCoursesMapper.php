<?php
namespace Youkok\Mappers;

class MostPopularCoursesMapper implements Mapper
{
    public static function map($obj, $data = null)
    {
        $output = [];
        foreach ($obj as $v) {
            $output[] = MostPopularCourseMapper::map($v, $data);
        }

        return [
            'code' => 200,
            'data' => $output
        ];
    }
}
