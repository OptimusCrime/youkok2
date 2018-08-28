<?php
namespace Youkok\Rest\Endpoints\Admin;

use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Admin\HomeGraphProcessor;
use Youkok\Rest\Endpoints\BaseProcessorView;

class HomeGraph extends BaseProcessorView
{
    public function view(Request $request, Response $response)
    {
        return $this->output($response, HomeGraphProcessor::run());
    }
}
