<?php
namespace Youkok\Rest\Endpoints\Admin\Home;

use Exception;
use Monolog\Logger;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

use Youkok\Biz\Services\Models\CourseService;
use Youkok\Biz\Services\Models\DownloadService;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class AdminHomeBoxesEndpoint extends BaseRestEndpoint
{
    private CourseService $courseService;
    private DownloadService $downloadService;
    private ElementService $elementService;
    private Logger $logger;

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->courseService = $container->get(CourseService::class);
        $this->downloadService = $container->get(DownloadService::class);
        $this->elementService = $container->get(ElementService::class);
        $this->logger = $container->get('logger');
    }

    public function get(Request $request, Response $response): Response
    {
        try {
            return $this->outputJson($response, [
                'data' => [
                    'files_num' => $this->elementService->getNumberOfVisibleFiles(),
                    'downloads_num' => $this->downloadService->getNumberOfDownloads(),
                    'courses_num' => $this->courseService->getNumberOfVisibleCourses()
                ]
            ]);
        }
        catch (Exception $ex) {
            $this->logger->error($ex);

            return $this->returnInternalServerError($response);
        }
    }
}
