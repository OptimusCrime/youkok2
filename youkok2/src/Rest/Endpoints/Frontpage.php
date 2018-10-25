<?php
namespace Youkok\Rest\Endpoints;

use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Exceptions\InvalidRequestException;
use Youkok\Biz\Services\FrontpageService;
use Youkok\Biz\Services\Mappers\CourseMapper;
use Youkok\Biz\Services\Mappers\ElementMapper;

class Frontpage extends BaseProcessorView
{
    /** @var \Youkok\Biz\Services\FrontpageService */
    private $frontpageService;

    /** @var \Youkok\Biz\Services\Mappers\CourseMapper */
    private $courseMapper;

    /** @var \Youkok\Biz\Services\Mappers\ElementMapper */
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

        $payload['elements_most_popular'] = $this->elementMapper->map(
            $payload['elements_most_popular'], [
                ElementMapper::DATASTORE_DOWNLOADS,
                ElementMapper::PARENT_DIRECT,
                ElementMapper::PARENT_COURSE
            ]
        );

        $payload['courses_most_popular'] = $this->courseMapper->map(
            $payload['courses_most_popular'], [
                CourseMapper::DATASTORE_DOWNLOAD_ESTIMATE
            ]
        );

        return $this->output($response, $payload);
    }

    public function put(Request $request, Response $response)
    {
        try {
            $this->frontpageService->resetFrontpageBox($request->getParam(FrontpageService::FRONTPAGE_CHANGE_PARAM, null));

            return $this->outputEmpty($response);
        }
        catch (InvalidRequestException $e) {
            // TODO log
            return $response->withStatus(400);
        }
    }
}