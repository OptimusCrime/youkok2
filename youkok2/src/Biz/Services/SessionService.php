<?php
namespace Youkok\Biz\Services;

use Carbon\Carbon;

use Youkok\Biz\Exceptions\CookieNotFoundException;
use Youkok\Biz\Exceptions\SessionNotFoundException;
use Youkok\Common\Controllers\SessionController;
use Youkok\Common\Models\Session;
use Youkok\Common\Utilities\CookieHelper;
use Youkok\Helpers\Utilities;

class SessionService
{
    const SESSION_TOKEN_LENGTH = 100;

    /** @var Session */
    private $session;

    public function __construct()
    {
        $this->session = $this->loadSession();
    }

    private function loadSession(): Session
    {
        try {
            $hash = CookieHelper::getCookie('youkok2');
            return SessionController::get($hash);
        }
        catch (CookieNotFoundException $exception) {
            return $this->createSession();
        }
        catch (SessionNotFoundException $exception) {
            return $this->createSession();
        }
    }

    public function getSession(): Session
    {
        return $this->session;
    }

    public function getData($key, $default = null)
    {
        if (!isset($this->session->data[$key])) {
            return $default;
        }

        return $this->session->data[$key];
    }

    public function isAdmin(): bool
    {
        return $this->session->isAdmin();
    }

    public function setData($key, $value): void
    {
        $this->data[$key] = $value;
    }

    public function store(): bool
    {
        $this->session->last_updated = Carbon::now();
        return $this->session->save();
    }

    public function deleteExpiredSessions()
    {
        return SessionController::deleteExpiredSessions();
    }

    private function createSession(): Session
    {
        $hash = Utilities::randomToken(self::SESSION_TOKEN_LENGTH);

        CookieHelper::setCookie('youkok2', $hash, SessionController::SESSION_LIFE_TIME);

        return SessionController::create($hash);
    }
}
