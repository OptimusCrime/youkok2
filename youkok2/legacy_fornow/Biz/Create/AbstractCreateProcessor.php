<?php
namespace Youkok\Biz\Create;

use \Psr\Http\Message\Response as Response;
use \Psr\Http\Message\Request as Request;
use Youkok\Models\Element;

abstract class AbstractCreateProcessor
{
    protected $response;
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function withResponse(Response $response)
    {
        $this->response = $response;
        return $this;
    }

    protected static function parentIsValid($id)
    {
        return Element::where('id', $id)
                ->where('deleted', 0)
                ->where('pending', 0)
                ->where('directory', 1)
                ->get() !== null;
    }

    abstract public function run();
}
