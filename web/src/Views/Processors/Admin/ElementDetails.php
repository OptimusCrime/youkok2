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
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(Request $request, Response $response, array $args)
    {
        $id = null;
        if (isset($args['id'])) {
            $id = $args['id'];
        }

        return $this->output($response, ElementDetailsProcessor
            ::fetch($id)
            ->withSettings($this->container->get('settings'))
            ->run()
        );
    }
}
