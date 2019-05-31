<?php
namespace Youkok\Web\Views\Admin;

use Slim\Http\Response;

use Youkok\Common\Controllers\ElementController;
use Youkok\Web\Views\BaseView;

class AdminBaseView extends BaseView
{
    protected function render(Response $response, $template, array $data = [])
    {
        $this->templateData['NUM_PENDING'] = count(ElementController::getAllPending());

        return parent::render($response, $template, $data);
    }
}
