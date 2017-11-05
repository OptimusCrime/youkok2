<?php
namespace Youkok\Views\Processors\Create;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Youkok\Processors\Create\UploadFileProcessor;
use Youkok\Views\Processors\BaseProcessorView;

class UploadFile extends BaseProcessorView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response, array $args)
    {
        return $this->output($response, UploadFileProcessor
            ::fromRequest($request)
            ->withSettings($this->container->get('settings'))
            ->withResponse($response)
            ->run());
    }
}
