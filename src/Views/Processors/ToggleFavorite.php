<?php
namespace Youkok\Views\Processors;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Youkok\Mappers\ElementsMapper;
use Youkok\Mappers\ToggleFavoriteMapper;
use Youkok\Processors\NewestElementsProcessor;
use Youkok\Processors\PopularCoursesProcessor;
use Youkok\Processors\ToggleFavoriteProcessor;

class ToggleFavorite extends BaseProcessorView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response, array $args)
    {
        $id = null;
        if (isset($request->getParams()['id'])) {
            $id = $request->getParams()['id'];
        }

        $type = null;
        if (isset($request->getParams()['type'])) {
            if ($request->getParams()['type'] === ToggleFavoriteProcessor::REMOVE) {
                $type = ToggleFavoriteProcessor::REMOVE;
            }
            else if ($request->getParams()['type'] === ToggleFavoriteProcessor::ADD) {
                $type = ToggleFavoriteProcessor::ADD;
            }
        }

        return $this->output($response, ToggleFavoriteMapper::map(ToggleFavoriteProcessor
            ::fromData($id, $type)
            ->withSessionHandler($this->sessionHandler)
            ->run()));
    }
}
