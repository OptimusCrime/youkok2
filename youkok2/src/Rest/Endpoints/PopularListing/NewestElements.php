<?php
namespace Youkok\Rest\Endpoints\PopularListing;

use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Mappers\MostPopularElementsMapper;
use Youkok\Biz\NewestElementsProcessor;
use Youkok\Rest\Endpoints\BaseProcessorView;

class NewestElements extends BaseProcessorView
{
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
