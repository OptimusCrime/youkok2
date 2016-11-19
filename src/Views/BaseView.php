<?php
declare(strict_types=1);

namespace Youkok\Views;

use \Psr\Http\Message\ResponseInterface as Response;
use \Slim\Container;

class BaseView
{
    protected $ci;
    protected $templateData;

    public function __construct(Container $ci)
    {
        $this->ci = $ci;

        $this->templateData = [
            'SITE_TITLE' => 'Den beste kokeboka på nettet',
            'HEADER_MENU' => 'home',
            'SITE_DATA' => json_encode([]),
            'SEARCH_QUERY' => '',
            'SITE_MESSAGES' => []
        ];
    }

    protected function render(Response $response, String $template, array $data): Response
    {
        return $this->ci->get('view')->render($response, $template, array_merge(
            $this->templateData,
            $data
        ));
    }
}
