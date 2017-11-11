<?php
namespace Youkok\Views\Processors\Admin;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Youkok\Processors\Admin\ElementDetailsProcessor;
use Youkok\Processors\Admin\HomeboxProcessor;
use Youkok\Views\Processors\BaseProcessorView;
use Youkok\Mappers\Admin\HomeboxMapper;

class ElementDetails extends BaseProcessorView
{
    const ELEMENT_UPDATE_PARAMETER = 'element-id';

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
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

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
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
