<?php
declare(strict_types=1);

namespace Youkok\Views;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

class Courses extends BaseView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response, array $args): Response
    {
        return $this->render($response, 'courses.tpl', [
            'HEADER_MENU' => 'courses',
            'VIEW_NAME' => 'courses'
        ]);
    }
}
