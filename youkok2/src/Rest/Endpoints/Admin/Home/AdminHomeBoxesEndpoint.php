<?php
namespace Youkok\Rest\Endpoints\Admin\Home;

use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Services\Models\CourseService;
use Youkok\Biz\Services\Models\DownloadService;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Biz\Services\Models\SessionService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class AdminHomeBoxesEndpoint extends BaseRestEndpoint
{
    /** @var CourseService */
    private $courseService;

    /** @var DownloadService */
    private $downloadService;

    /** @var ElementService */
    private $elementService;

    /** @var SessionService */
    private $sessionService;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->courseService = $container->get(CourseService::class);
        $this->downloadService = $container->get(DownloadService::class);
        $this->elementService = $container->get(ElementService::class);
        $this->sessionService = $container->get(SessionService::class);
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
