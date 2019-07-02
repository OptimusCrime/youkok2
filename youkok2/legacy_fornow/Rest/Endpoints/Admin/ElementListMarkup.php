<?php
namespace Youkok\Rest\Endpoints\Admin;

use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Admin\ElementListProcessor;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class ElementListMarkup extends BaseRestEndpoint
{
    public function get(Request $request, Response $response, array $args)
    {
        $id = null;
        if (isset($args['id'])) {
            $id = $args['id'];
        }

        return $this->output($response, [
            'code' => 200,
            'html' => $this->fetch('admin/includes/files_markup.html', [
                'COURSE' => ElementListProcessor::fetchChildrenForId($id)
            ])
        ]);
    }
}
