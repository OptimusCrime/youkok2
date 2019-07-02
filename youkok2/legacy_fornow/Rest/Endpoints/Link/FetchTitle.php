<?php
namespace Youkok\Rest\Endpoints\Link;

use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\FetchTitleProcessor;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class FetchTitle extends BaseRestEndpoint
{
    public function view(Request $request, Response $response, array $args)
    {
        $url = '';
        if (isset($request->getParams()['url'])) {
            $url = $request->getParams()['url'];
        }

        return $this->output($response, FetchTitleProcessor::fromUrl($url));
    }
}
