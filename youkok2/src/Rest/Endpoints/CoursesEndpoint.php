<?php
namespace Youkok\Rest\Endpoints;

use Exception;

use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Exceptions\CacheServiceException;
use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Biz\Exceptions\IdenticalLookupException;
use Youkok\Biz\Exceptions\InvalidRequestException;
use Youkok\Biz\Services\ArchiveService;
use Youkok\Biz\Services\CacheService;
use Youkok\Biz\Services\CoursesLookupService;
use Youkok\Biz\Services\Mappers\ElementMapper;

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
            return $this->output($response->withStatus(304));
        } catch (Exception $ex) {
            $this->logger->error($ex);

            return $this->returnBadRequest($response, $ex);
        }
    }
}
