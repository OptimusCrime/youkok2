<?php
namespace Youkok\Views\Processors\Admin;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Youkok\Processors\Admin\ElementCreateProcessor;
use Youkok\Processors\Admin\ElementDetailsProcessor;
use Youkok\Processors\Admin\HomeboxProcessor;
use Youkok\Views\Processors\BaseProcessorView;
use Youkok\Mappers\Admin\HomeboxMapper;

class ElementCreate extends BaseProcessorView
{
    const ELEMENT_CREATE_PARAMETER = 'directory-parent';

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
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
