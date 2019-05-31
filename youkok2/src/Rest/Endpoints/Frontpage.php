<?php
namespace Youkok\Rest\Endpoints;

use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Exceptions\InvalidRequestException;
use Youkok\Biz\Services\FrontpageService;
use Youkok\Biz\Services\Mappers\CourseMapper;
use Youkok\Biz\Services\Mappers\ElementMapper;
use Youkok\Biz\Services\SessionService;
use Youkok\Common\Models\Session;

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

    public function boxes(Request $request, Response $response)
    {
        $payload = $this->frontpageService->boxes();

        return $this->outputJson($response, [
            'data' => $payload
        ]);
    }

    public function popularElements(Request $request, Response $response)
    {
        $session = $this->sessionService->getSession();
        $payload = $this->frontpageService->popularElements();

        return $this->outputJson($response, [
            'data' => $this->mapElementsMostPopular($payload),
            'preference' => $session->getMostPopularElement()
        ]);
    }

    public function popularCourses(Request $request, Response $response)
    {
        $session = $this->sessionService->getSession();
        $payload = $this->frontpageService->popularCourses();

        return $this->outputJson($response, [
            'data' => $this->mapCoursesMostPopular($payload),
            'preference' => $session->getMostPopularCourse()
        ]);
    }

    public function newest(Request $request, Response $response)
    {
        $payload = $this->frontpageService->newest();

        $data = $this->elementMapper->map(
            $payload, [
            ElementMapper::POSTED_TIME,
            ElementMapper::PARENT_DIRECT,
            ElementMapper::PARENT_COURSE
        ]);

        return $this->outputJson($response, [
            'data' => $data
        ]);
    }

    public function lastVisited(Request $request, Response $response)
    {
        $payload = $this->frontpageService->lastVisited();

        $data = $this->courseMapper->map(
            $payload, [
                CourseMapper::LAST_VISITED
            ]
        );

        return $this->outputJson($response, [
            'data' => $data
        ]);
    }

    public function lastDownloaded(Request $request, Response $response)
    {
        $payload = $this->frontpageService->lastDownloaded();

        $data = $this->elementMapper->mapStdClass(
            $payload, [
                ElementMapper::KEEP_DOWNLOADED_TIME,
                ElementMapper::PARENT_DIRECT,
                ElementMapper::PARENT_COURSE
            ]
        );



        return $this->outputJson($response, [
            'data' => $data
        ]);
    }

    public function put(Request $request, Response $response)
    {
        $params = json_decode($request->getBody(), true);

        $delta = $params[FrontpageService::FRONTPAGE_PUT_DELTA_PARAM] ?? null;
        $value = $params[FrontpageService::FRONTPAGE_PUT_VALUE_PARAM] ?? null;

        if (!is_string($delta) || !is_string($value)) {
            return $response->withStatus(400);
        }

        try {
            $output = $this->frontpageService->put($delta, $value);

            return $this->outputJson(
                $response,
                $this->mapUpdateMostPopular($output, $delta, $value)
            );
        }
        catch (InvalidRequestException $e) {
            // TODO log
            return $response->withStatus(400);
        }
    }

    private function mapElementsMostPopular($arr)
    {
        return $this->elementMapper->mapFromArray(
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

        if ($delta === Session::KEY_MOST_POPULAR_ELEMENT) {
            $ret['data'] = $this->mapElementsMostPopular($output);
        }
        else {
            $ret['data'] = $this->mapCoursesMostPopular($output);
        }

        return $ret;
    }
}
