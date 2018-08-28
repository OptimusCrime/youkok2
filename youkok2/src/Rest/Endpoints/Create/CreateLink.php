<?php
namespace Youkok\Rest\Endpoints\Create;

use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Create\CreateLinkProcessor;

class CreateLink extends BaseCreateProcessorView
{
    public function view(Request $request, Response $response, array $args)
    {
        return $this->output($response, CreateLinkProcessor::fromRequest($request)
            ->withResponse($response)
            ->run());
    }
}
