<?php
namespace Youkok\Web\Views;

use Slim\Http\Response;

use Youkok\Biz\Exceptions\TemplateFileNotFoundException;
use Youkok\Helpers\Configuration\Configuration;

class BaseView
{
    /**
     * @param Response $response
     * @return Response
     * @throws TemplateFileNotFoundException
     */
    protected function render404(Response $response): Response
    {
        $templateDir = Configuration::getInstance()->getDirectoryTemplate();
        $template = @file_get_contents($templateDir . "404.html");

        if ($template === false) {
            throw new TemplateFileNotFoundException("Failed to get 404.html template file");
        }

        return $response
            ->write($template)
            ->withStatus(404);
    }
}
