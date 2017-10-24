<?php
namespace Youkok\Views\Processors;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
use Youkok\Mappers\Processors\ClearFrontpageBoxMapper;
use Youkok\Processors\Frontpage\ClearFrontpageBoxProcessor;

class ClearFrontpageBox extends BaseProcessorView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response)
    {
        return $this->output($response, ClearFrontpageBoxMapper::map(
            ClearFrontpageBoxProcessor::fromRequest($request)
            ->withSessionHandler($this->sessionHandler)
            ->run()
        ));
    }
}