<?php

namespace Youkok\Web\Views;

use Slim\Http\Response;
use Slim\Http\Request;


class KokebokaLegacyRedirect extends BaseView
{
    public function view(Request $request, Response $response): Response
    {
        $uri = $request->getAttribute('path', '');

        return $this->output(
            $response
                ->withStatus(301)
                ->withHeader(
                    'Location',
                    $this->router->pathFor(
                        'archive', [
                            'course' => $uri
                        ]
                    )
                )
        );
    }
}
