<?php
namespace Youkok\Views\Admin;

use \Psr\Http\Message\ResponseInterface as Response;

use Youkok\Processors\Admin\PendingProcessor;
use Youkok\Views\BaseView;

class AdminBaseView extends BaseView
{

    protected function render(Response $response, $template, array $data = [])
    {
        $this->templateData['NUM_PENDING'] = PendingProcessor::run();

        return parent::render($response, $template, $data);
    }
}
