<?php
namespace Youkok\Views\Processors;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Youkok\Mappers\ElementsMapper;
use Youkok\Processors\NewestElementsProcessor;
use Youkok\Processors\PopularCoursesProcessor;

class NewestElements extends BaseProcessorView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response, array $args)
    {
        return $this->output($response, ElementsMapper::map(NewestElementsProcessor::run()));
    }
}
