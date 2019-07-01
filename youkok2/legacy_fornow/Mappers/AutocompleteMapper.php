<?php
namespace Youkok\Mappers;

use Youkok\Common\Models\Element;

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

    public function getCourses($obj)
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

    private function pathFor(Element $element)
    {
        return $this->router->pathFor('archive', [
            'course' => 'dero',
            'path' => $element->fullUri
        ]);
    }
}
