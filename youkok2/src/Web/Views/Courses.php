<?php
namespace Youkok\Web\Views;

use Slim\Http\Response;
use Slim\Http\Request;

class Courses extends BaseView
{
    public function view(Request $request, Response $response)
    {
        return $this->renderReactApp($response, 'courses.html', [
            'HEADER_MENU' => 'courses',
            'VIEW_NAME' => 'courses',
            'BODY_CLASS' => 'courses',
            'SITE_TITLE' => 'Emner :: Youkok2.com',
            'SITE_DESCRIPTION' => 'Oversikt over alle emnene som ligger inne på Youkok2.com'
        ]);
    }
}
