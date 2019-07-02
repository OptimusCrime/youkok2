<?php
namespace Youkok\Web\Views;

use Slim\Interfaces\RouterInterface;
use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\ArchiveService;
use Youkok\Biz\Services\Models\CourseService;
use Youkok\Helpers\ElementHelper;

class Archive extends BaseView
{
    /** @var ArchiveService */
    private $archiveService;

    /** @var CourseService */
    private $courseService;

    /** @var RouterInterface */
    private $router;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->archiveService = $container->get(ArchiveService::class);
        $this->courseService = $container->get(CourseService::class);
        $this->router = $container->get('router');
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

            return $this->renderReactApp($response, 'archive.html', [
                'HEADER_MENU' => 'courses',
                'VIEW_NAME' => 'archive',
                'BODY_CLASS' => 'archive',
                'SITE_TITLE' => ElementHelper::siteTitleFor($element), // TODO
                'SITE_DESCRIPTION' => ElementHelper::siteDescriptionFor($element) // TODO
            ]);
        } catch (ElementNotFoundException $exception) {
            // TODO log
            return $this->render404($response);
        }
    }
}
