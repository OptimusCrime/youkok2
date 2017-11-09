<?php
namespace Youkok\Views\Processors\Admin;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Youkok\Processors\Admin\ElementListPendingProcessor;
use Youkok\Views\Processors\BaseProcessorView;

class ElementListPendingMarkup extends BaseProcessorView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
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
