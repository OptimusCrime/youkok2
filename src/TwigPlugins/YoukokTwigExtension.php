<?php
namespace Youkok\TwigPlugins;

use Youkok\Utilities\NumberFormatter;
use Youkok\Utilities\TimeFormatter;

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
            new \Twig_SimpleFunction('youkok_url', array($this, 'youkokUrl')),
            new \Twig_SimpleFunction('posted_at', array($this, 'postedAt')),
            new \Twig_SimpleFunction('whole_number_format', array($this, 'wholeNumberFormat')),
        ];
    }

    public function wholeNumberFormat($number)
    {
        return NumberFormatter::format($number);
    }

    public function postedAt($dateTime)
    {
        return TimeFormatter::clean($dateTime);
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

        // TODO handle redirect or download better?!

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
