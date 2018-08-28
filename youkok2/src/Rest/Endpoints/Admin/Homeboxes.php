<?php
namespace Youkok\Rest\Endpoints\Admin;

use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Admin\HomeboxProcessor;
use Youkok\Rest\Endpoints\BaseProcessorView;
use Youkok\Mappers\Admin\HomeboxMapper;

class Homeboxes extends BaseProcessorView
{
    public function view(Request $request, Response $response)
    {
        return $this->output($response, HomeboxMapper::map(HomeboxProcessor::run()));
    }
}
