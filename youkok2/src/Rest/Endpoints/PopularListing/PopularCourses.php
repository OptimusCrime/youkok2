<?php
namespace Youkok\Rest\Endpoints\PopularListing;

use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Enums\MostPopularCourse;
use Youkok\Mappers\ElementsMapper;
use Youkok\Mappers\MostPopularCoursesMapper;
use Youkok\Biz\Services\FrontpageService;
use Youkok\Biz\Services\PopularListing\PopularCoursesService;
use Youkok\Biz\UpdateUserMostPopularProcessor;
use Youkok\Rest\Endpoints\BaseProcessorView;

class PopularCourses extends BaseProcessorView
{
    const DELTA_POST_KEY = 'delta';

    public function get(Request $request, Response $response, array $args)
    {
        $output = ElementsMapper::map(
            PopularCoursesService::fromDelta(
                $args['delta'],
                $this->container->get('cache'),
                FrontpageService::PROCESSORS_LIMIT,
                $this->container->get('settings')
            ),
            [
                'router' => $this->container->get('router')
            ]
        );
        return $this->output($response, $output);
    }

    public function update(Request $request, Response $response)
    {
        $delta = null;
        if (isset($request->getParams()[static::DELTA_POST_KEY])
            and strlen($request->getParams()[static::DELTA_POST_KEY]) > 0) {
            $delta = $request->getParams()[static::DELTA_POST_KEY];
        }

        UpdateUserMostPopularProcessor
            ::fromSessionHandler($this->sessionService)
            ->withDelta($delta)
            ->withKey('frontpage.most_popular_course')
            ->withEnums(MostPopularCourse::all())
            ->run();

        $output = MostPopularCoursesMapper::map(
            PopularCoursesService::fromDelta(
                $delta,
                $this->container->get('cache'),
                FrontpageService::PROCESSORS_LIMIT,
                $this->container->get('settings')
            ),
            [
                'router' => $this->container->get('router')
            ]
        );

        return $this->output($response, $output);
    }
}
