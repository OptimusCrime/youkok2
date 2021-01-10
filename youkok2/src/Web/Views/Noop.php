<?php
namespace Youkok\Web\Views;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class Noop
 * @package Youkok\Web\Views
 *
 * This class is only used to create dynamic URLs using the router component instead of having to type them out
 * manually all the time.
 */
class Noop
{
    public function view(Request $request, Response $response): Response
    {
        return $response->withStatus(500);
    }
}
