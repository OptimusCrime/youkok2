<?php
namespace Youkok\Rest\Endpoints;

use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Mappers\HistoryMapper;
use Youkok\Biz\ArchiveHistoryProcessor;

// TODO: Legacy
class ArchiveHistory extends BaseProcessorView
{
    public function view(Request $request, Response $response, array $args)
    {
        return $this->output($response, HistoryMapper::map(ArchiveHistoryProcessor::run($args['id'])));
    }
}
