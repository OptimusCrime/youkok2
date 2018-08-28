<?php
namespace Youkok\Web\TwigPlugins;

use Twig_Extension;
use Twig_SimpleFunction;

use Youkok\Common\Utilities\NumberFormatter;
use Youkok\Common\Utilities\TimeFormatter;

class YoukokTwigExtension extends Twig_Extension
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
            new Twig_SimpleFunction('element_url', [$this, 'elementUrl']),
            new Twig_SimpleFunction('youkok_url', [$this, 'youkokUrl']),
            new Twig_SimpleFunction('posted_at', [$this, 'postedAt']),
            new Twig_SimpleFunction('whole_number_format', [$this, 'wholeNumberFormat']),
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
            'course' => $element->id, // TODO
            'params' => $element->fullUri
        ]);
    }

    public function youkokUrl()
    {
        $scheme = getenv('SSL') === '1' ? 'https' : 'http';

        $uri = $this->request->getUri();
        return $scheme . '://' . $uri->getHost()
            . (in_array($uri->getPort(), [80, 443]) ? '' : (':' . $uri->getPort()));
    }
}
