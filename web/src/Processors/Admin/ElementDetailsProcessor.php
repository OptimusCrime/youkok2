<?php
namespace Youkok\Processors\Admin;

use Youkok\Helpers\ElementHelper;
use Youkok\Models\Download;
use Youkok\Models\Element;
use Youkok\Models\Session;
use Youkok\Utilities\NumberFormatter;

class ElementDetailsProcessor
{
    private $id;
    private $settings;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function withSettings($settings)
    {
        $this->settings = $settings;
        return $this;
    }

    public static function fetch($id)
    {
        return new ElementDetailsProcessor($id);
    }

    public function run()
    {
        if ($this->id === null) {
            return [
                'code' => 400
            ];
        }

        $element = Element::fromIdAll($this->id, [
            'id', 'name', 'slug', 'uri', 'parent', 'empty' , 'checksum', 'size', 'directory',
            'pending', 'deleted', 'link' , 'added', 'last_visited'
        ]);
        if ($element === null) {
            return [
                'code' => 400
            ];
        }

        return [
            'code' => 200,
            'id' => $element->id,
            'name' => $element->name,
            'slug' => $element->slug,
            'uri' => $element->uri,
            'parent' => $element->parent,
            'empty' => $element->empty,
            'checksum' => $element->checksum,
            'size' => $element->size,
            'directory' => $element->directory,
            'pending' => $element->pending,
            'deleted' => $element->deleted,
            'link' => $element->link,
            'added' => $element->added,
            'last_visited' => $element->last_visited,
            'parents' => static::getChildrenForElement($element, $element->rootParentAll),
            'checksum_verified' => static::verifyChecksum($element, $this->settings)
        ];
    }

    private static function verifyChecksum(Element $element, $settings)
    {
        if ($element->link !== null) {
            return null;
        }
        if ($element->directory === 1) {
            return null;
        }

        return ElementHelper::fileExists($element, $settings['file_directory']);
    }

    private static function getChildrenForElement(Element $original, Element $element, $depth = 0)
    {
        $children = [];
        if ($depth === 0) {
            $children[] = [
                'value' => $element->id,
                'text' => $element->courseCode . ' - ' . $element->courseName,
                'disabled' => false
            ];
        }

        $elementEhildren = Element
            ::select('id', 'name')
            ->where('parent', $element->id)
            ->where('directory', 1)
            ->orderBy('deleted', 'ASC')
            ->orderBy('name', 'ASC')
            ->get();

        if ($elementEhildren === null or count($elementEhildren) === 0) {
            return $children;
        }

        foreach ($elementEhildren as $elementChild) {
            $children[] = [
                'value' => $elementChild->id,
                'text' => str_repeat('-', $depth + 1)
                    . ' '
                    . $elementChild->name
                    . ($original->id === $elementChild->id ? ' (nåværende objekt)' : ''),
                'disabled' => $original->id === $elementChild->id
            ];

            $children = array_merge($children, static::getChildrenForElement($original, $elementChild, $depth + 1));
        }

        return $children;
    }

}
