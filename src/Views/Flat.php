<?php
namespace Youkok\Views;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

class Flat extends BaseView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function help(Request $request, Response $response, array $args)
    {
        return $this->render($response, 'flat/help.html', [
            'SITE_TITLE' => 'Hjelp',
            'HEADER_MENU' => 'help',
            'VIEW_NAME' => 'help'
        ]);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function about(Request $request, Response $response, array $args)
    {
        return $this->render($response, 'flat/about.html', [
            'SITE_TITLE' => 'Om Youkok2',
            'HEADER_MENU' => 'about',
            'VIEW_NAME' => 'about'
        ]);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function terms(Request $request, Response $response, array $args)
    {
        return $this->render($response, 'flat/terms.html', [
            'SITE_TITLE' => 'Retningslinjer',
            'HEADER_MENU' => '',
            'VIEW_NAME' => 'terms',
            'FILE_ENDINGS' => $this->container->get('settings')['file_endings']
        ]);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function changelog(Request $request, Response $response, array $args)
    {
        $response = $response->withHeader('Content-Type', 'text/plain');

        return $this->render($response, 'flat/changelog.txt', [
            'CONTENT' => file_get_contents($this->container->get('settings')['base_dir'] . '/CHANGELOG.md')
        ]);
    }
}
