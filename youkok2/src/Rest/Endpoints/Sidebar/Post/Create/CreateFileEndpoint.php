<?php
namespace Youkok\Rest\Endpoints\Sidebar\Post\Create;

use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Youkok\Biz\Exceptions\CreateException;
use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Biz\Services\Post\Create\CreateFileService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class CreateFileEndpoint extends BaseRestEndpoint
{
    const FILE_ARRAY_KEY = 'file';

    /** @var CreateFileService */
    private $createFileService;

    /** @var Logger */
    private $logger;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->createFileService = $container->get(CreateFileService::class);
        $this->logger = $container->get(Logger::class);
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
        } catch (CreateException | GenericYoukokException $ex) {
            $this->logger->error($ex);
            return $this->returnBadRequest($response, $ex);
        }
    }
}