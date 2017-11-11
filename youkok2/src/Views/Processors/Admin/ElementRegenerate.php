<?php
namespace Youkok\Views\Processors\Admin;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;


use Youkok\Processors\Admin\ElementUriProcessor;
use Youkok\Views\Processors\BaseProcessorView;

class ElementRegenerate extends BaseProcessorView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function uri(Request $request, Response $response, array $args)
    {
        $id = null;
        if (isset($request->getParams()['id'])) {
            $id = $request->getParams()['id'];
        }

        return $this->output($response, ElementUriProcessor
            ::id($id)
            ->update()
        );
    }
}
