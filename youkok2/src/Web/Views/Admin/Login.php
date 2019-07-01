<?php

namespace Youkok\Web\Views\Admin;

use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\Request;

use Slim\Interfaces\RouterInterface;
use Youkok\Biz\Exceptions\InvalidLoginAttemptException;
use Youkok\Biz\Services\Admin\LoginService;
use Youkok\Web\Views\BaseView;

class Login extends BaseView
{
    /** @var RouterInterface */
    private $router;

    /** @var LoginService */
    private $loginService;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->router = $container->get('router');
        $this->loginService = $container->get(LoginService::class);
    }

    public function view(Request $request, Response $response)
    {
        return $this->render($response, 'admin/login.html', [
            'SITE_TITLE' => 'Admin',
            'HEADER_MENU' => 'admin',
            'VIEW_NAME' => 'admin'
        ]);
    }

    public function post(Request $request, Response $response)
    {
        try {
            // The validate method throws exceptions for all invalid login attempts
            $this->loginService->validateLogin($request->getParams());

            $session = $this->sessionService->getSession();
            $session->setAdmin(true);

            return $this->output(
                $response
                    ->withStatus(302)
                    ->withHeader('Location', $this->router->pathFor('admin_home'))
            );
        } catch (InvalidLoginAttemptException $e) {
            return $this->output(
                $response
                    ->withStatus(302)
                    ->withHeader('Location', 'https://www.google.com')
            );
        }
    }
}
