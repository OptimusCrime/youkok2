<?php
namespace Youkok\Views;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Youkok\Helpers\DownloadHelper;
use Youkok\Models\Element;
use Youkok\Processors\UpdateDownloadsProcessor;

class PageNotFound extends BaseView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response)
    {
        return $this->render404($response);
    }
}
