<?php
namespace Youkok\Rest\Endpoints\Admin;

use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Admin\ElementListPendingProcessor;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class ElementListPendingMarkup extends BaseRestEndpoint
{
    public function get(Request $request, Response $response, array $args)
    {
        $id = null;
        if (isset($args['id'])) {
            $id = $args['id'];
        }

        return $this->output($response, [
            'code' => 200,
            'html' => $this->fetch('admin/includes/files_markup_pending.html', [
                'PENDING_ELEMENTS' => ElementListPendingProcessor::fetchPendingForId($id)
            ])
        ]);
    }
}
