<?php
namespace Youkok\Rest\Endpoints;

use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Mappers\AutocompleteMapper;
use Youkok\Biz\AutocompleteProcessor;

class Autocomplete extends BaseProcessorView
{
    public function view(Request $request, Response $response)
    {
        return $this->output($response, AutocompleteMapper::map(AutocompleteProcessor::run(), [
            'router' => $this->container->get('router')
        ]));
    }
}
