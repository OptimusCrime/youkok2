<?php
namespace Youkok\Rest\Endpoints\Admin\Home;

use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Services\Models\CourseService;
use Youkok\Biz\Services\Models\DownloadService;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class AdminHomeBoxesEndpoint extends BaseRestEndpoint
{
    private CourseService $courseService;
    private DownloadService $downloadService;
    private ElementService $elementService;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->courseService = $container->get(CourseService::class);
        $this->downloadService = $container->get(DownloadService::class);
        $this->elementService = $container->get(ElementService::class);
    }

    public function get(Request $request, Response $response): Response
    {
        return $this->outputJson($response, [
            'data' => [
                'files_num' => $this->elementService->getNumberOfVisibleFiles(),
                'downloads_num' => $this->downloadService->getNumberOfDownloads(),
                'courses_num' => $this->courseService->getNumberOfVisibleCourses()
            ]
        ]);
    }
}
