<?php
namespace Youkok\Mappers;

use Youkok\Common\Utilities\NumberFormatter;

class MostPopularCourseMapper implements Mapper
{
    public static function map($obj, $data = null)
    {
        $router = $data['router'];

        return [
            'full_uri' => $router->pathFor('archive', ['course' => 'derp', 'params' => $obj->fullUri]),
            'course_code' => $obj->courseCode,
            'course_name' => $obj->courseName,
            'downloads' => NumberFormatter::format($obj->_downloads)
        ];
    }
}
