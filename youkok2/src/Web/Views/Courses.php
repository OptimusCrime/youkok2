<?php
namespace Youkok\Web\Views;

use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Services\CourseListService;

class Courses extends BaseView
{
    /** @var \Youkok\Biz\Services\CourseListService */
    private $courseListService;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->courseListService = $container->get(CourseListService::class);
    }

    public function view(Request $request, Response $response)
    {
        return $this->render($response, 'courses.html', [
            'SITE_TITLE' => 'Emner',
            'HEADER_MENU' => 'courses',
            'VIEW_NAME' => 'courses',
            'COURSES' => $this->courseListService->get(),
            'SITE_DESCRIPTION' => 'Oversikt over alle emnene som ligger inne på Youkok2.com'
        ]);
    }
}
