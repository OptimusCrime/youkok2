<?php
namespace Youkok\Biz\Admin;

use Youkok\Controllers\ElementController;
use Youkok\Helpers\ElementHelper;
use Youkok\Models\Download;
use Youkok\Models\Element;
use Youkok\Models\Session;
use Youkok\Utilities\NumberFormatter;

class ElementDetailsProcessor
{
    private $id;
    private $settings;
    private $params;
    private $router;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function withSettings($settings)
    {
        $this->settings = $settings;
        return $this;
    }

    public function withParams($params)
    {
        $this->params = $params;
        return $this;
    }

    public function withRouter($router)
    {
        $this->router = $router;
        return $this;
    }

    public static function id($id)
    {
        return new ElementDetailsProcessor($id);
    }

    private static function getElementFromId($id)
    {
        if ($id === null) {
            return null;
        }

        return Element::fromIdAll($id, [
            'id', 'name', 'slug', 'uri', 'parent', 'empty' , 'checksum', 'size', 'directory',
            'pending', 'deleted', 'link' , 'added', 'last_visited'
        ]);
    }

    public function fetch()
    {
        $element = static::getElementFromId($this->id);
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

    public function update()
    {
        $element = static::getElementFromId($this->id);
        if ($element === null) {
            return [
                'code' => 400
            ];
        }

        if (!static::updateElement($element, $this->params)) {
            return [
                'code' => 400
            ];
        }

        return [
            'code' => 200,
            'course' => $element->rootParentAll->id,
            'action' => $this->router->pathFor(
                'admin_processor_element_list_markup_fetch', [
                    'id' => $element->rootParentAll->id
                ]
            ),
            'action_pending' => $this->router->pathFor(
                'admin_processor_element_list_pending_markup_fetch', [
                    'id' => $element->rootParentAll->id
                ]
            ),
        ];
    }

    public static function updateElement(Element $element, $params)
    {
        // Name
        if (isset($params['element-name']) and strlen($params['element-name']) > 0) {
            $element->name = $params['element-name'];
        }

        // Slug
        if (isset($params['element-slug']) and strlen($params['element-slug']) > 0) {
            $element->slug = $params['element-slug'];
        }
        else {
            $element->slug =  null;
        }

        // URI
        if (isset($params['element-uri']) and strlen($params['element-uri']) > 0) {
            $element->uri = $params['element-uri'];
        }
        else {
            $element->uri =  null;
        }

        // Parent
        $oldParentId = $element->parent;
        if (isset($params['element-parent']) and strlen($params['element-parent']) > 0) {
            $element->parent = (int) $params['element-parent'];
        }
        else {
            $element->parent =  null;
        }

        // Checksum
        if (isset($params['element-checksum']) and strlen($params['element-checksum']) > 0) {
            $element->checksum = $params['element-checksum'];
        }
        else {
            $element->checksum =  null;
        }

        // Checksum
        if (isset($params['element-size']) and strlen($params['element-size']) > 0) {
            $element->size = (int) $params['element-size'];
        }
        else {
            $element->size =  null;
        }

        // Size
        if (isset($params['element-size']) and strlen($params['element-size']) > 0) {
            $element->size = (int) $params['element-size'];
        }
        else {
            $element->size =  null;
        }

        // Link
        if (isset($params['element-link']) and strlen($params['element-link']) > 0) {
            $element->link = $params['element-link'];
        }
        else {
            $element->link =  null;
        }

        // Empty
        if (isset($params['element-empty']) and $params['element-empty'] === '1') {
            $element->empty = 1;
        }
        else {
            $element->empty = 0;
        }

        // Pending
        if (isset($params['element-pending']) and $params['element-pending'] === '1') {
            $element->pending = 1;
        }
        else {
            $element->pending = 0;
        }

        // Pending
        if (isset($params['element-deleted']) and $params['element-deleted'] === '1') {
            $element->deleted = 1;
        }
        else {
            $element->deleted = 0;
        }

        if ($element->parent == null) {
            return $element->save();
        }

        return $element->save() and static::updateElementParentEmpty($element, $oldParentId) and static::updateCourseURIs($element, $oldParentId);
    }

    private static function updateCourseURIs(Element $element, $oldParentId)
    {
        /*if ($element->parent === $oldParentId) {
            return true;
        }*/

        $root = $element->rootParentAll->id;
        if ($element->isCourse()) {
            $root = $element->id;
        }

        return ElementUriProcessor::id($root)->updateAll();
    }

    private static function updateElementParentEmpty(Element $element, $oldParentId)
    {
        if ($element->parent === $oldParentId) {
            return static::updateElementParentObject($element->parent);
        }

        return static::updateElementParentObject($element->parent) and static::updateElementParentObject($oldParentId);
    }

    private static function updateElementParentObject($id)
    {
        $oldParentObject = Element::fromIdAll($id, ['id', 'empty']);
        $oldParentObjectDirty = false;

        $oldParentChildren = ElementController::getVisibleChildren($id);
        if (count($oldParentChildren) === 0 and $oldParentObject->empty === 0) {
            $oldParentObject->empty = 1;
            $oldParentObjectDirty = true;
        }

        if (count($oldParentChildren) > 0 and $oldParentObject->empty === 1) {
            $oldParentObject->empty = 0;
            $oldParentObjectDirty = true;
        }

        if (!$oldParentObjectDirty) {
            return true;
        }

        return $oldParentObject->save();
    }
}
