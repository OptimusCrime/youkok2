<?php
namespace Youkok\Views\Processors;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Youkok\Mappers\AutocompleteMapper;
use Youkok\Processors\AutocompleteProcessor;

class Autocomplete extends BaseProcessorView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response)
    {
        return $this->output($response, AutocompleteMapper::map(AutocompleteProcessor::run(), [
            'router' => $this->container->get('router')
        ]));
    }
}
