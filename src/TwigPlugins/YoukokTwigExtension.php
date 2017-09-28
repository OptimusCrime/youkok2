<?php
namespace Youkok\TwigPlugins;

class YoukokTwigExtension extends \Twig_Extension
{
    private $router;
    private $request;

    public function __construct($router, $request)
    {
        $this->router = $router;
        $this->request = $request;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('element_url', array($this, 'elementUrl')),
            new \Twig_SimpleFunction('youkok_url', array($this, 'youkokUrl'))
        ];
    }

    public function elementUrl($element = null)
    {
        if ($element === null) {
            return '';
        }

        if ($element->link !== null) {
            return $this->router->pathFor('redirect', [
                'id' => $element->id
            ]);
        }

        if ($element->checksum !== null) {
            return $this->router->pathFor('download', [
                'params' => $element->fullUri
            ]);
        }

        return $this->router->pathFor('archive', [
            'params' => $element->fullUri
        ]);
    }

    public function youkokUrl()
    {
        $uri = $this->request->getUri();
        return $uri->getScheme() . '://' . $uri->getHost() . (in_array($uri->getPort(), [80, 443]) ? '' : (':' . $uri->getPort()));
    }
}
