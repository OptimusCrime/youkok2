<?php
namespace Youkok\Web\Views;

use Exception;
use Slim\Psr7\Response;
use Youkok\Helpers\Configuration\Configuration;

class BaseView
{
    /**
     * @throws Exception
     */
    protected function render404(Response $response): Response
    {
        $templateDir = Configuration::getInstance()->getDirectoryTemplate();
        $template = @file_get_contents($templateDir . "404.html");

        if ($template === false) {
            throw new Exception("Failed to get 404.html template file");
        }

        $response->getBody()->write($template);

        return $response
            ->withStatus(404);
    }
}
