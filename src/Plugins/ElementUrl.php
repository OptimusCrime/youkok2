<?php
declare(strict_types=1);

namespace Youkok\Plugins;

use Slim\Interfaces\RouterInterface;
use Smarty_Internal_Template;

use Youkok\Models\Element;

class ElementUrl
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function elementUrl($params, Smarty_Internal_Template $template)
    {
        $attributes = json_decode($params['element'], true);

        $element = new Element();
        $element->forceFill($attributes);

        if ($element->link !== null) {
            return $this->router->pathFor('redirect', [
                'id' => $element->id
            ]);
        }

        if ($element->checksum !== null) {
            return $this->router->pathFor('download', [
                'id' => $element->id
            ]);
        }

        return $this->router->pathFor('archive', [
            'params' => $element->fullUri
        ]);
    }
}
