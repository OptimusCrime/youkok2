<?php
namespace Youkok\Rest\Endpoints\Create;

use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Create\UploadFileProcessor;

class UploadFile extends BaseCreateProcessorView
{
    public function view(Request $request, Response $response, array $args)
    {
        return $this->output($response, UploadFileProcessor
            ::fromRequest($request)
            ->withSettings($this->container->get('settings'))
            ->withResponse($response)
            ->run());
    }
}
