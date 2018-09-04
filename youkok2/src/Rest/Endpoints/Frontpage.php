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

        $payload['latest_elements'] = $this->elementMapper->map($payload['latest_elements'], [ElementMapper::POSTED_TIME, ElementMapper::PARENT_DIRECT, ElementMapper::PARENT_COURSE]);
        $payload['courses_last_visited'] = $this->courseMapper->map($payload['courses_last_visited']);

        $payload['elements_most_popular'] = $payload['elements_most_popular'];
        $payload['courses_most_popular'] = $this->courseMapper->map($payload['courses_most_popular']);

        // $payload['user_favorites'] = $this->courseMapper->map($payload['user_favorites']); TODO, this needs to handle both elements and courses, additional mapper?
        $payload['user_last_visited_courses'] = $this->courseMapper->map($payload['user_last_visited_courses']);

        return $this->output($response, $payload);
    }

    public function put(Request $request, Response $response)
    {
        try {
            $requestPayload = $request->getParams();

            $this->frontpageService->resetFrontpageBox(
                isset($requestPayload[FrontpageService::FRONTPAGE_CHANGE_PARAM]) ? $requestPayload[FrontpageService::FRONTPAGE_CHANGE_PARAM] : null
            );

            return $this->outputEmpty($response);
        }
        catch (InvalidRequestException $e) {
            // TODO log
            return $response->withStatus(400);
        }
    }
}