<?php
namespace Youkok\Biz\Services;

use Carbon\Carbon;

use Youkok\Biz\Exceptions\CookieNotFoundException;
use Youkok\Biz\Exceptions\SessionNotFoundException;
use Youkok\Biz\Services\Models\SessionService;
use Youkok\Common\Models\Session;
use Youkok\Common\Utilities\CookieHelper;
use Youkok\Helpers\Utilities;

class UserSessionService
{
    const SESSION_TOKEN_LENGTH = 100;

    /** @var Session */
    private $session;

    public function __construct()
    {
        $this->session = $this->loadSession();
    }

    private function loadSession(): ?Session
    {
        try {
            $hash = CookieHelper::getCookie('youkok2');
            return SessionService::get($hash);
        } catch (CookieNotFoundException $exception) {
            // There is no need for a session if the script is called from the command line
            if (php_sapi_name() === 'cli') {
                return null;
            }

            return $this->createSession();
        } catch (SessionNotFoundException $exception) {
            return $this->createSession();
        }
    }

    public function getSession(): Session
    {
        return $this->session;
    }

    public function isAdmin(): bool
    {
        return $this->session->isAdmin();
    }

    public function store(): bool
    {
        $this->session->last_updated = Carbon::now();
        return $this->session->save();
    }

    public function deleteExpiredSessions()
    {
        return UserSessionService::deleteExpiredSessions();
    }

    private function createSession(): Session
    {
        $hash = Utilities::randomToken(self::SESSION_TOKEN_LENGTH);

        CookieHelper::setCookie('youkok2', $hash, SessionService::SESSION_LIFE_TIME);

        return SessionService::create($hash);
    }
}