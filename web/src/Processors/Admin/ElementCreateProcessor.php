<?php
namespace Youkok\Processors\Admin;

use Carbon\Carbon;
use Youkok\Helpers\ElementHelper;
use Youkok\Models\Element;
use Youkok\Utilities\UriTranslator;

class ElementCreateProcessor
{
    const DIRECTORY_NAME_PARAMETER = 'directory-name';

    private $id;
    private $request;
    private $router;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function withRequest($request)
    {
        $this->request = $request;
        return $this;
    }

    public function withRouter($router)
    {
        $this->router = $router;
        return $this;
    }

    public static function fromParent($id)
    {
        return new ElementCreateProcessor($id);
    }

    public function create()
    {
        $parent = Element::fromIdAll($this->id);
        if ($parent === null) {
            return [
                'code' => 400
            ];
        }

        $element = static::createFromParent($parent, $this->request);

        if ($element === null or !static::updateParentIfEmpty($parent)) {
            return [
                'code' => 400
            ];
        }

        // Update the URI here, reusing some code for once
        $element->uri = ElementHelper::constructUri($element->id);
        if (!$element->save()) {
            return [
                'code' => 400
            ];
        }

        return [
            'code' => 200,
            'course' => $parent->rootParentAll->id,
            'action' => $this->router->pathFor(
                'admin_processor_element_list_markup_fetch', [
                    'id' => $parent->rootParentAll->id
                ]
            )
        ];
    }

    private static function updateParentIfEmpty(Element $parent)
    {
        if ($parent->empty === 0) {
            return true;
        }

        $parent->empty = 0;
        return $parent->save();
    }

    private static function createFromParent(Element $parent, $request)
    {
        if (!isset($request->getParams()[static::DIRECTORY_NAME_PARAMETER])) {
            return false;
        }

        $name = $request->getParams()[static::DIRECTORY_NAME_PARAMETER];
        if (strlen($name) === 0) {
            return false;
        }

        $element = new Element();
        $element->parent = $parent->id;
        $element->name = $name;
        $element->slug = UriTranslator::generate($name);
        $element->uri = '';
        $element->empty = 1;
        $element->directory = 1;
        $element->pending = 0;
        $element->deleted = 0;
        $element->added = Carbon::now();

        if ($element->save()) {
            return $element;
        }

        return null;
    }
}
