<?php
namespace Youkok\Web\Views;

use Slim\Http\Response;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouterInterface;
use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Common\Utilities\CoursesCacheConstants;
use Youkok\Helpers\Configuration\Configuration;

class BaseView
{
    protected function setSiteData(string $key, $value)
    {
        $this->templateData['SITE_DATA'][$key] = $value;
    }

    protected function render404(Response $response): Response
    {
        $templateDir = Configuration::getInstance()->getDirectoryTemplate();
        $template = @file_get_contents($templateDir . "404.html");

        if ($template === false) {
            throw new GenericYoukokException("Failed to get 404.html template file");
        }

        return $response
            ->write($template)
            ->withStatus(404);
    }
}
