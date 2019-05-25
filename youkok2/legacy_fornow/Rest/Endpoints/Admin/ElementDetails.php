<?php
namespace Youkok\Rest\Endpoints\Admin;

use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Admin\ElementDetailsProcessor;
use Youkok\Rest\Endpoints\BaseProcessorView;

class ElementDetails extends BaseProcessorView
{
    const ELEMENT_UPDATE_PARAMETER = 'element-id';

    public function get(Request $request, Response $response, array $args)
    {
        $id = null;
        if (isset($args['id'])) {
            $id = $args['id'];
        }

        return $this->output($response, ElementDetailsProcessor
            ::id($id)
            ->withSettings($this->container->get('settings'))
            ->fetch()
        );
    }

    public function update(Request $request, Response $response)
    {
        $id = null;
        if (isset($request->getParams()[static::ELEMENT_UPDATE_PARAMETER])) {
            $id = $request->getParams()[static::ELEMENT_UPDATE_PARAMETER];
        }

        return $this->output($response, ElementDetailsProcessor
            ::id($id)
            ->withParams($request->getParams())
            ->withRouter($this->container->get('router'))
            ->withSettings($this->container->get('settings'))
            ->update()
        );
    }
}
