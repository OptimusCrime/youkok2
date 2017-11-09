<?php
namespace Youkok\Views\Processors\Admin;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Youkok\Processors\Admin\ElementDetailsProcessor;
use Youkok\Processors\Admin\File\AdminFileFetchProcessor;
use Youkok\Processors\Admin\HomeboxProcessor;
use Youkok\Views\Processors\BaseProcessorView;
use Youkok\Mappers\Admin\HomeboxMapper;

class ElementListMarkup extends BaseProcessorView
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
            'html' => $this->fetch('admin/includes/files_markup.html', [
                'COURSE' => AdminFileFetchProcessor::fetchChildrenForId($id)
            ])
        ]);
    }
}
