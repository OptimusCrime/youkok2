<?php
namespace Youkok\Biz\Admin;

use Youkok\Controllers\ElementController;
use Youkok\Helpers\ElementHelper;
use Youkok\Models\Element;

class ElementUriProcessor
{
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public static function id($id)
    {
        return new ElementUriProcessor($id);
    }

    public function updateAll()
    {
        $children = ElementController::getAllChildren($this->id);
        return static::updateAllChildren($children);
    }

    private static function updateAllChildren($children)
    {
        if (count($children) === 0) {
            return true;
        }

        $newChildren = [];
        foreach ($children as $child) {
            if ($child->isLink()) {
                continue;
            }

            $uri = ElementHelper::constructUri($child->id);
            if ($uri === null or strlen($uri) === 0) {
                continue;
            }
            $child->uri = $uri;
            $child->save();

            $grandChildren = ElementController::getAllChildren($child->id);
            foreach ($grandChildren as $v) {
                $newChildren[] = $v;
            }
        }

        return static::updateAllChildren($newChildren);
    }

    public function update()
    {
        $element = Element::fromIdAll($this->id);
        if ($element === null or $element->isLink()) {
            return [
                'code' => 400
            ];
        }

        $uri = ElementHelper::constructUri($this->id);
        if ($uri === null or strlen($uri) === 0) {
            return [
                'code' => 400
            ];
        }

        $element->uri = $uri;
        $element->save();

        return [
            'code' => 200,
            'uri' => $uri
        ];
    }
}
