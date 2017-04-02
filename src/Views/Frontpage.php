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
    private $userCount;
    private $fileCount;
    private $downloadCount;

    private $latest;

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->userCount = 0;
        $this->fileCount = 0;
        $this->downloadCount = 0;

        $this->latest = [];
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response, array $args): Response
    {
        $this->loadFrontpageInfo();
        $this->loadFrontpageBoxes();

        return $this->render($response, 'frontpage.tpl', [
            'FRONTPAGE_INFO_USERS' => $this->userCount,
            'FRONTPAGE_INFO_FILES' => $this->fileCount,
            'FRONTPAGE_INFO_DOWNLOADS' => $this->downloadCount,

            'FRONTPAGE_LATEST' => $this->latest
        ]);
    }

    private function loadFrontpageInfo()
    {
        $this->userCount = Utilities::numberFormat(Contributor::where('banned', 0)->count());
        $this->fileCount = Utilities::numberFormat(Element::where('directory', 0)
            ->where('deleted', 0)
            ->count());
        $this->downloadCount = Utilities::numberFormat(Download::count());
    }

    private function loadFrontpageBoxes()
    {
        $this->loadFrontpageLatest();
    }

    private function loadFrontpageLatest()
    {

    }
}
