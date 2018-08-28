<?php
namespace Youkok\Rest\Endpoints;

use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Mappers\ToggleFavoriteMapper;
use Youkok\Biz\ToggleFavoriteProcessor;

class ToggleFavorite extends BaseProcessorView
{
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
            } elseif ($request->getParams()['type'] === ToggleFavoriteProcessor::ADD) {
                $type = ToggleFavoriteProcessor::ADD;
            }
        }

        return $this->output($response, ToggleFavoriteMapper::map(ToggleFavoriteProcessor
            ::fromData($id, $type)
            ->withSessionHandler($this->sessionService)
            ->run()));
    }
}
