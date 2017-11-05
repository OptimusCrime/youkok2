<?php
namespace Youkok\Views\Processors\Admin;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Youkok\Processors\Admin\HomeboxProcessor;
use Youkok\Views\Processors\BaseProcessorView;
use Youkok\Mappers\Admin\HomeboxMapper;

class Homeboxes extends BaseProcessorView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response)
    {
        return $this->output($response, HomeboxMapper::map(HomeboxProcessor::run()));
    }
}
