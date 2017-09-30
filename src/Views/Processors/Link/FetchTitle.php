<?php
namespace Youkok\Views\Processors\Link;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Youkok\Mappers\ElementsMapper;
use Youkok\Mappers\TitleMapper;
use Youkok\Processors\FetchTitleProcessor;
use Youkok\Views\Processors\BaseProcessorView;

class FetchTitle extends BaseProcessorView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response, array $args)
    {
        $url = '';
        if (isset($request->getParams()['url'])) {
            $url = $request->getParams()['url'];
        }

        return $this->output($response, FetchTitleProcessor::fromUrl($url));
    }
}
