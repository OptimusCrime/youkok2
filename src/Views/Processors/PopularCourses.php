<?php
namespace Youkok\Views\Processors;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Youkok\Mappers\ElementsMapper;
use Youkok\Processors\PopularCoursesProcessor;

class PopularCourses extends BaseProcessorView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response, array $args)
    {
        $output = ElementsMapper::map(PopularCoursesProcessor::fromDelta($args['delta']));
        return $this->output($response, $output);
    }
}
