<?php

namespace Youkok\Rest\Endpoints;

use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Exceptions\ElementNotFoundException;
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

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->archiveService = $container->get(ArchiveService::class);
        $this->courseService = $container->get(CourseService::class);
        $this->cacheService = $container->get(CacheService::class);
        $this->elementMapper = $container->get(ElementMapper::class);
        $this->logger = $container->get(Logger::class);
    }

    public function data(Request $request, Response $response): Response
    {
        try {
            $course = $request->getQueryParam('course', null);
            $path = urldecode($request->getQueryParam('path', null));

            if ($course === null || $path === null) {
                throw new InvalidRequestException('Malformed id: ' . $id);
            }

            $uri = $course . '/' . $path;

            $element = $this->archiveService->getArchiveElementFromUri($uri);
            $course = $this->getArchiveCourse($element);
            $parents = $this->archiveService->getBreadcrumbsForElement($element);

            if ($element->isCourse()) {
                $this->courseService->updateLastVisited($element);
            } else {
                $this->courseService->updateLastVisited($element->getCourse());
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
        } catch (ElementNotFoundException | InvalidRequestException $ex) {
            $this->logger->error($ex);

            return $this->returnBadRequest($response, $ex);
        }
    }

    public function content(Request $request, Response $response): Response
    {
        try {
            $id = $request->getQueryParam('id', null);
            if ($id === null || !is_numeric($id)) {
                throw new InvalidRequestException('Malformed id: ' . $id);
            }

            return $this->outputJson(
                $response,
                $this->elementMapper->map(
                    $this->archiveService->get(intval($id)),
                    [
                        ElementMapper::DOWNLOADS,
                        ElementMapper::POSTED_TIME,
                        ElementMapper::DOWNLOADS,
                        ElementMapper::ICON
                    ]
                )
            );
        } catch (ElementNotFoundException | InvalidRequestException $ex) {
            $this->logger->error($ex);

            return $this->returnBadRequest($response, $ex);
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
