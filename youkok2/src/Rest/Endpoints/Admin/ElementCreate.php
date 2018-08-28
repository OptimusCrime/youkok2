<?php
namespace Youkok\Rest\Endpoints\Admin;

use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Admin\ElementCreateProcessor;
use Youkok\Rest\Endpoints\BaseProcessorView;

class ElementCreate extends BaseProcessorView
{
    const ELEMENT_CREATE_PARAMETER = 'directory-parent';

    public function run(Request $request, Response $response, array $args)
    {
        $parent = null;
        if (isset($request->getParams()[static::ELEMENT_CREATE_PARAMETER])) {
            $parent = $request->getParams()[static::ELEMENT_CREATE_PARAMETER];
        }

        return $this->output($response, ElementCreateProcessor
            ::fromParent($parent)
            ->withRequest($request)
            ->withRouter($this->container->get('router'))
            ->create()
        );
    }
}
