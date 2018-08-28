<?php
namespace Youkok\Rest\Endpoints;

use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Mappers\Processors\ClearFrontpageBoxMapper;
use Youkok\Biz\Frontpage\ClearFrontpageBoxProcessor;

class ClearFrontpageBox extends BaseProcessorView
{
    public function view(Request $request, Response $response)
    {
        return $this->output($response, ClearFrontpageBoxMapper::map(
            ClearFrontpageBoxProcessor::fromRequest($request)
            ->withSessionHandler($this->sessionService)
            ->run()
        ));
    }
}
