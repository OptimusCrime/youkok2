<?php
namespace Youkok\Web\Views;

use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\ArchiveService;
use Youkok\Helpers\ElementHelper;

class Archive extends BaseView
{
    /** @var \Youkok\Biz\Services\ArchiveService */
    private $archiveService;

    /** \Slim\Interfaces\RouterInterface */
    private $router;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->archiveService = $container->get(ArchiveService::class);
        $this->router = $container->get('router');
    }

    public function view(Request $request, Response $response, array $args)
    {
        $course = $request->getAttribute('course');
        $params = $request->getAttribute('params', null);

        try {
            $element = $this->archiveService->getArchiveElementFromUri($course, $params);
            $parents = $this->archiveService->getBreadcrumbsForElement($element);

            $this->setSiteData('archive_id', $element->id);
            $this->setSiteData('archive_parents', $parents);
            $this->setSiteData('archive_title', $element->isCourse() ? $element->courseCode : $element->name);
            $this->setSiteData('archive_sub_title', $element->isCourse() ? $element->courseName : null);
            $this->setSiteData('archive_url_frontpage', $this->router->pathFor('home'));
            $this->setSiteData('archive_url_courses', $this->router->pathFor('courses'));

            return $this->renderReactApp($response, 'archive.html', [
                'HEADER_MENU' => 'courses',
                'VIEW_NAME' => 'archive',
                'BODY_CLASS' => 'archive',
                'SITE_TITLE' => ElementHelper::siteTitleFor($element), // TODO
                'SITE_DESCRIPTION' => ElementHelper::siteDescriptionFor($element) // TODO
            ]);
        }
        catch (ElementNotFoundException $exception) {
            // TODO log
            return $this->render404($response);
        }
    }
}