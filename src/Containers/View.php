<?php
declare(strict_types=1);

namespace Youkok\Containers;

use \Slim\Container;
use \Slim\Views\Smarty;
use \Slim\Views\SmartyPlugins;

use Youkok\Plugins\ElementUrl;

class View
{
    public static function load(Container $container)
    {
        $baseDir = $container->get('settings')['base_dir'];

        $container['view'] = function ($container) use ($baseDir) {
            $view = new Smarty($baseDir . '/templates', [
                'cacheDir' => $baseDir . '/smarty/cache',
                'compileDir' =>  $baseDir . '/smarty/compile',
            ]);

            $view->getSmarty()->setLeftDelimiter('[[+');
            $view->getSmarty()->setRightDelimiter(']]');

            // TODO
            $view->getSmarty()->setCaching(false);
            $view->getSmarty()->setDebugging(true);
            //$view->getSmarty()->setCompileCheck(false);

            // Add Slim specific plugins
            $smartyPlugins = new SmartyPlugins($container->get('router'), $container->get('request')->getUri());
            $view->registerPlugin('function', 'path_for', [$smartyPlugins, 'pathFor']);
            $view->registerPlugin('function', 'base_url', [$smartyPlugins, 'baseUrl']);

            // Plugin to easier create links for Elements
            $elementUrlPlugin = new ElementUrl($container->get('router'));
            $view->registerPlugin('function', 'element_url', [$elementUrlPlugin, 'elementUrl']);

            return $view;
        };
    }
}
