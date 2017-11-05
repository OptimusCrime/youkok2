<?php
namespace Youkok\Mappers;

class MostPopularElementParentsMapper implements Mapper
{
    public static function map($obj, $data = null)
    {
        $router = $data['router'];

        if (in_array(count($obj->parents), [0, 1])) {
            return [];
        }

        if (count($obj->parents) === 2) {
            return [
                static::mapCourseParent($obj->parents[0], $router)
            ];
        }

        return [
            static::mapDirectoryParent($obj->parents[1], $router),
            static::mapCourseParent($obj->parents[0], $router)
        ];
    }

    private static function mapCourseParent($obj, $router)
    {
        return [
            'full_uri' => $router->pathFor('archive', ['params' => $obj->fullUri]),
            'course_code' => $obj->courseCode,
            'course_name' => $obj->courseName,
        ];
    }

    private static function mapDirectoryParent($obj, $router)
    {
        return [
            'full_uri' => $router->pathFor('archive', ['params' => $obj->fullUri]),
            'name' => $obj->name
        ];
    }
}
