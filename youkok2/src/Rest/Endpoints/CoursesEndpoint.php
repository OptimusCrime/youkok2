<?php
namespace Youkok\Rest\Endpoints;

use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Exceptions\IdenticalLookupException;
use Youkok\Biz\Exceptions\YoukokException;
use Youkok\Biz\Services\CoursesLookupService;

class CoursesEndpoint extends BaseRestEndpoint
{
    private CoursesLookupService $coursesLookupService;
    private Logger $logger;

    public function __construct(ContainerInterface $container)
    {
        $this->coursesLookupService = $container->get(CoursesLookupService::class);
        $this->logger = $container->get(Logger::class);
    }

    public function post(Request $request, Response $response): Response
    {
        $checksum = $request->getQueryParam('checksum', null);

        try {
            return $this->outputJson(
                $response,
                $this->coursesLookupService->get(
                    $checksum
                )
            );
        } catch (IdenticalLookupException $ex) {
            // Content has not changed
            return $response->withStatus(304);
        } catch (YoukokException $ex) {
            $this->logger->error($ex);
            return $this->returnBadRequest($response, $ex);
        }
    }
}
