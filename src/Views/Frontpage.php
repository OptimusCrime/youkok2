<?php
declare(strict_types=1);

namespace Youkok\Views;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

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
        $userCount = Utilities::numberFormat(Contributor::where('banned', 0)->count());
        $fileCount = Utilities::numberFormat(Element::where('directory', 0)
            ->where('deleted', 0)
            ->count());
        $downloadCount = Utilities::numberFormat(Download::count());

        return $this->render($response, 'frontpage.tpl', [
            'FRONTPAGE_INFO_USERS' => $userCount,
            'FRONTPAGE_INFO_FILES' => $fileCount,
            'FRONTPAGE_INFO_DOWNLOADS' => $downloadCount
        ]);
    }
}
