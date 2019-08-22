<?php
namespace Youkok\Web\TwigPlugins;

use Slim\Interfaces\RouterInterface;
use Slim\Http\Request;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class YoukokTwigExtension extends AbstractExtension
{
    private $router;
    private $request;

    public function __construct(RouterInterface $router, Request $request)
    {
        $this->router = $router;
        $this->request = $request;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('youkok_url', [$this, 'youkokUrl']),
        ];
    }

    public function youkokUrl(): string
    {
        $scheme = getenv('SSL') === '1' ? 'https' : 'http';

        $uri = $this->request->getUri();
        return $scheme . '://' . $uri->getHost()
            . (in_array($uri->getPort(), [80, 443]) ? '' : (':' . $uri->getPort()));
    }
}
