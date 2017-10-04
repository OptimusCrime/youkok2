<?php
namespace Youkok\Views;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Slim\Container;

use Youkok\Controllers\ElementController;
use Youkok\Helpers\Utilities;
use Youkok\Models\Contributor;
use Youkok\Models\Download;
use Youkok\Models\Element;
use Youkok\Processors\FrontpageFetchProcessor;
use Youkok\Processors\PopularCoursesProcessor;
use Youkok\Processors\PopularElementsProcessor;

class Frontpage extends BaseView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response)
    {
        return $this->render($response, 'frontpage.html', [
            'FRONTPAGE' => FrontpageFetchProcessor::fromSessionHandler($this->sessionHandler),
            'BODY_CLASS' => 'frontpage'
        ]);
    }
}
