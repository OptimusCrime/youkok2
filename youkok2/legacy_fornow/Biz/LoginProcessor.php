<?php
namespace Youkok\Biz;

use \Psr\Http\Message\Response as Response;

use Youkok\Biz\Services\UserSessionService;

// TODO make middleware
class LoginProcessor
{
    private $params;
    private $sessionHandler;
    private $response;
    private $passwords;
    private $router;

    private function __construct(array $params)
    {
        $this->params = $params;
        return $this;
    }

    public static function fromParams(array $params)
    {
        return new LoginProcessor($params);
    }

    public function withSessionHandler(UserSessionService $sessionHandler)
    {
        $this->sessionHandler = $sessionHandler;
        return $this;
    }

    public function withResponse(Response $response)
    {
        $this->response = $response;
        return $this;
    }

    public function withPasswords(array $passwords)
    {
        $this->passwords = $passwords;
        return $this;
    }

    public function withRouter($router)
    {
        $this->router = $router;
        return $this;
    }

    public function run()
    {
        if (!static::verifyParams($this->params)) {
            return static::redirectToGoogle($this->response);
        }

        if (!static::loginIsCorrect($this->params, $this->passwords)) {
            return static::redirectToGoogle($this->response);
        }

        return static::logAdminIn($this->sessionHandler, $this->response, $this->router);
    }

    private static function logAdminIn(UserSessionService $sessionHandler, Response $response, $router)
    {
        $sessionHandler->setData('admin', true);

        return $response
            ->withStatus(302)
            ->withHeader('Location', $router->pathFor('admin_home'));
    }

    private static function loginIsCorrect(array $params, array $passwords)
    {
        for ($i = 1; $i <= 6; $i++) {
            $paramsKey = 'password' . $i;
            $passwordsKey = 'pass' . $i;
            if (!password_verify($params[$paramsKey], $passwords[$passwordsKey])) {
                return false;
            }
        }

        return true;
    }

    private static function verifyParams(array $params)
    {
        for ($i = 1; $i <= 6; $i++) {
            $key = 'password' . $i;
            if (!isset($params[$key])) {
                return false;
            }
        }

        return true;
    }

    private static function redirectToGoogle(Response $response)
    {
        return $response
            ->withStatus(302)
            ->withHeader('Location', 'https://www.google.com');
    }
}