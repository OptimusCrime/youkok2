<?php
namespace Youkok\Processors\Create;

use \Psr\Http\Message\ServerRequestInterface as Request;
use Carbon\Carbon;

use Youkok\Models\Element;

class CreateLinkProcessor extends AbstractCreateProcessor
{
    const MINIMUM_NAME_LENGTH = 4;

    public static function fromRequest(Request $request)
    {
        return new CreateLinkProcessor($request);
    }

    public function run()
    {
        if (!static::isValidRequest($this->request)) {
            return [
                'code' => 500
            ];
        }

        if (!static::nameIsLongEnough($this->request)) {
            return [
                'code' => 401
            ];
        }

        return static::addNewElement($this->request);
    }

    private static function isValidRequest(Request $request)
    {
        $params = ['id', 'url', 'name'];
        foreach ($params as $param) {
            if (!isset($request->getParams()[$param])) {
                return false;
            }

            if (strlen($request->getParams()[$param]) === 0) {
                return false;
            }
        }

        if (!filter_var($request->getParams()['url'], FILTER_VALIDATE_URL)) {
            return false;
        }

        if (!is_numeric($request->getParams()['id'])) {
            return false;
        }

        return static::parentIsValid($request->getParams()['id']);
    }

    private static function nameIsLongEnough(Request $request)
    {
        return strlen($request->getParams()['name']) > static::MINIMUM_NAME_LENGTH;
    }

    private static function addNewElement(Request $request)
    {
        $element = new Element();
        $element->name = $request->getParams()['name'];
        $element->parent = $request->getParams()['id'];
        $element->pending = 1;
        $element->link = $request->getParams()['url'];
        $element->added = Carbon::now();
        $element->deleted = 0;
        $element->save();

        return [
            'code' => 200
        ];
    }
}
