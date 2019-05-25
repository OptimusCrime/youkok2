<?php
namespace Youkok\Web\Views;

use Slim\Http\Response;
use Slim\Http\Request;

class Flat extends BaseView
{
    public function help(Request $request, Response $response): Response
    {
        return $this->render(
            $response,
            'flat/help.html', [
                'SITE_TITLE' => 'Hjelp',
                'HEADER_MENU' => 'help',
                'VIEW_NAME' => 'help',
                'BODY_CLASS' => 'flat',
                'SITE_DESCRIPTION' => 'Trenger du hjelp med Youkok2.com? Se her'
            ]
        );
    }

    public function about(Request $request, Response $response): Response
    {
        return $this->render(
            $response,
            'flat/about.html', [
                'SITE_TITLE' => 'Om Youkok2',
                'HEADER_MENU' => 'about',
                'VIEW_NAME' => 'about',
                'BODY_CLASS' => 'flat',
                'SITE_DESCRIPTION' => 'Om Youkok2.com'
            ]
        );
    }

    public function terms(Request $request, Response $response): Response
    {
        return $this->render(
            $response,
            'flat/terms.html', [
                'SITE_TITLE' => 'Retningslinjer',
                'HEADER_MENU' => '',
                'VIEW_NAME' => 'terms',
                'FILE_ENDINGS' => explode(',', getenv('FILE_ENDINGS')),
                'BODY_CLASS' => 'flat',
                'SITE_DESCRIPTION' => 'Retningslinjer for Youkok2.com'
            ]
        );
    }

    public function changelog(Request $request, Response $response): Response
    {
        return $this->render(
            $response->withHeader('Content-Type', 'text/plain; charset=utf-8'),
            'flat/changelog.txt', [
                'CONTENT' => file_get_contents(getenv('BASE_DIRECTORY') . 'CHANGELOG.md')
            ]
        );
    }
}
