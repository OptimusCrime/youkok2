<?php
namespace Youkok\Views\Processors\PopularListing;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Youkok\Mappers\MostPopularElementsMapper;
use Youkok\Processors\NewestElementsProcessor;
use Youkok\Views\Processors\BaseProcessorView;

class NewestElements extends BaseProcessorView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response)
    {
        return $this->output($response, MostPopularElementsMapper::map(
            NewestElementsProcessor::run(),
            [
                'router' => $this->container->get('router')
            ]
        ));
    }
}
