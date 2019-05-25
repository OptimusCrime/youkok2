<?php
namespace Youkok\Web\Views;

use Slim\Http\Response;
use Slim\Http\Request;

class Frontpage extends BaseView
{
    public function view(Request $request, Response $response): Response
    {
        return $this->renderReactApp($response, 'frontpage.html', [
            'BODY_CLASS' => 'frontpage'
        ]);
    }
}
