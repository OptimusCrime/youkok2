<?php
namespace Youkok\Mappers;

use Youkok\Models\Element;
use Youkok\Utilities\NumberFormatter;

class MostPopularElementMapper implements Mapper
{
    public static function map($obj, $data = null)
    {
        $router = $data['router'];

        return [
            'full_uri' => static::getUrlForElement($obj, $router),
            'name' => $obj->name,
            'link' => $obj->link,
            'downloads' => NumberFormatter::format($obj->_downloads),
            'parents' => MostPopularElementParentsMapper::map($obj, $data)
        ];
    }

    private static function getUrlForElement(Element $element, $router)
    {
        if ($element->link === null) {
            return $router->pathFor('download', ['params' => $element->uri]);
        }

        return $router->pathFor('redirect', ['id' => $element->id]);
    }
}
