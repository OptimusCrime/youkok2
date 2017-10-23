<?php
namespace Youkok\Processors\Create;

use Carbon\Carbon;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
use Youkok\Models\Element;

class CreateLinkProcessor
{
    const MINIMUM_NAME_LENGTH = 4;

    private $response;
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public static function fromRequest(Request $request)
    {
        return new CreateLinkProcessor($request);
    }

    public function withResponse(Response $response)
    {
        $this->response = $response;
        return $this;
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

        // TODO validate parent

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

    private static function parentIsValid($id)
    {
        return Element::where('id', $id)
            ->where('deleted', 0)
            ->where('pending',0)
            ->where('directory', 1)
            ->get() !== null;
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
        $element->save();

        return [
            'code' => 200
        ];
    }
}