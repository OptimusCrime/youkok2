<?php
namespace Youkok\Rest\Endpoints;

use Exception;
use Monolog\Logger;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

use Slim\Routing\RouteContext;
use Youkok\Biz\Exceptions\InvalidRequestException;
use Youkok\Biz\Services\ArchiveService;
use Youkok\Biz\Services\CacheService;
use Youkok\Biz\Services\Mappers\ElementMapper;
use Youkok\Biz\Services\Models\CourseService;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\CacheKeyGenerator;
use Youkok\Helpers\Configuration\Configuration;

class ArchiveEndpoint extends BaseRestEndpoint
{
    private ArchiveService $archiveService;
    private CourseService $courseService;
    private CacheService $cacheService;
    private ElementMapper $elementMapper;
    private Logger $logger;

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->archiveService = $container->get(ArchiveService::class);
        $this->courseService = $container->get(CourseService::class);
        $this->cacheService = $container->get(CacheService::class);
        $this->elementMapper = $container->get(ElementMapper::class);
        $this->logger = $container->get('logger');
    }

    public function data(Request $request, Response $response): Response
    {
        try {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            $queryParams = $request->getQueryParams();

            $course = $queryParams['course'] ?? null;
            $path = urldecode($queryParams['path']) ?? null;

            if ($course === null || $path === null) {
                throw new InvalidRequestException('Malformed request. Course: ' . $course . ', path: ' . $path);
            }

            $uri = $course . '/' . $path;

            $element = $this->archiveService->getArchiveElementFromUri($uri);
            $course = $this->getArchiveCourse($element);
            $parents = $this->archiveService->getBreadcrumbsForElement($routeParser, $element);

            if ($element->isCourse()) {
                $this->courseService->updateLastVisited($routeParser, $element);
            } else {
                $this->courseService->updateLastVisited($routeParser, $element->getCourse());
            }

            // Flush cache
            $this->cacheService->delete(CacheKeyGenerator::keyForLastVisitedCoursesPayload());

            $configuration = Configuration::getInstance();

            return $this->outputJson($response, [
                'id' => $element->id,
                'empty' => $element->empty === 1,
                'parents' => $parents,
                'title' => $element->isCourse() ? $element->getCourseCode() : $element->name,
                'sub_title' => $element->isCourse() ? $element->getCourseName() : null,
                'valid_file_types' => $configuration->getFileUploadAllowedTypes(),
                'max_file_size_bytes' => $configuration->getFileUploadMaxSizeInBytes(),
                'requested_deletion' => $course->requested_deletion === 1,
                'html_title' => $this->archiveService->getSiteTitle($element),
                'html_description' => $this->archiveService->getSiteDescription($element),
            ]);
        } catch (InvalidRequestException $ex) {
            $this->logger->debug($ex);
            return $this->returnBadRequest($response);
        } catch (Exception $ex) {
            $this->logger->error($ex);
            return $this->returnInternalServerError($response);
        }
    }

    public function content(Request $request, Response $response): Response
    {
        try {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();

            $queryParams = $request->getQueryParams();
            $id = $queryParams['id'] ?? null;

            if (!is_numeric($id)) {
                throw new InvalidRequestException('Malformed id: ' . $id);
            }

            return $this->outputJson(
                $response,
                $this->elementMapper->map(
                    $routeParser,
                    $this->archiveService->get(intval($id)),
                    [
                        ElementMapper::DOWNLOADS,
                        ElementMapper::POSTED_TIME,
                        ElementMapper::DOWNLOADS,
                        ElementMapper::ICON
                    ]
                )
            );
        } catch (InvalidRequestException $ex) {
            $this->logger->error($ex);
            return $this->returnBadRequest($response);
        } catch (Exception $ex) {
            $this->logger->error($ex);
            return $this->returnInternalServerError($response);
        }
    }

    private function getArchiveCourse(Element $element): Element
    {
        if ($element->isCourse()) {
            return $element;
        }

        return $element->getCourse();
    }
}
