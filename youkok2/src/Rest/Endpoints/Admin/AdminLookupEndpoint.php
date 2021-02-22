<?php
namespace Youkok\Rest\Endpoints\Admin;

use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Exceptions\YoukokException;
use Youkok\Biz\Services\CoursesLookupService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class AdminLookupEndpoint extends BaseRestEndpoint
{
    private CoursesLookupService $coursesLookupService;
    private Logger $logger;

    public function __construct(ContainerInterface $container)
    {
        $this->coursesLookupService = $container->get(CoursesLookupService::class);
        $this->logger = $container->get(Logger::class);
    }

    public function get(Request $request, Response $response): Response
    {
        try {
            return $this->outputJson($response, [
                'data' => $this->coursesLookupService->getCoursesToAdminLookup()
            ]);

        } catch (YoukokException $ex) {
            $this->logger->error($ex);
            return $this->returnBadRequest($response, $ex);
        }
    }
}
