<?php
namespace Youkok\Rest\Endpoints;

use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Exceptions\InvalidRequestException;
use Youkok\Biz\Services\FrontpageService;
use Youkok\Biz\Services\Mappers\CourseMapper;
use Youkok\Biz\Services\Mappers\ElementMapper;
use Youkok\Biz\Services\User\UserService;

class Frontpage extends BaseProcessorView
{
    /** @var FrontpageService */
    private $frontpageService;

    /** @var CourseMapper */
    private $courseMapper;

    /** @var ElementMapper */
    private $elementMapper;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->frontpageService = $container->get(FrontpageService::class);
        $this->courseMapper = $container->get(CourseMapper::class);
        $this->elementMapper = $container->get(ElementMapper::class);
    }

    public function get(Request $request, Response $response)
    {
        $payload = $this->frontpageService->get();

        $payload['latest_elements'] = $this->elementMapper->map(
            $payload['latest_elements'], [
                ElementMapper::POSTED_TIME,
                ElementMapper::PARENT_DIRECT,
                ElementMapper::PARENT_COURSE
            ]
        );

        $payload['last_downloaded'] = $this->elementMapper->mapStdClass(
            $payload['last_downloaded'], [
                ElementMapper::KEEP_DOWNLOADED_TIME,
                ElementMapper::PARENT_DIRECT,
                ElementMapper::PARENT_COURSE
            ]
        );

        $payload['courses_last_visited'] = $this->courseMapper->map(
            $payload['courses_last_visited'], [
                CourseMapper::LAST_VISITED
            ]
        );

        $payload['elements_most_popular'] = $this->mapElementsMostPopular($payload['elements_most_popular']);
        $payload['courses_most_popular'] = $this->mapCoursesMostPopular($payload['courses_most_popular']);

        return $this->output($response, $payload);
    }

    public function put(Request $request, Response $response)
    {
        $params = json_decode($request->getBody(), true);

        $delta = isset($params[FrontpageService::FRONTPAGE_PUT_DELTA_PARAM]) ? $params[FrontpageService::FRONTPAGE_PUT_DELTA_PARAM] : null;
        $value = isset($params[FrontpageService::FRONTPAGE_PUT_VALUE_PARAM]) ? $params[FrontpageService::FRONTPAGE_PUT_VALUE_PARAM] : null;

        try {
            $output = $this->frontpageService->put($delta, $value);

            return $this->output($response, $this->mapUpdateMostPopular($output, $delta, $value));
        }
        catch (InvalidRequestException $e) {
            // TODO log
            return $response->withStatus(400);
        }
    }

    private function mapElementsMostPopular($arr)
    {
        return $this->elementMapper->map(
            $arr, [
                ElementMapper::DATASTORE_DOWNLOADS,
                ElementMapper::PARENT_DIRECT,
                ElementMapper::PARENT_COURSE
            ]
        );
    }

    private function mapCoursesMostPopular($arr)
    {
        return $this->courseMapper->map(
            $arr, [
                CourseMapper::DATASTORE_DOWNLOAD_ESTIMATE
            ]
        );
    }

    private function mapUpdateMostPopular($output, $delta, $value)
    {
        $ret = [
            'delta' => $delta,
            'value' => $value,
        ];

        if ($delta === UserService::DELTA_POST_POPULAR_ELEMENTS) {
            $ret['data'] = $this->mapElementsMostPopular($output);
        }
        else {
            $ret['data'] = $this->mapCoursesMostPopular($output);
        }

        return $ret;
    }
}
