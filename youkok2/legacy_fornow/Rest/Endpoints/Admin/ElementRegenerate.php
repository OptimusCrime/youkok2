<?php
namespace Youkok\Rest\Endpoints\Admin;

use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Admin\ElementUriProcessor;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class ElementRegenerate extends BaseRestEndpoint
{
    public function uri(Request $request, Response $response, array $args)
    {
        $id = null;
        if (isset($request->getParams()['id'])) {
            $id = $request->getParams()['id'];
        }

        return $this->output($response, ElementUriProcessor
            ::id($id)
            ->update()
        );
    }
}
