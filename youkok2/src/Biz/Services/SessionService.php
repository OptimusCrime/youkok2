<?php
namespace Youkok\Biz\Services;

use Carbon\Carbon;

use Youkok\Biz\Exceptions\CookieNotFoundException;
use Youkok\Biz\Exceptions\SessionNotFoundException;
use Youkok\Common\Controllers\SessionController;
use Youkok\Enums\MostPopularCourse;
use Youkok\Enums\MostPopularElement;
use Youkok\Common\Models\Session;
use Youkok\Common\Utilities\CookieHelper;
use Youkok\Helpers\Utilities;

// TODO: Move attributes into Session itself
class SessionService
{
    const SESSION_TOKEN_LENGTH = 100;

    private $session;

    public function init(): void
    {
        // This is the default session data array


        $this->session = $this->loadSession();
    }

    private function loadSession(): Session
    {
        try {
            $hash = CookieHelper::getCookie('youkok2');
            return SessionController::load($hash);
        }
        catch (CookieNotFoundException $exception) {
            return $this->createSession();
        }
        catch (SessionNotFoundException $exception) {
            return $this->createSession();
        }
    }

    /**
     * @param $hash
     * @return array
     * @throws SessionNotFoundException
     */

    public function getSessionDataFromHash(string $hash): Session
    {
        return SessionController::load($hash);
    }

    public function getAllData(): array
    {
        return $this->session->data;
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
        return isset($this->session->data['admin']) and $this->session->data['admin'];
    }

    public function setData($key, $value): void
    {
        $this->data[$key] = $value;
    }

    public function store(): bool
    {
        $currentSession->data = json_encode($this->data);
        $currentSession->last_updated = Carbon::now();
        return $currentSession->save();
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
