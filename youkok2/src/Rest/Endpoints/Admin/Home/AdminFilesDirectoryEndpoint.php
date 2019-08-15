<?php
namespace Youkok\Rest\Endpoints\Admin\Home;

use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Youkok\Biz\Exceptions\CreateException;
use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Biz\Exceptions\InvalidRequestException;
use Youkok\Biz\Services\Admin\FileCreateDirectoryService;
use Youkok\Biz\Services\Admin\FileListingService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class AdminFilesDirectoryEndpoint extends BaseRestEndpoint
{
    /** @var FileCreateDirectoryService */
    private $adminFileCreateDirectoryService;

    /** @var Logger */
    private $logger;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->adminFileCreateDirectoryService = $container->get(FileCreateDirectoryService::class);
        $this->logger = $container->get(Logger::class);
    }

    public function get(Request $request, Response $response): Response
    {
        try {
            $data = $this->getJsonArrayFromBody(
                $request, [
                    'course',
                    'directory',
                    'value'
                ]
            );

            return $this->outputJson($response, [
                'data' => $this->adminFileCreateDirectoryService->createDirectory(
                    (int) $data['course'],
                    (int) $data['directory'],
                    (string) $data['value'],
                )[0]
            ]);
        }
        catch (InvalidRequestException $ex) {
            $this->logger->error($ex);
            return $this->returnBadRequest($response, $ex);
        }
        catch (CreateException $ex) {
            $this->logger->error($ex);
            return $this->returnBadRequest($response, $ex);
        }
        catch (GenericYoukokException $ex) {
            $this->logger->error($ex);
            return $this->returnBadRequest($response, $ex);
        }
    }
}
