<?php
namespace Youkok\Rest\Endpoints\Sidebar\Post\Create;

use Exception;
use Monolog\Logger;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

use Youkok\Biz\Exceptions\CreateException;
use Youkok\Biz\Services\Post\Create\CreateFileService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class CreateFileEndpoint extends BaseRestEndpoint
{
    const string FILE_ARRAY_KEY = 'file';

    private CreateFileService $createFileService;
    private Logger $logger;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->createFileService = $container->get(CreateFileService::class);
        $this->logger = $container->get('logger');
    }

    public function post(Request $request, Response $response, array $args): Response
    {
        try {
            if (!isset($args['id']) || !is_numeric($args['id'])) {
                throw new CreateException('Malformed id: ' . $args['id']);
            }

            $files = $request->getUploadedFiles();

            if (count($files) !== 1
                || !isset($files[static::FILE_ARRAY_KEY])
                || $files[static::FILE_ARRAY_KEY] === null
            ) {
                throw new CreateException('Did not get uploaded files');
            }

            $this->createFileService->run(
                (int) $args['id'],
                $files[static::FILE_ARRAY_KEY]
            );

            return $this->outputSuccess($response);
        } catch (Exception $ex) {
            $this->logger->error($ex);

            return $this->returnInternalServerError($response);
        }
    }
}
