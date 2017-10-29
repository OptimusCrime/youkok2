<?php
namespace Youkok\Views\Processors\Create;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Youkok\Processors\Create\CreateLinkProcessor;
use Youkok\Views\Processors\BaseProcessorView;

class CreateLink extends BaseProcessorView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response, array $args)
    {
        return $this->output($response, CreateLinkProcessor::fromRequest($request)
            ->withResponse($response)
            ->run());
    }
}
