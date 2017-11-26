<?php
namespace Youkok\Views\Admin;

use \Psr\Http\Message\ResponseInterface as Response;

use Youkok\Controllers\ElementController;
use Youkok\Views\BaseView;

class AdminBaseView extends BaseView
{

    protected function render(Response $response, $template, array $data = [])
    {
        $this->templateData['NUM_PENDING'] = count(ElementController::getAllPending());

        return parent::render($response, $template, $data);
    }
}
