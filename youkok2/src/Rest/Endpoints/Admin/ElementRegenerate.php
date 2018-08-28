<?php
namespace Youkok\Rest\Endpoints\Admin;

use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Admin\ElementUriProcessor;
use Youkok\Rest\Endpoints\BaseProcessorView;

class ElementRegenerate extends BaseProcessorView
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
