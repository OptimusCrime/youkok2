<?php
namespace Youkok\Web\Views;

use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\ArchiveService;
use Youkok\Biz\Services\Models\CourseService;
use Youkok\Common\Utilities\FileTypesHelper;

class Archive extends BaseView
{
    /** @var ArchiveService */
    private $archiveService;

    /** @var CourseService */
    private $courseService;

    /** @var Logger */
    private $logger;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->archiveService = $container->get(ArchiveService::class);
        $this->courseService = $container->get(CourseService::class);
        $this->logger = $container->get(Logger::class);
    }

    public function view(Request $request, Response $response): Response
    {
         $uri = $request->getAttribute('course') . '/' . $request->getAttribute('path', '');

        try {
            $element = $this->archiveService->getArchiveElementFromUri($uri);
            $parents = $this->archiveService->getBreadcrumbsForElement($element);

            if ($element->isCourse()) {
                $this->courseService->updateLastVisible($element);
            }
            else {
                $this->courseService->updateLastVisible($element->getCourse());
            }

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

            return $this->renderReactApp($response, 'archive.html', [
                'HEADER_MENU' => 'courses',
                'VIEW_NAME' => 'archive',
                'BODY_CLASS' => 'archive',
                'SITE_TITLE' => $this->archiveService->getSiteTitle($element),
                'SITE_DESCRIPTION' => $this->archiveService->getSiteDescription($element)
            ]);
        } catch (ElementNotFoundException $ex) {
            $this->logger->error($ex);
            return $this->render404($response);
        }
    }
}
