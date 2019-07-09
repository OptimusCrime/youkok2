<?php
namespace Youkok\Rest\Endpoints\Sidebar\Post\Create;

use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Biz\Exceptions\InvalidRequestException;
use Youkok\Biz\Services\Post\Create\CreateLinkService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class CreateLinkEndpoint extends BaseRestEndpoint
{
    /** @var CreateLinkService */
    private $createLinkService;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->createLinkService = $container->get(CreateLinkService::class);
    }

    public function put(Request $request, Response $response)
    {
        try {
            $data = $this->getJsonArrayFromBody(
                $request, [
                    'url',
                    'title',
                    'id'
                ]
            );

            $this->createLinkService->run(
                $data['id'],
                $data['url'],
                $data['title']
            );

            return $this->outputSuccess($response);
        }
        catch (GenericYoukokException $ex) {
            return $this->returnBadRequest($response, $ex);
        }
    }
}
