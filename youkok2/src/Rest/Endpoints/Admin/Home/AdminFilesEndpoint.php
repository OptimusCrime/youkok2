<?php
namespace Youkok\Rest\Endpoints\Admin\Home;

use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Biz\Exceptions\InvalidRequestException;
use Youkok\Biz\Exceptions\UpdateException;
use Youkok\Biz\Services\Admin\FileDetailsService;
use Youkok\Biz\Services\Admin\FileListingService;
use Youkok\Biz\Services\Admin\FileUpdateService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class AdminFilesEndpoint extends BaseRestEndpoint
{
    /** @var FileListingService */
    private $adminFileListingService;

    /** @var FileDetailsService */
    private $adminFileDetailsService;

    /** @var FileUpdateService */
    private $adminFileUpdateService;

    /** @var Logger */
    private $logger;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->adminFileListingService = $container->get(FileListingService::class);
        $this->adminFileDetailsService = $container->get(FileDetailsService::class);
        $this->adminFileUpdateService = $container->get(FileUpdateService::class);
        $this->logger = $container->get(Logger::class);
    }

    public function get(Request $request, Response $response, array $args): Response
    {
        return $this->outputJson($response, [
            'data' => $this->adminFileDetailsService->get((int) $args['id'])
        ]);
    }

    public function list(Request $request, Response $response): Response
    {
        return $this->outputJson($response, [
            'data' => $this->adminFileListingService->getAll()
        ]);
    }

    public function put(Request $request, Response $response, array $args): Response
    {
        try {
            $data = $this->getJsonArrayFromBody(
                $request,
                ['course']
            );

            // Remember to run the update service before fetching the updated information below
            $course = $this->adminFileUpdateService->put(
                (int) $data['course'],
                (int) $args['id'],
                $data
            );

            return $this->outputJson($response, [
                'data' => [
                    'element' => $this->adminFileDetailsService->get((int) $args['id']),
                    'course' => $course
                ]
            ]);
        }
        catch (InvalidRequestException | UpdateException | GenericYoukokException $ex) {
            $this->logger->error($ex);
            return $this->returnBadRequest($response, $ex);
        }
    }
}
