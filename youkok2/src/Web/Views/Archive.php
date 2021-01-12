<?php
// TODO remove this file
namespace Youkok\Web\Views;

use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\ArchiveService;
use Youkok\Biz\Services\CacheService;
use Youkok\Biz\Services\Models\CourseService;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\CacheKeyGenerator;
use Youkok\Common\Utilities\FileTypesHelper;

class Archive extends BaseView
{
    /** @var ArchiveService */
    private $archiveService;

    /** @var CourseService */
    private $courseService;

    /** @var Logger */
    private $logger;

    /** @var CacheService */
    private $cacheService;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->archiveService = $container->get(ArchiveService::class);
        $this->courseService = $container->get(CourseService::class);
        $this->logger = $container->get(Logger::class);
        $this->cacheService = $container->get(CacheService::class);
    }

    public function view(Request $request, Response $response): Response
    {
        $course = $request->getAttribute('course');
        $path = $request->getAttribute('path', '');

        // Handle old legacy URLs
        if ($course === 'kokeboka') {
            return $this->output(
                $response
                    ->withStatus(301)
                    ->withHeader(
                        'Location',
                        $this->router->pathFor(
                            'archive', [
                                'course' => $path
                            ]
                        )
                    )
            );
        }

        $uri = $course . '/' . $path;

        try {
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

            $this->setSiteData('archive_id', $element->id);
            $this->setSiteData('archive_empty', $element->empty === 1);
            $this->setSiteData('archive_parents', $parents);
            $this->setSiteData('archive_title', $element->isCourse() ? $element->getCourseCode() : $element->name);
            $this->setSiteData('archive_sub_title', $element->isCourse() ? $element->getCourseName() : null);
            $this->setSiteData('archive_url_frontpage', $this->router->pathFor('home'));
            $this->setSiteData('archive_url_courses', $this->router->pathFor('courses'));
            $this->setSiteData('archive_url_terms', $this->router->pathFor('terms'));
            $this->setSiteData('archive_valid_file_types', FileTypesHelper::getValidFileTypes());
            $this->setSiteData('archive_max_file_size_bytes', (int) getenv('FILE_MAX_SIZE_IN_BYTES'));
            $this->setSiteData('archive_requested_deletion', $course->requested_deletion === 1);

            return $this->renderReactApp($response, 'archive.html', [
                'HEADER_MENU' => 'courses',
                'VIEW_NAME' => 'archive',
                'BODY_CLASS' => 'archive',
                'SITE_TITLE' => $this->archiveService->getSiteTitle($element),
                'SITE_DESCRIPTION' => $this->archiveService->getSiteDescription($element)
            ]);
        } catch (ElementNotFoundException $ex) {
            return $this->render404($response);
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
