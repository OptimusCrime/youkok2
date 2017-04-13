<?php
declare(strict_types=1);

namespace Youkok\Views;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Slim\Container;

use Youkok\Helpers\Utilities;
use Youkok\Models\Contributor;
use Youkok\Models\Download;
use Youkok\Models\Element;

class Frontpage extends BaseView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response, array $args): Response
    {
        $this->loadFrontpageInfo();

        return $this->render($response, 'frontpage.tpl', [
            'FRONTPAGE_LATEST_ELEMENTS' => $this->loadFrontpageLatest(),
            'FRONTPAGE_MOST_POPULAR_ELEMENTS' => $this->loadFrontpageMostPopularElements(),
            'FRONTPAGE_MOST_POPULAR_COURSES' => [],
            'FRONTPAGE_LATEST_VISITED' => $this->loadFrontpageLastVisited()
        ]);
    }

    private function loadFrontpageInfo()
    {
        $this->setTemplateData('FRONTPAGE_INFO_USERS', Utilities::numberFormat(Contributor::where('banned', 0)->count()));

        $this->setTemplateData('FRONTPAGE_INFO_FILES', Utilities::numberFormat(Element::where('directory', 0)
            ->where('deleted', 0)
            ->count())
        );

        $this->setTemplateData('FRONTPAGE_INFO_DOWNLOADS', Utilities::numberFormat(Download::count()));
    }

    private function loadFrontpageLatest()
    {
        return Element::select('id', 'name', 'slug', 'uri', 'link', 'empty', 'parent', 'added')
            ->where('directory', 0)
            ->where('pending', 0)
            ->where('deleted', 0)
            ->orderBy('added', 'DESC')
            ->orderBy('name', 'DESC')
            ->get();
    }

    private function loadFrontpageMostPopularElements()
    {
        $this->container->get('cache')->forever('frontpage_most_popular_elements', [1, 2, 3, 4]);
        $cache = $this->container->get('cache')->get('frontpage_most_popular_elements');
        //var_dump($cache);
        //die();

        return Element::select('id', 'name', 'slug', 'uri', 'link', 'empty', 'parent', 'added')
            ->where('directory', 0)
            ->where('pending', 0)
            ->where('deleted', 0)
            ->orderBy('added', 'DESC')
            ->orderBy('name', 'DESC')
            ->get();
    }

    private function loadFrontpageLastVisited()
    {
        return Element::select('id', 'name', 'slug', 'uri')
            ->where('parent', null)
            ->where('directory', 1)
            ->where('pending', 0)
            ->where('deleted', 0)
            ->orderBy('last_visited', 'DESC')
            ->get();
    }
}
