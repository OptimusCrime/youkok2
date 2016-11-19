<?php
declare(strict_types=1);

namespace Youkok\Containers;

use \Slim\Container;
use \Slim\Views\Smarty as Smarty;
use \Slim\Views\SmartyPlugins as SmartyPlugins;

class View
{
    public static function load(Container $container)
    {
        $baseDir = $container->get('settings')['base_dir'];

        $container['view'] = function ($c) use ($baseDir) {
            $view = new Smarty($baseDir . '/templates', [
                'cacheDir' => $baseDir . '/smarty/cache',
                'compileDir' =>  $baseDir . '/smarty/compile',
                //'pluginsDir' => ['path/to/plugins', 'another/path/to/plugins']
            ]);

            // Add Slim specific plugins
            $smartyPlugins = new SmartyPlugins($c['router'], $c['request']->getUri());
            $view->registerPlugin('function', 'path_for', [$smartyPlugins, 'pathFor']);
            $view->registerPlugin('function', 'base_url', [$smartyPlugins, 'baseUrl']);

            return $view;
        };
    }
}
