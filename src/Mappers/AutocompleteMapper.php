<?php
namespace Youkok\Mappers;

use Youkok\Models\Element;

class AutocompleteMapper implements Mapper
{
    private $router;

    private function __construct($router)
    {
        $this->router = $router;
    }

    public static function map($obj, $data = null)
    {
        $autocompleteMapper = new AutocompleteMapper($data['router']);
        return $autocompleteMapper->getCourses($obj);
    }

    private function getCourses($obj)
    {
        if (count($obj) === 0) {
            return [];
        }

        $output = [];
        foreach ($obj as $v) {
            $output[] = [
                'course' => $v->courseCode . ' - ' . $v->courseName,
                'url' => $this->pathFor($v)
            ];
        }

        return $output;
    }

    private function urlFor(Element $element)
    {
        return $this->router->pathFor('archive', [
            'params' => $element->fullUri
        ]);
    }
}
